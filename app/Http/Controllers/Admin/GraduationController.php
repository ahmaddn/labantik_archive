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
     * Show form tambah transkrip nilai (full semester)
     */
    public function createTranscript()
    {
        $classes = RefClass::with('expertiseConcentration')
            ->where('academic_level', 12)
            ->orderBy('academic_level')
            ->orderBy('name')
            ->get();

        return view('admin.graduation.create-transcript', compact('classes'));
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan validasi: ' . implode(' ', $e->errors()));
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
            public function storeTranscript(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:ref_students,id',
            'mapel_ids'  => 'required|array|min:1',
            's1'         => 'nullable|array',
            's2'         => 'nullable|array',
            's3'         => 'nullable|array',
            's4'         => 'nullable|array',
            's5'         => 'nullable|array',
            's6'         => 'nullable|array',
            'nr'         => 'nullable|array',
            'na'         => 'nullable|array',
        ]);

        try {
            $student = RefStudent::with(['academicYears.class'])->findOrFail($validated['student_id']);
            \DB::beginTransaction();

            $graduation = GoogleGraduation::firstOrCreate(['user_id' => $student->id]);

            foreach ($validated['mapel_ids'] as $mapelUuid) {
                GoogleGraduationMapel::updateOrCreate(
                    ['graduation_id' => $graduation->uuid, 'mapel_id' => $mapelUuid],
                    [
                        'sem_1' => $validated['s1'][$mapelUuid] ?? null,
                        'sem_2' => $validated['s2'][$mapelUuid] ?? null,
                        'sem_3' => $validated['s3'][$mapelUuid] ?? null,
                        'sem_4' => $validated['s4'][$mapelUuid] ?? null,
                        'sem_5' => $validated['s5'][$mapelUuid] ?? null,
                        'sem_6' => $validated['s6'][$mapelUuid] ?? null,
                        'nr'    => $validated['nr'][$mapelUuid] ?? null,
                        'score' => $validated['na'][$mapelUuid] ?? null,
                    ]
                );
            }

            \DB::commit();
            return redirect()->route('admin.graduation.index')->with('success', 'Data transkrip nilai berhasil disimpan!');
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

    public function updateScore(Request $request)
    {
        try {
            $validated = $request->validate([
                'uuid'  => 'required|string',
                'sem_1' => 'nullable|numeric|min:0|max:100',
                'sem_2' => 'nullable|numeric|min:0|max:100',
                'sem_3' => 'nullable|numeric|min:0|max:100',
                'sem_4' => 'nullable|numeric|min:0|max:100',
                'sem_5' => 'nullable|numeric|min:0|max:100',
                'sem_6' => 'nullable|numeric|min:0|max:100',
                'nr'    => 'nullable|numeric|min:0|max:100',
                'score' => 'nullable|numeric|min:0|max:100',
            ], [
                'score.numeric' => 'Nilai harus berupa angka.',
                'score.min'     => 'Nilai minimal 0.',
                'score.max'     => 'Nilai maksimal 100.',
            ]);

            $graduationMapel = GoogleGraduationMapel::where('uuid', $validated['uuid'])->firstOrFail();

            $graduationMapel->update([
                'sem_1' => $request->sem_1 ?? null,
                'sem_2' => $request->sem_2 ?? null,
                'sem_3' => $request->sem_3 ?? null,
                'sem_4' => $request->sem_4 ?? null,
                'sem_5' => $request->sem_5 ?? null,
                'sem_6' => $request->sem_6 ?? null,
                'nr'    => $request->nr ?? null,
                'score' => $request->score ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil diperbarui.',
                'data'    => $graduationMapel,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data mapel kelulusan tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('updateScore error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nilai: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update nilai semua mapel sekaligus (AJAX)
     * POST /admin/graduation/score/update-bulk
     */
    public function updateScoreBulk(Request $request)
    {
        try {
            $validated = $request->validate([
                'scores'         => 'required|array|min:1',
                'scores.*.uuid'  => 'required|string',
                'scores.*.score' => 'nullable|numeric|min:0|max:100',
            ], [
                'scores.*.score.numeric' => 'Nilai harus berupa angka.',
                'scores.*.score.min'     => 'Nilai minimal 0.',
                'scores.*.score.max'     => 'Nilai maksimal 100.',
            ]);

            \DB::beginTransaction();

            $updatedCount = 0;
            foreach ($validated['scores'] as $item) {
                $rows = GoogleGraduationMapel::where('uuid', $item['uuid'])->update([
                    'score' => $item['score'] ?? null,
                ]);
                $updatedCount += $rows;
            }

            \DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => "Nilai berhasil diperbarui untuk {$updatedCount} mapel.",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('updateScoreBulk error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nilai: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate tokens for all students in class 12 who have a graduation record.
     */
    public function generateTokens()
    {
        try {
            $graduations = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })->get();

            $updatedCount = 0;
            \DB::beginTransaction();
            foreach ($graduations as $graduation) {
                // Generate a random 6 character uppercase alphanumeric token
                $token = strtoupper(\Illuminate\Support\Str::random(6));
                $graduation->update(['token' => $token]);
                $updatedCount++;
            }
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Token berhasil di-generate untuk {$updatedCount} siswa.",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('generateTokens error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal meng-generate token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export tokens to Excel
     */
    public function exportTokens()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GraduationTokensExport, 'data_token_kelulusan.xlsx');
    }
}
