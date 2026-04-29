<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleGraduation;
use App\Models\GoogleGraduationMapel;
use App\Models\GoogleMapel;
use App\Models\User;
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
        // 1. Ambil data mapels (sudah ada di kode Anda)
        $mapels = GoogleMapel::with(['class', 'expertise'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // 2. AMBIL DATA KELULUSAN untuk tabel component
        // Component akan handle pagination sendiri
        $graduations = GoogleGraduation::with(['user', 'mapels'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Stats (sudah ada di kode Anda)
        $totalMapels = GoogleMapel::count();
        $totalGraduations = GoogleGraduation::count();
        $totalUsers = GoogleGraduation::distinct('user_id')->count('user_id');

        // 4. Data untuk dropdown
        $classes = RefClass::select(['id', 'name', 'expertise_concentration_id', 'academic_level'])->get();
        $expertise = ExpertiseConcentration::select(['id', 'name'])->get();

        // 5. Kirim 'graduations' ke view menggunakan compact
        return view('admin.graduation.index', compact(
            'mapels',
            'graduations',
            'totalMapels',
            'totalGraduations',
            'totalUsers',
            'classes',
            'expertise'
        ));
    }
    public function create()
    {
        $classes = RefClass::with('expertiseConcentration')
            ->orderBy('academic_level')
            ->orderBy('name')
            ->get();

        return view('admin.graduation.create', compact('classes'));
    }

    public function getStudentsByClass(Request $request)
    {
        $classId = $request->query('class_id');

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

            // Cek dulu apakah class_id yang dikirim valid
            $class = RefClass::find($classId);
            if (!$class) {
                return response()->json(['error' => 'Kelas tidak ditemukan'], 404);
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
            'student_id'      => 'required|exists:ref_students,id',
            'letter_number'   => 'required|string',
            'graduation_date' => 'required|date',
            'mapel_ids'       => 'required|array|min:1',
        ]);

        try {
            \DB::beginTransaction();

            $graduation = GoogleGraduation::create([
                'user_id'         => $validated['student_id'],
                'letter_number'   => $validated['letter_number'],
                'graduation_date' => $validated['graduation_date'],
            ]);

            foreach ($validated['mapel_ids'] as $mapelUuid) {
                GoogleGraduationMapel::create([
                    'graduation_id' => $graduation->uuid,
                    'mapel_id'      => $mapelUuid,
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
        $classes = RefClass::select(['id', 'name', 'expertise_concentration_id', 'academic_level'])
            ->get();

        $expertise = ExpertiseConcentration::select(['id', 'name'])
            ->get();

        return view('admin.graduation.create-mapel', compact('classes', 'expertise'));
    }

    /**
     * Store mapel baru ke database
     */
    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:ref_classes,id',
            'expertise_id' => 'required|exists:core_expertise_concentrations,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:wajib,pilihan,praktik',
        ], [
            'class_id.required' => 'Kelas harus dipilih',
            'class_id.exists' => 'Kelas tidak ditemukan',
            'expertise_id.required' => 'Jurusan harus dipilih',
            'expertise_id.exists' => 'Jurusan tidak ditemukan',
            'name.required' => 'Nama mapel harus diisi',
            'name.max' => 'Nama mapel maksimal 255 karakter',
            'type.required' => 'Tipe mapel harus dipilih',
            'type.in' => 'Tipe mapel harus: wajib, pilihan, atau praktik',
        ]);

        try {
            GoogleMapel::create($validated);

            return redirect()
                ->route('admin.graduation.index')
                ->with('success', 'Mapel ' . $validated['name'] . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
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
        $expertiseId = $request->query('expertise_id');

        $students = RefStudent::query()
            ->when($classId, function ($q) use ($classId) {
                $q->whereHas('academicYears', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
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
            ->when($expertiseId, fn($q) => $q->where('expertise_id', $expertiseId))
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
        $classes = RefClass::select(['id', 'name', 'expertise_concentration_id', 'academic_level'])
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
            'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.file' => 'File harus berupa file',
            'file.mimes' => 'File harus berformat CSV, XLSX, XLS, atau TXT',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            \DB::beginTransaction();

            $file = $request->file('file');
            $successCount = 0;
            $skipCount = 0;
            $errorCount = 0;
            $errors = [];
            $graduationMap = []; // Cache untuk graduation record per user

            // Baca file CSV
            $handle = fopen($file->getRealPath(), 'r');

            // Skip BOM jika ada
            $bom = fread($handle, 3);
            if ($bom !== "\xef\xbb\xbf") {
                rewind($handle);
            }

            // Baca header (baris pertama)
            $headers = fgetcsv($handle, 1000, ';');
            if (!$headers) {
                throw new \Exception('File kosong atau tidak valid');
            }

            // Normalize headers
            $headers = array_map(
                fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h))),
                $headers
            );

            // Find column indices
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
                fn($h) => str_contains($h, 'id mapel') || str_contains($h, 'id_mapel')
            );
            $nilaiCol = collect($headers)->search(
                fn($h) => str_contains($h, 'nilai')
            );

            if ($nisCol === false || $nilaiCol === false) {
                throw new \Exception('Kolom NIS dan Nilai harus ada di file');
            }

            $rowNumber = 1; // Header adalah baris 1

            // Process data rows
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                $nis = trim((string)($row[$nisCol] ?? ''));
                $nilai = trim((string)($row[$nilaiCol] ?? ''));
                $mapelId = trim((string)($row[$mapelIdCol] ?? ''));

                // Skip jika NIS kosong
                if (empty($nis)) {
                    $skipCount++;
                    continue;
                }

                // Skip jika tidak ada mapelId atau nilai
                if (empty($mapelId) || empty($nilai)) {
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

                    // HAPUS blok cek $user, langsung pakai $student->id sebagai user_id
                    if (!isset($graduationMap[$student->id])) {
                        $graduation = GoogleGraduation::firstOrCreate(
                            ['user_id' => $student->id],  // kolom tetap user_id, isi pakai student->id
                            [
                                'letter_number' => '',
                                'graduation_date' => now(),
                            ]
                        );
                        $graduationMap[$student->id] = $graduation;
                    } else {
                        $graduation = $graduationMap[$student->id];
                    }

                    // Cek apakah mapelId valid
                    $mapel = GoogleMapel::where('uuid', $mapelId)->first();
                    if (!$mapel) {
                        $errors[] = "Baris $rowNumber: Mapel dengan ID '$mapelId' tidak ditemukan";
                        $errorCount++;
                        continue;
                    }

                    // Validate nilai (harus numeric dan antara 0-100)
                    $nilaiNumeric = (float)str_replace(',', '.', $nilai);
                    if (!is_numeric($nilaiNumeric) || $nilaiNumeric < 0 || $nilaiNumeric > 100) {
                        $errors[] = "Baris $rowNumber: Nilai '$nilai' tidak valid (harus numeric antara 0-100)";
                        $errorCount++;
                        continue;
                    }

                    // Simpan atau update GoogleGraduationMapel
                    $existingMapel = GoogleGraduationMapel::where('graduation_id', $graduation->uuid)
                        ->where('mapel_id', $mapel->uuid)
                        ->first();

                    if ($existingMapel) {
                        $existingMapel->update(['score' => $nilaiNumeric]);
                    } else {
                        GoogleGraduationMapel::create([
                            'graduation_id' => $graduation->uuid,
                            'mapel_id'      => $mapel->uuid,
                            'score'         => $nilaiNumeric,
                        ]);
                    }

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
                'skipCount' => $skipCount,
                'errorCount' => $errorCount,
            ]);

            $message = "Import berhasil! $successCount nilai berhasil disimpan.";
            if ($skipCount > 0) {
                $message .= " $skipCount baris dilewati (kosong/tidak lengkap).";
            }
            if ($errorCount > 0) {
                $message .= " $errorCount baris gagal.";
            }

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
}
