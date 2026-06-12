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
        $statusFilter = $request->input('status');
        $classFilter = $request->input('class_id');
        $search = $request->input('search');

        // Get list of grade 12 classes for dropdown
        $classes = RefClass::where('academic_level', 12)->orderBy('name')->get();

        $query = GoogleGraduation::with(['user.academicYears.class'])
            ->whereHas('user.academicYears', function ($q) use ($classFilter) {
                $q->where('status', 'active');
                if ($classFilter) {
                    $q->where('class_id', $classFilter);
                }
            })
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            });

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('national_student_number', 'like', "%{$search}%")
                  ->orWhere('diploma_number', 'like', "%{$search}%");
            });
        }

        // Apply filter based on status
        if ($statusFilter === 'filled') {
            $query->whereHas('user', function ($q) {
                $q->whereNotNull('diploma_number')->where('diploma_number', '!=', '');
            });
        } elseif ($statusFilter === 'empty') {
            $query->whereHas('user', function ($q) {
                $q->whereNull('diploma_number')->orWhere('diploma_number', '');
            });
        }

        // Sort by student name to make pagination consistent
        $graduations = $query->join('ref_students', 'google_graduation.user_id', '=', 'ref_students.id')
            ->select('google_graduation.*')
            ->orderBy('ref_students.full_name', 'asc')
            ->paginate(50)
            ->withQueryString();

        return view('admin.graduation.ijazah.index', compact('graduations', 'statusFilter', 'classes', 'classFilter', 'search'));
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
            Excel::import(new IjazahImport, $request->file('file'));

            return redirect()->route('admin.graduation.ijazah.index')
                ->with('success', 'Import Nomor Ijazah berhasil dilakukan!');
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
