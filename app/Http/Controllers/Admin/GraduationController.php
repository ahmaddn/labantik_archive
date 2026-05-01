<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoreExpertiseConcentration;
use App\Models\GoogleGraduation;
use App\Models\GoogleGraduationMapel;
use App\Models\GoogleMapel;
use App\Models\User;
use App\Models\RefClass;
use App\Models\RefStudent;
use App\Models\ExpertiseConcentration;
use App\Models\GoogleGraduationLetter;
use Illuminate\Http\Request;

class GraduationController extends Controller
{
    /**
     * Display a listing of graduations
     */
    public function index(Request $request)
    {
        // 1. Ambil data mapels (sudah ada di kode Anda)
        $mapels = GoogleMapel::with(['class', 'expertise'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // 2. AMBIL DATA KELULUSAN untuk tabel component - FILTER HANYA KELAS 12
        // Component akan handle pagination sendiri
        $graduations = GoogleGraduation::with(['user', 'mapels', 'letter'])
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Stats - HANYA KELAS 12
        $totalMapels = GoogleMapel::count();
        $totalGraduations = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
            $q->where('academic_level', 12);
        })->count();
        $totalUsers = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
            $q->where('academic_level', 12);
        })->distinct('user_id')->count('user_id');

        // 4. Data untuk dropdown - HANYA KELAS 12
        $classes = RefClass::where('academic_level', 12)->select(['id', 'name', 'expertise_concentration_id', 'academic_level'])->get();
        $expertise = ExpertiseConcentration::select(['id', 'name'])->get();

        $letters = GoogleGraduationLetter::orderBy('created_at', 'desc')->get();

        // 3. Tambahkan 'letters' ke compact():
        return view('admin.graduation.index', compact(
            'mapels',
            'graduations',
            'totalMapels',
            'totalGraduations',
            'totalUsers',
            'classes',
            'expertise',
            'letters'   // <-- tambahkan ini
        ));
    }
    public function create()
    {
        // HANYA TAMPILKAN KELAS 12
        $classes = RefClass::with('expertiseConcentration')
            ->where('academic_level', 12)
            ->orderBy('academic_level')
            ->orderBy('name')
            ->get();

        return view('admin.graduation.create', compact('classes'));
    }

    public function getStudentsByClass(Request $request)
    {
        $classId = $request->query('class_id');

        // VALIDASI BAHWA CLASS ADALAH KELAS 12
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

    public function getMapelsByClass(Request $request)
    {
        try {
            $classId = $request->query('class_id');

            if (!$classId) {
                return response()->json([]);
            }

            // Cek dulu apakah class_id yang dikirim valid dan HARUS KELAS 12
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:ref_students,id',
            'mapel_ids'  => 'required|array|min:1',
            'scores'     => 'nullable|array',
            'scores.*'   => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $student = RefStudent::with(['academicYears.class'])->findOrFail($validated['student_id']);
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
     * Show form untuk create mapel baru
     */
    public function createMapel()
    {
        // Tampilkan hanya expertise untuk pilihan
        // Kelas akan otomatis di-apply ke semua kelas 12 berdasarkan expertise
        $expertise = ExpertiseConcentration::select(['id', 'name'])
            ->get();

        return view('admin.graduation.create-mapel', compact('expertise'));
    }

    /**
     * Show form untuk edit mapel
     */
    public function editMapel($id)
    {
        $mapel = GoogleMapel::where('uuid', $id)->firstOrFail();
        $classes = RefClass::where('academic_level', 12)->get();
        $expertise = \App\Models\ExpertiseConcentration::all(); // sesuaikan model

        // Expertise yang sudah dipilih untuk mapel ini
        $selectedExpertiseIds = $mapel->expertise_id ? [$mapel->expertise_id] : [];

        return view('admin.graduation.edit-mapel', compact('mapel', 'classes', 'expertise', 'selectedExpertiseIds'));
    }

    /**
     * Update mapel ke database
     */
    public function updateMapel(Request $request, $id)
    {
        $mapel = GoogleMapel::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'class_id'      => 'required|exists:ref_classes,id',
            'expertise_ids' => 'nullable|array',
            'expertise_ids.*' => 'exists:core_expertise_concentrations,id',
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:umum,jurusan',
        ]);

        try {
            if ($validated['type'] === 'jurusan' && empty($validated['expertise_ids'])) {
                return redirect()->back()
                    ->with('error', 'Pilih minimal satu jurusan untuk tipe mapel jurusan')
                    ->withInput();
            }

            if ($validated['type'] === 'umum') {
                // Cek duplikat
                $exists = GoogleMapel::where('uuid', '!=', $id)
                    ->where('class_id', $validated['class_id'])
                    ->where('name', $validated['name'])
                    ->where('type', 'umum')
                    ->whereNull('expertise_id')
                    ->exists();

                if ($exists) {
                    return redirect()->back()
                        ->with('error', 'Mapel dengan nama dan kelas yang sama sudah ada')
                        ->withInput();
                }

                $mapel->update([
                    'class_id'     => $validated['class_id'],
                    'expertise_id' => null,
                    'name'         => $validated['name'],
                    'type'         => 'umum',
                ]);
            } else {
                // Tipe jurusan: update mapel ini untuk expertise pertama,
                // tambahkan mapel baru untuk expertise tambahan
                $expertiseIds = $validated['expertise_ids'];
                $firstExpertise = array_shift($expertiseIds);

                $mapel->update([
                    'class_id'     => $validated['class_id'],
                    'expertise_id' => $firstExpertise,
                    'name'         => $validated['name'],
                    'type'         => 'jurusan',
                ]);

                // Expertise tambahan → buat mapel baru jika belum ada
                foreach ($expertiseIds as $expId) {
                    $exists = GoogleMapel::where('class_id', $validated['class_id'])
                        ->where('name', $validated['name'])
                        ->where('type', 'jurusan')
                        ->where('expertise_id', $expId)
                        ->exists();

                    if (!$exists) {
                        GoogleMapel::create([
                            'class_id'     => $validated['class_id'],
                            'expertise_id' => $expId,
                            'name'         => $validated['name'],
                            'type'         => 'jurusan',
                        ]);
                    }
                }
            }

            return redirect()->route('admin.graduation.index')
                ->with('success', 'Mapel berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui mapel: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateMapelOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'uuid'  => 'required|string',
                'order' => 'required|integer|min:1|max:999',
                'join'  => 'required|integer|min:1|max:10',
            ], [
                'uuid.required'  => 'UUID mapel wajib diisi',
                'order.required' => 'Urutan wajib diisi',
                'order.integer'  => 'Urutan harus berupa angka',
                'order.min'      => 'Urutan minimal 1',
                'join.required'  => 'Join baris wajib diisi',
                'join.integer'   => 'Join baris harus berupa angka',
                'join.min'       => 'Join baris minimal 1',
                'join.max'       => 'Join baris maksimal 10',
            ]);

            $mapel = GoogleMapel::where('uuid', $validated['uuid'])->firstOrFail();

            $mapel->update([
                'order' => $validated['order'],
                'join'  => $validated['join'],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Urutan & join mapel '{$mapel->name}' berhasil diperbarui.",
                'data'    => [
                    'uuid'  => $mapel->uuid,
                    'order' => $mapel->order,
                    'join'  => $mapel->join,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('updateMapelOrder error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Delete mapel dari database
     */
    public function destroyMapel($id)
    {
        try {
            $mapel = GoogleMapel::where('uuid', $id)->firstOrFail();

            \DB::beginTransaction();

            // Hapus data mapel yang terkait di GoogleGraduationMapel
            GoogleGraduationMapel::where('mapel_id', $id)->delete();

            // Hapus mapel
            $mapel->delete();

            \DB::commit();

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Mapel berhasil dihapus!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus mapel: ' . $e->getMessage());
        }
    }

    /**
     * Store mapel baru ke database - auto-apply ke semua kelas 12
     */
    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'expertise_ids'   => 'nullable|array',
            'expertise_ids.*' => 'exists:core_expertise_concentrations,id',
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:umum,jurusan',
        ]);

        try {
            $allClasses = RefClass::where('academic_level', 12)->get();

            if ($allClasses->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada kelas level 12 di database')
                    ->withInput();
            }

            $successCount = 0;
            $skipCount = 0;

            if ($validated['type'] === 'umum') {
                foreach ($allClasses as $class) {
                    $exists = GoogleMapel::where('class_id', $class->id)
                        ->where('name', $validated['name'])
                        ->where('type', 'umum')
                        ->whereNull('expertise_id')
                        ->exists();

                    if ($exists) {
                        $skipCount++;
                        continue;
                    }

                    GoogleMapel::create([
                        'class_id'     => $class->id,
                        'expertise_id' => null,
                        'name'         => $validated['name'],
                        'type'         => 'umum',
                    ]);
                    $successCount++;
                }
            } else {
                if (empty($validated['expertise_ids'])) {
                    return redirect()->back()
                        ->with('error', 'Pilih minimal satu jurusan untuk tipe mapel jurusan')
                        ->withInput();
                }

                foreach ($validated['expertise_ids'] as $expId) {
                    $matchedClasses = $allClasses->where('expertise_concentration_id', $expId);

                    foreach ($matchedClasses as $class) {
                        $exists = GoogleMapel::where('class_id', $class->id)
                            ->where('name', $validated['name'])
                            ->where('type', 'jurusan')
                            ->where('expertise_id', $expId)
                            ->exists();

                        if ($exists) {
                            $skipCount++;
                            continue;
                        }

                        GoogleMapel::create([
                            'class_id'     => $class->id,
                            'expertise_id' => $expId,
                            'name'         => $validated['name'],
                            'type'         => 'jurusan',
                        ]);
                        $successCount++;
                    }
                }
            }

            $message = "Mapel '{$validated['name']}' berhasil ditambahkan ke $successCount kelas!";
            if ($skipCount > 0) $message .= " ($skipCount mapel sudah ada)";

            return redirect()->route('admin.graduation.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan mapel: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template Excel untuk input mapel kelulusan
     */
    public function downloadTemplate(Request $request)
    {
        $classId = $request->query('class_id');

        $students = RefStudent::query()
            ->when($classId, function ($q) use ($classId) {
                $q->whereHas('academicYears', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            })
            // HANYA SISWA DARI KELAS 12
            ->whereHas('academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })
            ->with([
                'user',
                'academicYears' => function ($q) use ($classId) {
                    $q->when($classId, fn($q) => $q->where('class_id', $classId))
                        ->with('class')
                        ->latest();
                }
            ])
            ->orderBy('full_name', 'asc')
            ->get()
            ->sortBy(fn($student) => $student->academicYears->first()?->class?->academic_level);

        // Load semua mapel, nanti di-match per siswa
        $mapels = GoogleMapel::query()
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->get()
            ->groupBy('class_id'); // Group by class_id untuk lookup cepat

        $fileName = 'Template_Kelulusan_' . now()->format('d-m-Y_His') . '.csv';

        $fp = fopen('php://memory', 'w');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $headers = ['No', 'NIS', 'NISN', 'Nama Siswa', 'Kelas', 'Tapel (Tahun Pelajaran)', 'Id Mapel', 'Nama Mapel', 'Nilai'];
        fputcsv($fp, $headers, ';');

        $no = 1;
        foreach ($students->values() as $student) {
            $latestAcademicYear = $student->academicYears->first();
            $studentClassId = $latestAcademicYear?->class_id;

            // Ambil mapel yang sesuai dengan class_id siswa
            $studentMapels = $studentClassId
                ? ($mapels->get($studentClassId) ?? collect())
                : collect();

            if ($studentMapels->isEmpty()) {
                $row = [
                    $no++,
                    $student->student_number ?? '',
                    $student->national_student_number ?? '',
                    $student->full_name,
                    ($latestAcademicYear?->class?->academic_level ?? '') . ' ' . ($latestAcademicYear?->class?->name ?? ''),
                    $latestAcademicYear?->academic_year ?? '',
                    '',
                    '',
                    '',
                ];
                fputcsv($fp, $row, ';');
                continue;
            }

            foreach ($studentMapels as $mapel) {
                $row = [
                    $no++,
                    $student->student_number ?? '',
                    $student->national_student_number ?? '',
                    $student->full_name,
                    ($latestAcademicYear?->class?->academic_level ?? '') . ' ' . ($latestAcademicYear?->class?->name ?? ''),
                    $latestAcademicYear?->academic_year ?? '',
                    $mapel->uuid,
                    $mapel->name,
                    '',
                ];
                fputcsv($fp, $row, ';');
            }
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    /**
     * Show form untuk import mapel
     */
    public function showImportMapel()
    {
        // HANYA TAMPILKAN KELAS 12
        $classes = RefClass::where('academic_level', 12)->select(['id', 'name', 'expertise_concentration_id', 'academic_level'])
            ->get();

        $expertise = ExpertiseConcentration::select(['id', 'name'])
            ->get();

        return view('admin.graduation.import-mapel', compact('classes', 'expertise'));
    }

    /**
     * Process import mapel dari file
     */
    public function importMapel(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'required|string|exists:ref_classes,id',
            'expertise_ids' => 'required|array|min:1',
            'expertise_ids.*' => 'required|string|exists:core_expertise_concentrations,id',
        ], [
            'file.required' => 'File harus diupload',
            'file.file' => 'File harus berupa file',
            'file.mimes' => 'File harus berformat CSV, XLSX, atau XLS',
            'file.max' => 'Ukuran file maksimal 10MB',
            'class_ids.required' => 'Pilih minimal satu kelas',
            'class_ids.min' => 'Pilih minimal satu kelas',
            'expertise_ids.required' => 'Pilih minimal satu jurusan',
            'expertise_ids.min' => 'Pilih minimal satu jurusan',
        ]);

        try {
            $file = $request->file('file');

            $classIds = array_values(array_unique(array_filter(
                array_map('strval', $validated['class_ids']),
                fn($id) => !empty(trim($id))
            )));

            $expertiseIds = array_values(array_unique(array_filter(
                array_map('strval', $validated['expertise_ids']),
                fn($id) => !empty(trim($id))
            )));

            \Log::info('Import Mapel - Start', [
                'classIds' => $classIds,
                'expertiseIds' => $expertiseIds,
            ]);

            // Parse file menggunakan Maatwebsite (support CSV, XLSX, XLS)
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0] ?? [];

            if (empty($rows)) {
                throw new \Exception('File kosong atau format tidak valid');
            }

            // Normalize headers - remove BOM, trim, lowercase
            $headers = array_map(
                fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h))),
                $rows[0]
            );

            \Log::info('Import Mapel - Headers', ['headers' => $headers]);

            // Find columns dynamically
            $nameCol = collect($headers)->search(
                fn($h) => str_contains($h, 'name') || str_contains($h, 'nama')
            );
            $typeCol = collect($headers)->search(
                fn($h) => str_contains($h, 'type') || str_contains($h, 'tipe') || str_contains($h, 'jenis')
            );

            if ($nameCol === false || $typeCol === false) {
                throw new \Exception('Kolom name/nama dan type/tipe/jenis harus ada di file');
            }

            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $mapelData    = [];

            // Process data rows (skip header)
            foreach (array_slice($rows, 1) as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                $name = trim((string)($row[$nameCol] ?? ''));
                $type = strtolower(trim((string)($row[$typeCol] ?? '')));

                if (!$name || !$type) {
                    $errors[] = "Baris $rowNumber: Kolom name dan type tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                $validTypes = ['umum', 'jurusan'];
                if (!in_array($type, $validTypes)) {
                    $errors[] = "Baris $rowNumber: Tipe '$type' tidak valid. Gunakan: " . implode(', ', $validTypes);
                    $errorCount++;
                    continue;
                }

                $mapelData[] = [
                    'name'      => $name,
                    'type'      => $type,
                    'rowNumber' => $rowNumber,
                ];
            }

            \Log::info('Import Mapel - Parsed rows', ['count' => count($mapelData), 'data' => $mapelData]);

            // Create mapels for each combination of class and expertise
            foreach ($mapelData as $mapel) {
                foreach ($classIds as $classId) {
                    /*
                 * - Tipe 'umum'   : expertise_id = NULL, cukup 1 record per kelas
                 * - Tipe 'jurusan': buat per kombinasi kelas + jurusan
                 */
                    $targetExpertiseIds = $mapel['type'] === 'umum'
                        ? [null]
                        : $expertiseIds;

                    foreach ($targetExpertiseIds as $expertiseId) {
                        try {
                            $query = GoogleMapel::where('class_id', $classId)
                                ->where('name', $mapel['name'])
                                ->where('type', $mapel['type']);

                            // Untuk umum: expertise_id harus NULL
                            // Untuk jurusan: expertise_id harus sesuai
                            if ($expertiseId === null) {
                                $query->whereNull('expertise_id');
                            } else {
                                $query->where('expertise_id', $expertiseId);
                            }

                            $exists = $query->exists();

                            \Log::info('Import Mapel - Check exists', [
                                'name'        => $mapel['name'],
                                'type'        => $mapel['type'],
                                'classId'     => $classId,
                                'expertiseId' => $expertiseId,
                                'exists'      => $exists,
                            ]);

                            if ($exists) {
                                // Skip duplikat tanpa dihitung sebagai error
                                $skipCount++;
                                continue;
                            }

                            GoogleMapel::create([
                                'class_id'     => $classId,
                                'expertise_id' => $expertiseId, // null untuk umum
                                'name'         => $mapel['name'],
                                'type'         => $mapel['type'],
                            ]);

                            $successCount++;
                        } catch (\Exception $e) {
                            $errors[]   = "Baris {$mapel['rowNumber']} ({$mapel['name']}): " . $e->getMessage();
                            $errorCount++;
                        }
                    }
                }
            }

            \Log::info('Import Mapel - Done', [
                'successCount' => $successCount,
                'skipCount'    => $skipCount,
                'errorCount'   => $errorCount,
                'errors'       => $errors,
            ]);

            $message = "Import berhasil! $successCount mapel ditambahkan.";
            if ($skipCount > 0) {
                $message .= " $skipCount mapel dilewati (sudah ada).";
            }
            if ($errorCount > 0) {
                $message .= " $errorCount baris gagal.";
            }

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            \Log::error('Import Mapel - Fatal error', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Gagal melakukan import: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function importMapelAuto(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.file'     => 'File harus berupa file',
            'file.mimes'    => 'File harus berformat CSV, XLSX, atau XLS',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $file = $request->file('file');

            // Parse file
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0] ?? [];

            if (empty($rows)) {
                throw new \Exception('File kosong atau format tidak valid');
            }

            // Normalize headers
            $headers = array_map(
                fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $h))),
                $rows[0]
            );

            \Log::info('Import Mapel Auto - Headers', ['headers' => $headers]);

            // Cari kolom
            $nameCol = collect($headers)->search(
                fn($h) => str_contains($h, 'name') || str_contains($h, 'nama')
            );
            $typeCol = collect($headers)->search(
                fn($h) => str_contains($h, 'type') || str_contains($h, 'tipe') || str_contains($h, 'jenis')
            );
            // expertise_name bersifat opsional di header (boleh tidak ada untuk file yang hanya umum)
            $expertiseNameCol = collect($headers)->search(
                fn($h) => str_contains($h, 'expertise_name') || str_contains($h, 'nama_jurusan') || str_contains($h, 'jurusan')
            );

            if ($nameCol === false || $typeCol === false) {
                throw new \Exception('Kolom name/nama dan type/tipe/jenis harus ada di file');
            }

            // Ambil semua data kelas & expertise dari DB - HANYA KELAS 12
            $allClasses = RefClass::where('academic_level', 12)->get();
            $allExpertise = CoreExpertiseConcentration::all();

            // Buat lookup expertise by name (lowercase) → id
            $expertiseLookup = $allExpertise->keyBy(fn($e) => strtolower(trim($e->name)));

            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $mapelData    = [];

            // Parse baris data (skip header)
            foreach (array_slice($rows, 1) as $index => $row) {
                $rowNumber = $index + 2;

                // Skip baris kosong
                if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) {
                    continue;
                }

                $name          = trim((string) ($row[$nameCol] ?? ''));
                $type          = strtolower(trim((string) ($row[$typeCol] ?? '')));
                $expertiseName = $expertiseNameCol !== false
                    ? strtolower(trim((string) ($row[$expertiseNameCol] ?? '')))
                    : '';

                if (!$name || !$type) {
                    $errors[] = "Baris $rowNumber: Kolom name dan type tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                $validTypes = ['umum', 'jurusan'];
                if (!in_array($type, $validTypes)) {
                    $errors[] = "Baris $rowNumber: Tipe '$type' tidak valid. Gunakan: " . implode(', ', $validTypes);
                    $errorCount++;
                    continue;
                }

                // Validasi: tipe jurusan wajib punya expertise_name
                if ($type === 'jurusan') {
                    if (empty($expertiseName)) {
                        $errors[] = "Baris $rowNumber: Kolom expertise_name wajib diisi untuk tipe 'jurusan'";
                        $errorCount++;
                        continue;
                    }

                    if (!isset($expertiseLookup[$expertiseName])) {
                        $errors[] = "Baris $rowNumber: Jurusan '$expertiseName' tidak ditemukan di database";
                        $errorCount++;
                        continue;
                    }
                }

                $mapelData[] = [
                    'name'          => $name,
                    'type'          => $type,
                    'expertiseName' => $expertiseName,
                    'rowNumber'     => $rowNumber,
                ];
            }

            \Log::info('Import Mapel Auto - Parsed rows', ['count' => count($mapelData)]);

            // Simpan ke DB
            foreach ($mapelData as $mapel) {
                try {
                    if ($mapel['type'] === 'umum') {
                        /*
                     * Tipe umum → diterapkan ke SEMUA kombinasi kelas + jurusan
                     * expertise_id = NULL
                     */
                        foreach ($allClasses as $class) {
                            $exists = GoogleMapel::where('class_id', $class->id)
                                ->where('name', $mapel['name'])
                                ->where('type', 'umum')
                                ->whereNull('expertise_id')
                                ->exists();

                            if ($exists) {
                                $skipCount++;
                                continue;
                            }

                            GoogleMapel::create([
                                'class_id'     => $class->id,
                                'expertise_id' => null,
                                'name'         => $mapel['name'],
                                'type'         => 'umum',
                            ]);

                            $successCount++;
                        }
                    } else {
                        // Tipe jurusan → cocokkan expertise, lalu terapkan ke semua kelas yang punya expertise tersebut
                        $expertise   = $expertiseLookup[strtolower(trim($mapel['expertiseName']))];
                        $expertiseId = $expertise->id;

                        // Ambil kelas yang memiliki expertise_concentration_id sesuai
                        $matchedClasses = $allClasses->where('expertise_concentration_id', $expertiseId);

                        if ($matchedClasses->isEmpty()) {
                            // Tidak ada kelas dengan jurusan ini — skip dengan catatan
                            $errors[] = "Baris {$mapel['rowNumber']} ({$mapel['name']}): Tidak ada kelas dengan jurusan '{$mapel['expertiseName']}', data dilewati";
                            $skipCount++;
                            continue;
                        }

                        foreach ($matchedClasses as $class) {
                            $exists = GoogleMapel::where('class_id', $class->id)
                                ->where('name', $mapel['name'])
                                ->where('type', 'jurusan')
                                ->where('expertise_id', $expertiseId)
                                ->exists();

                            if ($exists) {
                                $skipCount++;
                                continue;
                            }

                            GoogleMapel::create([
                                'class_id'     => $class->id,
                                'expertise_id' => $expertiseId,
                                'name'         => $mapel['name'],
                                'type'         => 'jurusan',
                            ]);

                            $successCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[]   = "Baris {$mapel['rowNumber']} ({$mapel['name']}): " . $e->getMessage();
                    $errorCount++;
                }
            }

            \Log::info('Import Mapel Auto - Done', [
                'successCount' => $successCount,
                'skipCount'    => $skipCount,
                'errorCount'   => $errorCount,
            ]);

            $message = "Import berhasil! $successCount mapel ditambahkan.";
            if ($skipCount > 0) {
                $message .= " $skipCount mapel dilewati (sudah ada atau tidak ada kelas yang cocok).";
            }
            if ($errorCount > 0) {
                $message .= " $errorCount baris gagal.";
            }

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            \Log::error('Import Mapel Auto - Fatal error', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Gagal melakukan import: ' . $e->getMessage())
                ->withInput();
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

        // VALIDASI BAHWA GRADUATION ADALAH KELAS 12
        $latestAcademicYear = $graduation->user->academicYears->first();
        if (!$latestAcademicYear || $latestAcademicYear->class->academic_level != 12) {
            return redirect()->route('admin.graduation.index')
                ->with('error', 'Data kelulusan ini bukan dari kelas 12');
        }

        return view('admin.graduation.show', compact('graduation'));
    }

    /**
     * Show surat kelulusan untuk 1 siswa atau semua siswa
     */
    public function showSuratKelulusan($id)
    {
        // Handle export semua
        if ($id === 'all') {
            $graduations = GoogleGraduation::with(['user', 'letter', 'mapels.mapel'])
                ->get();

            $data = [];
            foreach ($graduations as $graduation) {
                $student = $graduation->user;
                $user = auth()->user();
                $letter = $graduation->letter;

                $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();
                $mapelUmum = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')
                    ->sortBy(fn($m) => $m->mapel->order ?? 999)
                    ->values();

                $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')
                    ->sortBy(fn($m) => $m->mapel->order ?? 999)
                    ->values();

                $scores = $mapelsData->whereNotNull('score')->pluck('score');
                $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

                $latestAcademicYear = $student->academicYears->first();
                $program = $latestAcademicYear?->class?->expertiseConcentration;
                $program1 =  $latestAcademicYear?->class?->expertiseProgram;

                $signature = (object)['signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='];

                $data[] = (object) compact('graduation', 'student', 'user', 'letter', 'mapelUmum', 'mapelJurusan', 'rataRata', 'program', 'program1', 'signature');
            }

            return view('admin.graduation.surat-kelulusan-all', compact('data'));
        }

        // Handle 1 siswa
        $graduation = GoogleGraduation::with(['user', 'letter', 'mapels.mapel'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student = $graduation->user;
        $user = auth()->user();
        $letter = $graduation->letter;

        $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();
        $mapelUmum = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')
            ->sortBy(fn($m) => $m->mapel->order ?? 999)
            ->values();

        $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')
            ->sortBy(fn($m) => $m->mapel->order ?? 999)
            ->values();

        $scores = $mapelsData->whereNotNull('score')->pluck('score');
        $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

        $latestAcademicYear = $student->academicYears->first();
        $program = $latestAcademicYear?->class?->expertiseConcentration;
        $program1 =  $latestAcademicYear?->class?->expertiseProgram;

        $signature = (object)['signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='];

        return view('admin.graduation.surat-kelulusan', compact('graduation', 'student', 'user', 'letter', 'mapelUmum', 'mapelJurusan', 'rataRata', 'program', 'program1', 'signature'));
    }

    /**
     * Show surat pernyataan untuk 1 siswa atau semua siswa
     */
    public function showSuratPernyataan($id)
    {
        // Handle export semua
        if ($id === 'all') {
            $graduations = GoogleGraduation::with(['user', 'letter'])->get();

            $data = [];
            foreach ($graduations as $graduation) {
                $student = $graduation->user;
                $user = auth()->user();

                $latestAcademicYear = $student->academicYears->first();
                $program1 = $latestAcademicYear?->class?->expertiseConcentration;

                // Ambil signature dari google_statement → google_student_signatures
                $statement = \App\Models\GoogleStatement::where('user_id', $student->id)
                    ->first();
                $signature = $statement?->signature ?? null;

                $data[] = (object) compact('graduation', 'student', 'user', 'program1', 'signature');
            }

            return view('admin.graduation.surat-pernyataan-all', compact('data'));
        }

        // Handle 1 siswa
        $graduation = GoogleGraduation::with(['user', 'letter'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student = $graduation->user;
        $user = auth()->user();

        $latestAcademicYear = $student->academicYears->first();
        $program1 = $latestAcademicYear?->class?->expertiseConcentration;

        // Ambil signature
        $statement = \App\Models\GoogleStatement::where('user_id', $student->id)
            ->first();
        $signature = $statement?->signature ?? null;

        return view('admin.graduation.surat-pernyataan', compact('graduation', 'student', 'user', 'program1', 'signature'));
    }

    /**
     * Delete kelulusan
     */
    public function destroy($id)
    {
        try {
            $graduation = GoogleGraduation::findOrFail($id);

            \DB::beginTransaction();

            // Hapus detail mapel terlebih dahulu
            GoogleGraduationMapel::where('graduation_id', $id)->delete();

            // Hapus header kelulusan
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
     * Show form untuk import nilai
     */
    public function showImportNilai()
    {
        return view('admin.graduation.import-nilai');
    }

    /**
     * Process import nilai dari file CSV
     * CSV harus memiliki kolom: No, NIS, NISN, Nama Siswa, Kelas, Tapel, Id Mapel, Nama Mapel, Nilai
     */
    public function importNilai(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.file'     => 'File harus berupa file',
            'file.mimes'    => 'File harus berformat CSV atau TXT',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        try {
            \DB::beginTransaction();

            $file         = $request->file('file');
            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $graduationMap = [];

            // Buka file
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                throw new \Exception('Gagal membuka file');
            }

            // ── 1. Skip BOM jika ada ──────────────────────────────────────────
            $bom = fread($handle, 3);
            if ($bom !== "\xef\xbb\xbf") {
                rewind($handle);
            }

            // ── 2. Auto-detect delimiter ──────────────────────────────────────
            $firstLine = fgets($handle);
            if (!$firstLine) {
                throw new \Exception('File kosong atau tidak valid');
            }
            // Kembali ke posisi setelah BOM (atau awal file)
            fseek($handle, $bom === "\xef\xbb\xbf" ? 3 : 0);

            $delimiters = [';' => 0, ',' => 0, "\t" => 0, '|' => 0];
            foreach ($delimiters as $delim => &$count) {
                $count = substr_count($firstLine, $delim);
            }
            unset($count);
            arsort($delimiters);
            $delimiter = array_key_first($delimiters);

            \Log::info('Import Nilai - Delimiter detected', ['delimiter' => json_encode($delimiter)]);

            // ── 3. Baca header ────────────────────────────────────────────────
            $headers = fgetcsv($handle, 0, $delimiter);
            if (!$headers) {
                throw new \Exception('File kosong atau tidak valid');
            }

            // Normalize headers: lowercase, trim, buang karakter non-printable
            $headers = array_map(
                fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $h))),
                $headers
            );

            \Log::info('Import Nilai - Headers parsed', ['headers' => $headers]);

            // ── 4. Cari indeks kolom ──────────────────────────────────────────
            $nisCol = collect($headers)->search(
                fn($h) => str_contains($h, 'nis') && !str_contains($h, 'nisn')
            );
            $nisnCol = collect($headers)->search(
                fn($h) => str_contains($h, 'nisn')
            );
            $namaCol = collect($headers)->search(
                fn($h) => str_contains($h, 'nama')
            );
            $mapelIdCol = collect($headers)->search(
                fn($h) => str_contains($h, 'id mapel')
                    || str_contains($h, 'id_mapel')
                    || $h === 'id'
            );
            $nilaiCol = collect($headers)->search(
                fn($h) => str_contains($h, 'nilai')
            );

            // Validasi kolom wajib
            if ($nisCol === false) {
                throw new \Exception(
                    'Kolom NIS tidak ditemukan. Header yang terbaca: ' . implode(', ', $headers)
                );
            }
            if ($nilaiCol === false) {
                throw new \Exception(
                    'Kolom Nilai tidak ditemukan. Header yang terbaca: ' . implode(', ', $headers)
                );
            }
            if ($mapelIdCol === false) {
                throw new \Exception(
                    'Kolom Id Mapel tidak ditemukan. Header yang terbaca: ' . implode(', ', $headers)
                );
            }

            $rowNumber = 1; // baris 1 = header

            // ── 5. Proses data ────────────────────────────────────────────────
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Skip baris kosong
                if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) {
                    continue;
                }

                $nis     = trim((string) ($row[$nisCol]     ?? ''));
                $nilai   = trim((string) ($row[$nilaiCol]   ?? ''));
                $mapelId = trim((string) ($row[$mapelIdCol] ?? ''));

                // Skip jika NIS kosong
                if ($nis === '') {
                    $skipCount++;
                    continue;
                }

                // Skip jika mapelId atau nilai kosong
                if ($mapelId === '' || $nilai === '') {
                    $skipCount++;
                    continue;
                }

                try {
                    // Cari student berdasarkan NIS
                    $student = RefStudent::where('student_number', $nis)->first();

                    if (!$student) {
                        $errors[] = "Baris $rowNumber: NIS '$nis' tidak ditemukan di database";
                        $errorCount++;
                        continue;
                    }

                    // Ambil atau buat graduation record
                    if (!isset($graduationMap[$student->id])) {
                        $graduation = GoogleGraduation::firstOrCreate(
                            ['user_id' => $student->id],
                            [
                                'letter_number'   => '',
                                'graduation_date' => now(),
                            ]
                        );
                        $graduationMap[$student->id] = $graduation;
                    } else {
                        $graduation = $graduationMap[$student->id];
                    }

                    // Cek mapel valid
                    $mapel = GoogleMapel::where('uuid', $mapelId)->first();
                    if (!$mapel) {
                        $errors[] = "Baris $rowNumber: Mapel dengan ID '$mapelId' tidak ditemukan";
                        $errorCount++;
                        continue;
                    }

                    // Validasi nilai numerik 0–100
                    $nilaiNumeric = (float) str_replace(',', '.', $nilai);
                    if ($nilaiNumeric < 0 || $nilaiNumeric > 100) {
                        $errors[] = "Baris $rowNumber: Nilai '$nilai' tidak valid (harus angka antara 0–100)";
                        $errorCount++;
                        continue;
                    }

                    // Simpan atau update GoogleGraduationMapel
                    GoogleGraduationMapel::updateOrCreate(
                        [
                            'graduation_id' => $graduation->uuid,
                            'mapel_id'      => $mapel->uuid,
                        ],
                        ['score' => $nilaiNumeric]
                    );

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                    $errorCount++;
                }
            }

            fclose($handle);

            \DB::commit();

            \Log::info('Import Nilai - Done', [
                'successCount' => $successCount,
                'skipCount'    => $skipCount,
                'errorCount'   => $errorCount,
            ]);

            $message = "Import berhasil! $successCount nilai berhasil disimpan.";
            if ($skipCount > 0) $message .= " $skipCount baris dilewati.";
            if ($errorCount > 0) $message .= " $errorCount baris gagal.";

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Import Nilai - Fatal error', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Gagal melakukan import: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Apply template letter ke semua graduation records
     * POST /admin/graduation/apply-template
     */
    public function applyTemplateToAll(Request $request)
    {
        try {
            $validated = $request->validate([
                'letter_id' => 'required|string|exists:google_graduation_letters,uuid',
            ]);

            $letterUuid = $validated['letter_id'];

            // Pakai where+first bukan findOrFail agar tidak throw HTML exception
            $letter = GoogleGraduationLetter::where('uuid', $letterUuid)->first();

            if (!$letter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template surat tidak ditemukan',
                ], 404);
            }

            $updatedCount = GoogleGraduation::whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })->update([
                'letter_id' => $letterUuid,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Template '{$letter->letter_number}' berhasil diterapkan ke {$updatedCount} data kelulusan!",
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
