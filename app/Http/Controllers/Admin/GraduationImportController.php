<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoreExpertiseConcentration;
use App\Models\GoogleGraduation;
use App\Models\GoogleGraduationMapel;
use App\Models\GoogleMapel;
use App\Models\RefClass;
use App\Models\RefStudent;
use App\Models\ExpertiseConcentration;
use Illuminate\Http\Request;

class GraduationImportController extends Controller
{
    // =========================================================================
    // DOWNLOAD TEMPLATE
    // =========================================================================

    /**
     * Download template CSV untuk input nilai kelulusan
     */
    public function downloadTemplate(Request $request)
    {
        $classId     = $request->query('class_id');
        $expertiseId = $request->query('expertise_id');
        $userId      = $request->query('user_id');
        $format      = $request->query('format', 'csv');
        $type        = $request->query('template_type', 'graduation');

        $students = RefStudent::query()
            ->when($userId, fn($q) => $q->where('id', $userId))
            ->when($classId, fn($q) => $q->whereHas('academicYears', fn($q) => $q->where('class_id', $classId)))
            ->when($expertiseId, fn($q) => $q->whereHas('academicYears', fn($q) => $q->where('expertise_concentration_id', $expertiseId)))
            ->whereHas('academicYears.class', fn($q) => $q->where('academic_level', 12))
            ->with([
                'user',
                'academicYears' => function ($q) {
                    $q->with('class')->latest();
                },
            ])
            ->orderBy('full_name', 'asc')
            ->get();

        $mapels = GoogleMapel::query()
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->get()
            ->groupBy('class_id');

        $fileName = 'Template_Kelulusan_';
        if ($userId && $students->isNotEmpty()) {
            $fileName .= str_replace(' ', '_', $students->first()->full_name) . '_';
        }
        $fileName .= now()->format('d-m-Y_His') . '.' . ($format === 'xlsx' ? 'xlsx' : 'csv');

        if ($format === 'xlsx') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\GraduationMultiSheetExport($students, $mapels, $type),
                $fileName
            );
        }

        $fp = fopen('php://memory', 'w');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if ($type === 'graduation') {
            fputcsv($fp, ['Nama Siswa', 'Kelas', 'Id Mapel', 'Nama Mapel', 'NA (Nilai Akhir)', 'NIS'], ';');
        } else {
            fputcsv($fp, ['Nama Siswa', 'Kelas', 'Id Mapel', 'Nama Mapel', 'S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'NA', 'NIS'], ';');
        }

        foreach ($students->values() as $student) {
            $latestAcademicYear = $student->academicYears->first();
            $studentClassId     = $latestAcademicYear?->class_id;
            $studentMapels      = $studentClassId ? ($mapels->get($studentClassId) ?? collect()) : collect();
            $kelasLabel         = ($latestAcademicYear?->class?->academic_level ?? '') . ' ' . ($latestAcademicYear?->class?->name ?? '');

            foreach ($studentMapels as $mapel) {
                // Get existing scores if any
                $existing = GoogleGraduationMapel::where('graduation_id', function($q) use ($student) {
                    $q->select('uuid')->from('google_graduation')->where('user_id', $student->id)->limit(1);
                })->where('mapel_id', $mapel->uuid)->first();

                if ($type === 'graduation') {
                    fputcsv($fp, [
                        $student->full_name,
                        $kelasLabel,
                        $mapel->uuid,
                        $mapel->name,
                        $mapel->has_na ? ($existing?->score ?? '') : 'N/A',
                        $student->student_number ?? '',
                    ], ';');
                } else {
                    fputcsv($fp, [
                        $student->full_name,
                        $kelasLabel,
                        $mapel->uuid,
                        $mapel->name,
                        $existing?->sem_1 ?? '',
                        $existing?->sem_2 ?? '',
                        $existing?->sem_3 ?? '',
                        $existing?->sem_4 ?? '',
                        $existing?->sem_5 ?? '',
                        $existing?->sem_6 ?? '',
                        $mapel->has_na ? ($existing?->score ?? '') : 'N/A',
                        $student->student_number ?? '',
                    ], ';');
                }
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

    // =========================================================================
    // IMPORT MAPEL
    // =========================================================================

    /**
     * Show form import mapel
     */
    public function showImportMapel()
    {
        $classes   = RefClass::where('academic_level', 12)
            ->select(['id', 'name', 'expertise_concentration_id', 'academic_level'])
            ->get();
        $expertise = ExpertiseConcentration::select(['id', 'name'])->get();

        return view('admin.graduation.import-mapel', compact('classes', 'expertise'));
    }

    /**
     * Process import mapel (dengan pilihan kelas & jurusan manual)
     */
    public function importMapel(Request $request)
    {
        $validated = $request->validate([
            'file'            => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'class_ids'       => 'required|array|min:1',
            'class_ids.*'     => 'required|string|exists:ref_classes,id',
            'expertise_ids'   => 'required|array|min:1',
            'expertise_ids.*' => 'required|string|exists:core_expertise_concentrations,id',
        ], [
            'file.required'        => 'File harus diupload',
            'file.mimes'           => 'File harus berformat CSV, XLSX, atau XLS',
            'file.max'             => 'Ukuran file maksimal 10MB',
            'class_ids.required'   => 'Pilih minimal satu kelas',
            'expertise_ids.required' => 'Pilih minimal satu jurusan',
        ]);

        try {
            $classIds     = array_values(array_unique(array_filter(array_map('strval', $validated['class_ids']), fn($id) => !empty(trim($id)))));
            $expertiseIds = array_values(array_unique(array_filter(array_map('strval', $validated['expertise_ids']), fn($id) => !empty(trim($id)))));

            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('file'))[0] ?? [];
            if (empty($rows)) throw new \Exception('File kosong atau format tidak valid');

            [$nameCol, $typeCol, $naCol] = $this->parseMapelHeaders($rows[0]);

            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $mapelData    = $this->parseMapelRows(array_slice($rows, 1), $nameCol, $typeCol, $naCol, $errors, $errorCount);

            foreach ($mapelData as $mapel) {
                foreach ($classIds as $classId) {
                    $targetExpertiseIds = $mapel['type'] === 'umum' ? [null] : $expertiseIds;

                    foreach ($targetExpertiseIds as $expertiseId) {
                        try {
                            $query = GoogleMapel::where('class_id', $classId)
                                ->where('name', $mapel['name'])
                                ->where('type', $mapel['type']);

                            $expertiseId === null
                                ? $query->whereNull('expertise_id')
                                : $query->where('expertise_id', $expertiseId);

                            if ($query->exists()) {
                                $skipCount++;
                                continue;
                            }

                            GoogleMapel::create([
                                'class_id'     => $classId,
                                'expertise_id' => $expertiseId,
                                'name'         => $mapel['name'],
                                'type'         => $mapel['type'],
                                'has_na'       => $mapel['has_na'] ?? true
                            ]);
                            $successCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Baris {$mapel['rowNumber']} ({$mapel['name']}): " . $e->getMessage();
                            $errorCount++;
                        }
                    }
                }
            }

            return $this->importRedirect($successCount, $skipCount, $errorCount, $errors);
        } catch (\Exception $e) {
            \Log::error('Import Mapel - Fatal error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal melakukan import: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Process import mapel otomatis (kelas & jurusan dicocokkan dari DB)
     */
    public function importMapelAuto(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.mimes'    => 'File harus berformat CSV, XLSX, atau XLS',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('file'))[0] ?? [];
            if (empty($rows)) throw new \Exception('File kosong atau format tidak valid');

            $headers          = $this->normalizeHeaders($rows[0]);
            [$nameCol, $typeCol, $naCol] = $this->parseMapelHeaders($rows[0]);
            $expertiseNameCol = collect($headers)->search(
                fn($h) => str_contains($h, 'expertise_name') || str_contains($h, 'nama_jurusan') || str_contains($h, 'jurusan')
            );

            $allClasses      = RefClass::where('academic_level', 12)->get();
            $allExpertise    = CoreExpertiseConcentration::all();
            $expertiseLookup = $allExpertise->keyBy(fn($e) => strtolower(trim($e->name)));

            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $mapelData    = [];

            foreach (array_slice($rows, 1) as $index => $row) {
                $rowNumber = $index + 2;
                if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) continue;

                $name          = trim((string) ($row[$nameCol] ?? ''));
                $type          = strtolower(trim((string) ($row[$typeCol] ?? '')));
                $expertiseName = $expertiseNameCol !== false ? strtolower(trim((string) ($row[$expertiseNameCol] ?? ''))) : '';
                
                $hasNa = true;
                if ($naCol !== false && isset($row[$naCol])) {
                    $val = strtolower(trim((string)$row[$naCol]));
                    if ($val === '0' || $val === 'tidak' || $val === 'no' || $val === 'false') {
                        $hasNa = false;
                    }
                }

                if (!$name || !$type) {
                    $errors[] = "Baris $rowNumber: Kolom name dan type tidak boleh kosong";
                    $errorCount++;
                    continue;
                }
                if (!in_array($type, ['umum', 'jurusan'])) {
                    $errors[] = "Baris $rowNumber: Tipe '$type' tidak valid";
                    $errorCount++;
                    continue;
                }

                if ($type === 'jurusan') {
                    if (empty($expertiseName)) {
                        $errors[] = "Baris $rowNumber: Kolom expertise_name wajib diisi untuk tipe 'jurusan'";
                        $errorCount++;
                        continue;
                    }
                    if (!isset($expertiseLookup[$expertiseName])) {
                        $errors[] = "Baris $rowNumber: Jurusan '$expertiseName' tidak ditemukan";
                        $errorCount++;
                        continue;
                    }
                }

                $mapelData[] = [
                    'name'          => $name,
                    'type'          => $type,
                    'expertiseName' => $expertiseName,
                    'has_na'        => $hasNa,
                    'rowNumber'     => $rowNumber
                ];
            }

            foreach ($mapelData as $mapel) {
                try {
                    if ($mapel['type'] === 'umum') {
                        foreach ($allClasses as $class) {
                            $exists = GoogleMapel::where('class_id', $class->id)->where('name', $mapel['name'])->where('type', 'umum')->whereNull('expertise_id')->exists();
                            if ($exists) {
                                $skipCount++;
                                continue;
                            }
                            GoogleMapel::create([
                                'class_id'     => $class->id,
                                'expertise_id' => null,
                                'name'         => $mapel['name'],
                                'type'         => 'umum',
                                'has_na'       => $mapel['has_na'] ?? true
                            ]);
                            $successCount++;
                        }
                    } else {
                        $expertise      = $expertiseLookup[strtolower(trim($mapel['expertiseName']))];
                        $expertiseId    = $expertise->id;
                        $matchedClasses = $allClasses->where('expertise_concentration_id', $expertiseId);

                        if ($matchedClasses->isEmpty()) {
                            $errors[] = "Baris {$mapel['rowNumber']} ({$mapel['name']}): Tidak ada kelas dengan jurusan '{$mapel['expertiseName']}'";
                            $skipCount++;
                            continue;
                        }

                        foreach ($matchedClasses as $class) {
                            $exists = GoogleMapel::where('class_id', $class->id)->where('name', $mapel['name'])->where('type', 'jurusan')->where('expertise_id', $expertiseId)->exists();
                            if ($exists) {
                                $skipCount++;
                                continue;
                            }
                            GoogleMapel::create([
                                'class_id'     => $class->id,
                                'expertise_id' => $expertiseId,
                                'name'         => $mapel['name'],
                                'type'         => 'jurusan',
                                'has_na'       => $mapel['has_na'] ?? true
                            ]);
                            $successCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris {$mapel['rowNumber']} ({$mapel['name']}): " . $e->getMessage();
                    $errorCount++;
                }
            }

            return $this->importRedirect($successCount, $skipCount, $errorCount, $errors);
        } catch (\Exception $e) {
            \Log::error('Import Mapel Auto - Fatal error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal melakukan import: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================================
    // IMPORT NILAI
    // =========================================================================

    /**
     * Show form import nilai
     */
    public function showImportNilai()
    {
        return view('admin.graduation.import-nilai');
    }

    /**
     * Process import nilai dari file CSV
     */
    public function importNilai(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.mimes'    => 'File harus berformat CSV, XLSX, atau XLS',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        try {
            \DB::beginTransaction();

            $file          = $request->file('file');
            $extension     = strtolower($file->getClientOriginalExtension());
            $successCount  = 0;
            $skipCount     = 0;
            $errorCount    = 0;
            $errors        = [];
            $graduationMap = [];

            if (in_array($extension, ['xlsx', 'xls'])) {
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new class {}, $file);
                
                // Jika hasilnya adalah array 2D (berarti hanya 1 sheet yang terbaca secara default di beberapa versi)
                // Tapi biasanya toArray(new class{}, $file) mengembalikan array of sheets (3D)
                
                if (isset($sheets[0]) && !is_array($sheets[0][0])) {
                    // Ini 2D array (hanya 1 sheet)
                    $this->processImportRows($sheets, $successCount, $skipCount, $errorCount, $errors, $graduationMap);
                } else {
                    // Ini 3D array (multi sheet)
                    foreach ($sheets as $rows) {
                        if (empty($rows)) continue;
                        $this->processImportRows($rows, $successCount, $skipCount, $errorCount, $errors, $graduationMap);
                    }
                }
            } else {
                $handle = fopen($file->getRealPath(), 'r');
                if (!$handle) throw new \Exception('Gagal membuka file');

                // Skip BOM
                $bom = fread($handle, 3);
                if ($bom !== "\xef\xbb\xbf") rewind($handle);

                // Auto-detect delimiter
                $firstLine = fgets($handle);
                if (!$firstLine) throw new \Exception('File kosong atau tidak valid');
                fseek($handle, $bom === "\xef\xbb\xbf" ? 3 : 0);

                $delimiters = [';' => 0, ',' => 0, "\t" => 0, '|' => 0];
                foreach ($delimiters as $delim => &$count) $count = substr_count($firstLine, $delim);
                unset($count);
                arsort($delimiters);
                $delimiter = array_key_first($delimiters);

                $rows = [];
                rewind($handle);
                if ($bom === "\xef\xbb\xbf") fread($handle, 3);
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);

                if (!empty($rows)) {
                    $this->processImportRows($rows, $successCount, $skipCount, $errorCount, $errors, $graduationMap);
                }
            }

            \DB::commit();

            $message = "Import berhasil! $successCount nilai berhasil disimpan.";
            if ($skipCount > 0) $message .= " $skipCount baris dilewati.";
            if ($errorCount > 0) $message .= " $errorCount baris gagal.";

            return redirect()->route('admin.graduation.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Import Nilai - Fatal error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal melakukan import: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function normalizeHeaders(array $row): array
    {
        return array_map(
            fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $h))),
            $row
        );
    }

    private function parseMapelHeaders(array $headerRow): array
    {
        $headers = $this->normalizeHeaders($headerRow);

        $nameCol = collect($headers)->search(fn($h) => str_contains($h, 'name') || str_contains($h, 'nama'));
        $typeCol = collect($headers)->search(fn($h) => str_contains($h, 'type') || str_contains($h, 'tipe') || str_contains($h, 'jenis'));
        $naCol   = collect($headers)->search(fn($h) => str_contains($h, 'has_na') || str_contains($h, 'ada_na') || str_contains($h, 'punya_na'));

        if ($nameCol === false || $typeCol === false) {
            throw new \Exception('Kolom name/nama dan type/tipe/jenis harus ada di file');
        }

        return [$nameCol, $typeCol, $naCol];
    }

    private function parseMapelRows(array $rows, int $nameCol, int $typeCol, $naCol, array &$errors, int &$errorCount): array
    {
        $mapelData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) continue;

            $name = trim((string) ($row[$nameCol] ?? ''));
            $type = strtolower(trim((string) ($row[$typeCol] ?? '')));

            if (!$name || !$type) {
                $errors[] = "Baris $rowNumber: Kolom name dan type tidak boleh kosong";
                $errorCount++;
                continue;
            }

            if (!in_array($type, ['umum', 'jurusan'])) {
                $errors[] = "Baris $rowNumber: Tipe '$type' tidak valid. Gunakan: umum, jurusan";
                $errorCount++;
                continue;
            }

            $hasNa = true;
            if ($naCol !== false && isset($row[$naCol])) {
                $val = strtolower(trim((string)$row[$naCol]));
                if ($val === '0' || $val === 'tidak' || $val === 'no' || $val === 'false') {
                    $hasNa = false;
                }
            }

            $mapelData[] = [
                'name'   => $name,
                'type'   => $type,
                'has_na' => $hasNa,
                'rowNumber' => $rowNumber
            ];
        }

        return $mapelData;
    }

    private function processImportRows(array $rows, int &$successCount, int &$skipCount, int &$errorCount, array &$errors, array &$graduationMap)
    {
        if (empty($rows)) return;

        // Cari baris header (cek 10 baris pertama)
        $headerRowIndex = -1;
        $nisCol = false;
        $mapelIdCol = false;

        for ($i = 0; $i < min(10, count($rows)); $i++) {
            $headers = $this->normalizeHeaders($rows[$i]);
            $nisCol      = collect($headers)->search(fn($h) => str_contains($h, 'nis') && !str_contains($h, 'nisn'));
            $mapelIdCol  = collect($headers)->search(fn($h) => str_contains($h, 'id mapel') || str_contains($h, 'id_mapel') || $h === 'id');
            
            if ($nisCol !== false && $mapelIdCol !== false) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($headerRowIndex === -1) return; // Tidak menemukan header yang valid di sheet ini

        // Refresh headers from the detected row
        $headers = $this->normalizeHeaders($rows[$headerRowIndex]);
        $s1Col       = collect($headers)->search(fn($h) => $h === 's1' || str_contains($h, 'semester 1'));
        $s2Col       = collect($headers)->search(fn($h) => $h === 's2' || str_contains($h, 'semester 2'));
        $s3Col       = collect($headers)->search(fn($h) => $h === 's3' || str_contains($h, 'semester 3'));
        $s4Col       = collect($headers)->search(fn($h) => $h === 's4' || str_contains($h, 'semester 4'));
        $s5Col       = collect($headers)->search(fn($h) => $h === 's5' || str_contains($h, 'semester 5'));
        $s6Col       = collect($headers)->search(fn($h) => $h === 's6' || str_contains($h, 'semester 6'));
        $nrCol       = collect($headers)->search(fn($h) => $h === 'nr' || str_contains($h, 'nilai rapor'));
        $naCol       = collect($headers)->search(fn($h) => $h === 'na' || str_contains($h, 'nilai akhir') || str_contains($h, 'nilai'));

        // Mulai proses data dari baris setelah header
        foreach (array_slice($rows, $headerRowIndex + 1) as $index => $row) {
            $rowNumber = $headerRowIndex + $index + 2;
            if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) continue;

            $nis     = trim((string) ($row[$nisCol]     ?? ''));
            $mapelId = trim((string) ($row[$mapelIdCol] ?? ''));

            if ($nis === '' || $mapelId === '') {
                $skipCount++;
                continue;
            }

            try {
                $student = RefStudent::where('student_number', $nis)->first();
                if (!$student) {
                    $errors[] = "Baris $rowNumber: NIS '$nis' tidak ditemukan";
                    $errorCount++;
                    continue;
                }

                if (!isset($graduationMap[$student->id])) {
                    $graduationMap[$student->id] = GoogleGraduation::firstOrCreate(
                        ['user_id' => $student->id],
                        ['letter_number' => '', 'graduation_date' => now()]
                    );
                }

                $mapel = GoogleMapel::where('uuid', $mapelId)->first();
                if (!$mapel) {
                    $errors[] = "Baris $rowNumber: Mapel '$mapelId' tidak ditemukan";
                    $errorCount++;
                    continue;
                }

                $parseScore = function($val) {
                    if ($val === '' || $val === null || strtoupper(trim((string)$val)) === 'N/A') return null;
                    $n = (float) str_replace(',', '.', $val);
                    return ($n >= 0 && $n <= 100) ? $n : null;
                };

                $s1 = $s1Col !== false ? $parseScore($row[$s1Col] ?? '') : null;
                $s2 = $s2Col !== false ? $parseScore($row[$s2Col] ?? '') : null;
                $s3 = $s3Col !== false ? $parseScore($row[$s3Col] ?? '') : null;
                $s4 = $s4Col !== false ? $parseScore($row[$s4Col] ?? '') : null;
                $s5 = $s5Col !== false ? $parseScore($row[$s5Col] ?? '') : null;
                $s6 = $s6Col !== false ? $parseScore($row[$s6Col] ?? '') : null;

                $semesters = array_filter([$s1, $s2, $s3, $s4, $s5, $s6], fn($v) => !is_null($v));
                $nr = count($semesters) > 0 ? array_sum($semesters) / count($semesters) : null;

                GoogleGraduationMapel::updateOrCreate(
                    ['graduation_id' => $graduationMap[$student->id]->uuid, 'mapel_id' => $mapel->uuid],
                    [
                        'sem_1' => $s1,
                        'sem_2' => $s2,
                        'sem_3' => $s3,
                        'sem_4' => $s4,
                        'sem_5' => $s5,
                        'sem_6' => $s6,
                        'nr'    => $nr,
                        'score' => ($mapel->has_na && $naCol !== false) ? $parseScore($row[$naCol] ?? '') : null,
                    ]
                );
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Baris $rowNumber: " . $e->getMessage();
                $errorCount++;
            }
        }
    }

    private function importRedirect(int $success, int $skip, int $error, array $errors)
    {
        $message = "Import berhasil! $success mapel ditambahkan.";
        if ($skip > 0)  $message .= " $skip mapel dilewati (sudah ada).";
        if ($error > 0) $message .= " $error baris gagal.";

        return redirect()->route('admin.graduation.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
