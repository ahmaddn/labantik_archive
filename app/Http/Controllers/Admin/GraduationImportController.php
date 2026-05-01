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
        $classId = $request->query('class_id');

        $students = RefStudent::query()
            ->when($classId, fn($q) => $q->whereHas('academicYears', fn($q) => $q->where('class_id', $classId)))
            ->whereHas('academicYears.class', fn($q) => $q->where('academic_level', 12))
            ->with([
                'user',
                'academicYears' => function ($q) use ($classId) {
                    $q->when($classId, fn($q) => $q->where('class_id', $classId))
                        ->with('class')
                        ->latest();
                },
            ])
            ->orderBy('full_name', 'asc')
            ->get()
            ->sortBy(fn($s) => $s->academicYears->first()?->class?->academic_level);

        $mapels = GoogleMapel::query()
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->get()
            ->groupBy('class_id');

        $fileName = 'Template_Kelulusan_' . now()->format('d-m-Y_His') . '.csv';
        $fp       = fopen('php://memory', 'w');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($fp, ['No', 'NIS', 'NISN', 'Nama Siswa', 'Kelas', 'Tapel (Tahun Pelajaran)', 'Id Mapel', 'Nama Mapel', 'Nilai'], ';');

        $no = 1;
        foreach ($students->values() as $student) {
            $latestAcademicYear = $student->academicYears->first();
            $studentClassId     = $latestAcademicYear?->class_id;
            $studentMapels      = $studentClassId ? ($mapels->get($studentClassId) ?? collect()) : collect();
            $kelasLabel         = ($latestAcademicYear?->class?->academic_level ?? '') . ' ' . ($latestAcademicYear?->class?->name ?? '');

            if ($studentMapels->isEmpty()) {
                fputcsv($fp, [$no++, $student->student_number ?? '', $student->national_student_number ?? '', $student->full_name, $kelasLabel, $latestAcademicYear?->academic_year ?? '', '', '', ''], ';');
                continue;
            }

            foreach ($studentMapels as $mapel) {
                fputcsv($fp, [$no++, $student->student_number ?? '', $student->national_student_number ?? '', $student->full_name, $kelasLabel, $latestAcademicYear?->academic_year ?? '', $mapel->uuid, $mapel->name, ''], ';');
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

            [$nameCol, $typeCol] = $this->parseMapelHeaders($rows[0]);

            $successCount = 0;
            $skipCount    = 0;
            $errorCount   = 0;
            $errors       = [];
            $mapelData    = $this->parseMapelRows(array_slice($rows, 1), $nameCol, $typeCol, $errors, $errorCount);

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

                            GoogleMapel::create(['class_id' => $classId, 'expertise_id' => $expertiseId, 'name' => $mapel['name'], 'type' => $mapel['type']]);
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
            [$nameCol, $typeCol] = $this->parseMapelHeaders($rows[0]);
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

                $mapelData[] = ['name' => $name, 'type' => $type, 'expertiseName' => $expertiseName, 'rowNumber' => $rowNumber];
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
                            GoogleMapel::create(['class_id' => $class->id, 'expertise_id' => null, 'name' => $mapel['name'], 'type' => 'umum']);
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
                            GoogleMapel::create(['class_id' => $class->id, 'expertise_id' => $expertiseId, 'name' => $mapel['name'], 'type' => 'jurusan']);
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
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.mimes'    => 'File harus berformat CSV atau TXT',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        try {
            \DB::beginTransaction();

            $file          = $request->file('file');
            $successCount  = 0;
            $skipCount     = 0;
            $errorCount    = 0;
            $errors        = [];
            $graduationMap = [];

            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) throw new \Exception('Gagal membuka file');

            // Skip BOM
            $bom = fread($handle, 3);
            if ($bom !== "\xef\xbb\xbf") rewind($handle);

            // Auto-detect delimiter
            $firstLine  = fgets($handle);
            if (!$firstLine) throw new \Exception('File kosong atau tidak valid');
            fseek($handle, $bom === "\xef\xbb\xbf" ? 3 : 0);

            $delimiters = [';' => 0, ',' => 0, "\t" => 0, '|' => 0];
            foreach ($delimiters as $delim => &$count) $count = substr_count($firstLine, $delim);
            unset($count);
            arsort($delimiters);
            $delimiter = array_key_first($delimiters);

            // Parse header
            $headers = fgetcsv($handle, 0, $delimiter);
            if (!$headers) throw new \Exception('File kosong atau tidak valid');
            $headers = array_map(fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $h))), $headers);

            $nisCol     = collect($headers)->search(fn($h) => str_contains($h, 'nis') && !str_contains($h, 'nisn'));
            $mapelIdCol = collect($headers)->search(fn($h) => str_contains($h, 'id mapel') || str_contains($h, 'id_mapel') || $h === 'id');
            $nilaiCol   = collect($headers)->search(fn($h) => str_contains($h, 'nilai'));

            if ($nisCol === false) throw new \Exception('Kolom NIS tidak ditemukan. Header: ' . implode(', ', $headers));
            if ($nilaiCol === false) throw new \Exception('Kolom Nilai tidak ditemukan. Header: ' . implode(', ', $headers));
            if ($mapelIdCol === false) throw new \Exception('Kolom Id Mapel tidak ditemukan. Header: ' . implode(', ', $headers));

            $rowNumber = 1;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                if (empty(array_filter($row, fn($v) => trim((string) $v) !== ''))) continue;

                $nis     = trim((string) ($row[$nisCol]     ?? ''));
                $nilai   = trim((string) ($row[$nilaiCol]   ?? ''));
                $mapelId = trim((string) ($row[$mapelIdCol] ?? ''));

                if ($nis === '' || $mapelId === '' || $nilai === '') {
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

                    $nilaiNumeric = (float) str_replace(',', '.', $nilai);
                    if ($nilaiNumeric < 0 || $nilaiNumeric > 100) {
                        $errors[] = "Baris $rowNumber: Nilai '$nilai' tidak valid (0–100)";
                        $errorCount++;
                        continue;
                    }

                    GoogleGraduationMapel::updateOrCreate(
                        ['graduation_id' => $graduationMap[$student->id]->uuid, 'mapel_id' => $mapel->uuid],
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

        if ($nameCol === false || $typeCol === false) {
            throw new \Exception('Kolom name/nama dan type/tipe/jenis harus ada di file');
        }

        return [$nameCol, $typeCol];
    }

    private function parseMapelRows(array $rows, int $nameCol, int $typeCol, array &$errors, int &$errorCount): array
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

            $mapelData[] = ['name' => $name, 'type' => $type, 'rowNumber' => $rowNumber];
        }

        return $mapelData;
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
