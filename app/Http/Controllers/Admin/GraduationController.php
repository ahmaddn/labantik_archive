<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleGraduation;
use App\Models\GoogleGraduationLetter;
use App\Models\GoogleGraduationMapel;
use App\Models\GoogleMapel;
use App\Models\RefClass;
use App\Models\RefStudent;
use App\Models\ExpertiseConcentration;
use Illuminate\Http\Request;

class GraduationController extends Controller
{
    /**
     * Display a listing of graduations
     */
    public function index(Request $request)
    {
        $mapels = GoogleMapel::with(['class', 'expertise'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $graduations = GoogleGraduation::with(['user', 'mapels', 'letter'])
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalMapels = GoogleMapel::count();
        $totalGraduations = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
            $q->where('academic_level', 12);
        })->count();
        $totalUsers = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
            $q->where('academic_level', 12);
        })->distinct('user_id')->count('user_id');

        $classes   = RefClass::where('academic_level', 12)
            ->select(['id', 'name', 'expertise_concentration_id', 'academic_level'])
            ->get();
        $expertise = ExpertiseConcentration::select(['id', 'name'])->get();
        $letters   = GoogleGraduationLetter::orderBy('created_at', 'desc')->get();

        $allHaveLetter = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
            $q->where('academic_level', 12);
        })->whereNull('letter_id')->doesntExist();

        return view('admin.graduation.index', compact(
            'mapels',
            'graduations',
            'totalMapels',
            'totalGraduations',
            'totalUsers',
            'classes',
            'expertise',
            'letters',
            'allHaveLetter'
        ));
    }

    /**
     * Show form tambah kelulusan siswa
     */
    public function create()
    {
        $classes = RefClass::with('expertiseConcentration')
            ->where('academic_level', 12)
            ->orderBy('academic_level')
            ->orderBy('name')
            ->get();

        return view('admin.graduation.create', compact('classes'));
    }

    /**
     * Store kelulusan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:ref_students,id',
            'mapel_ids'  => 'required|array|min:1',
            'scores'     => 'nullable|array',
            'scores.*'   => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $student            = RefStudent::with(['academicYears.class'])->findOrFail($validated['student_id']);
            $latestAcademicYear = $student->academicYears->first();

            if (!$latestAcademicYear || $latestAcademicYear->class->academic_level != 12) {
                return back()->with('error', 'Student harus dari kelas 12 (academic_level = 12)');
            }

            \DB::beginTransaction();

            $graduation = GoogleGraduation::create([
                'user_id' => $validated['student_id'],
            ]);

            $scores = $validated['scores'] ?? [];

            foreach ($validated['mapel_ids'] as $mapelUuid) {
                GoogleGraduationMapel::create([
                    'graduation_id' => $graduation->uuid,
                    'mapel_id'      => $mapelUuid,
                    'score'         => isset($scores[$mapelUuid]) && $scores[$mapelUuid] !== ''
                        ? (float) $scores[$mapelUuid]
                        : null,
                ]);
            }

            \DB::commit();
            return redirect()->route('admin.graduation.index')->with('success', 'Data kelulusan berhasil disimpan!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show detail kelulusan
     */
    public function show($id)
    {
        $graduation = GoogleGraduation::with([
            'user',
            'mapels.mapel.class',
            'mapels.mapel.expertise',
        ])
            ->where('uuid', $id)
            ->firstOrFail();

        $latestAcademicYear = $graduation->user->academicYears->first();
        if (!$latestAcademicYear || $latestAcademicYear->class->academic_level != 12) {
            return redirect()->route('admin.graduation.index')
                ->with('error', 'Data kelulusan ini bukan dari kelas 12');
        }

        return view('admin.graduation.show', compact('graduation'));
    }

    /**
     * Delete kelulusan
     */
    public function destroy($id)
    {
        try {
            $graduation = GoogleGraduation::findOrFail($id);

            \DB::beginTransaction();
            GoogleGraduationMapel::where('graduation_id', $id)->delete();
            $graduation->delete();
            \DB::commit();

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Data kelulusan berhasil dihapus!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data kelulusan: ' . $e->getMessage());
        }
    }

    /**
     * API — daftar siswa berdasarkan kelas (digunakan oleh form create)
     */
    public function getStudentsByClass(Request $request)
    {
        $classId = $request->query('class_id');

        $class = RefClass::where('id', $classId)
            ->where('academic_level', 12)
            ->first();

        if (!$class) {
            return response()->json(['error' => 'Kelas harus level 12'], 400);
        }

        $students = RefStudent::whereHas('academicYears', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        })
            ->select('id', 'full_name', 'student_number')
            ->orderBy('full_name')
            ->get();

        return response()->json($students);
    }

    /**
     * API — daftar mapel berdasarkan kelas (digunakan oleh form create)
     */
    public function getMapelsByClass(Request $request)
    {
        try {
            $classId = $request->query('class_id');

            if (!$classId) {
                return response()->json([]);
            }

            $class = RefClass::where('id', $classId)
                ->where('academic_level', 12)
                ->first();

            if (!$class) {
                return response()->json(['error' => 'Kelas harus level 12'], 404);
            }

            $mapels = GoogleMapel::with(['expertise'])
                ->where('class_id', $classId)
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(fn($m) => [
                    'uuid'           => $m->uuid,
                    'name'           => $m->name,
                    'type'           => $m->type,
                    'expertise_name' => $m->expertise->name ?? 'Umum',
                ]);

            return response()->json($mapels);
        } catch (\Exception $e) {
            \Log::error('getMapelsByClass error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Apply template surat ke semua data kelulusan kelas 12
     * POST /admin/graduation/apply-template
     */
    public function applyTemplateToAll(Request $request)
    {
        try {
            $validated = $request->validate([
                'letter_id' => 'required|string|exists:google_graduation_letters,uuid',
            ]);

            $letter = GoogleGraduationLetter::where('uuid', $validated['letter_id'])->first();

            if (!$letter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template surat tidak ditemukan',
                ], 404);
            }

            $updatedCount = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })->update(['letter_id' => $validated['letter_id']]);

            return response()->json([
                'success'       => true,
                'message'       => "Template '{$letter->letter_number}' berhasil diterapkan ke {$updatedCount} data kelulusan!",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Apply Template To All - Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerapkan template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
