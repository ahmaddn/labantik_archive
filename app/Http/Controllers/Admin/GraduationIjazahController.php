<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleGraduation;
use App\Models\RefStudent;
use App\Models\RefClass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IjazahTemplateExport;
use App\Imports\IjazahImport;
use DB;

class GraduationIjazahController extends Controller
{
    /**
     * Display a listing of students with their ijazah numbers.
     */
    public function index(Request $request)
    {
        // Get list of grade 12 classes for dropdown
        $classes = RefClass::where('academic_level', 12)->orderBy('name')->get();

        $query = GoogleGraduation::with(['user.academicYears.class'])
            ->whereHas('user.academicYears', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            });

        $graduationsData = $query->join('ref_students', 'google_graduation.user_id', '=', 'ref_students.id')
            ->select('google_graduation.*', 'ref_students.full_name', 'ref_students.student_number', 'ref_students.national_student_number', 'ref_students.diploma_number', 'ref_students.id as student_id')
            ->orderBy('ref_students.full_name', 'asc')
            ->get();

        $allGraduationsData = $graduationsData->map(function ($graduation) {
            $arr = $graduation->toArray();
            
            $latestYear = $graduation->user->academicYears->first();
            $arr['class_id'] = $latestYear?->class_id;
            $arr['class_name'] = $latestYear?->class
                ? $latestYear->class->academic_level . ' ' . $latestYear->class->name
                : '-';
            
            return $arr;
        })->toArray();

        return view('admin.graduation.ijazah.index', compact('allGraduationsData', 'classes'));
    }

    /**
     * Update a single student's diploma number.
     */
    public function updateSingle(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:ref_students,id',
            'diploma_number' => 'nullable|string|max:255',
        ]);

        try {
            $student = RefStudent::findOrFail($validated['student_id']);
            $student->update([
                'diploma_number' => $validated['diploma_number']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor ijazah berhasil diperbarui.',
                'diploma_number' => $student->diploma_number
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nomor ijazah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple students' diploma numbers.
     */
    public function updateBulk(Request $request)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*.id' => 'required|exists:ref_students,id',
            'students.*.diploma_number' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            foreach ($validated['students'] as $item) {
                RefStudent::where('id', $item['id'])->update([
                    'diploma_number' => $item['diploma_number']
                ]);
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Nomor ijazah berhasil diperbarui untuk {$updatedCount} siswa.",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nomor ijazah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel template.
     */
    public function export()
    {
        return Excel::download(new IjazahTemplateExport, 'Template_Nomor_Ijazah.xlsx');
    }

    /**
     * Import diploma numbers from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ], [
            'file.required' => 'File Excel wajib diunggah',
            'file.mimes' => 'Format file harus xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 5MB'
        ]);

        try {
            $import = new IjazahImport;
            Excel::import($import, $request->file('file'));

            $message = "<strong>Import Nomor Ijazah berhasil dilakukan!</strong><br><br>" .
                       "• Total baris berisi nomor ijazah diproses: <strong>{$import->processedCount}</strong><br>" .
                       "• Berhasil diupdate ke database: <strong style='color: green;'>{$import->updatedCount}</strong>";

            if (count($import->failedRows) > 0) {
                $message .= "<br><br><strong style='color: red;'>Detail data di Excel yang TIDAK ditemukan/cocok di DB (Silakan cek NIS/NISN atau Nama siswa berikut):</strong><br>";
                $message .= "<ul style='max-height: 250px; overflow-y: auto; padding-left: 20px; margin-top: 5px;'>";
                foreach (array_slice($import->failedRows, 0, 50) as $failed) {
                    $message .= "<li>{$failed}</li>";
                }
                $message .= "</ul>";
                if (count($import->failedRows) > 50) {
                    $message .= "dan " . (count($import->failedRows) - 50) . " siswa lainnya.";
                }
            }

            return redirect()->route('admin.graduation.ijazah.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = '';
            foreach ($failures as $failure) {
                $errorMsg .= "Baris {$failure->row()}: " . implode(', ', $failure->errors()) . "<br>";
            }
            return redirect()->back()->with('error', "Gagal import karena validasi data:<br>$errorMsg");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
