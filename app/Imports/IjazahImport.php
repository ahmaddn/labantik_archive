<?php

namespace App\Imports;

use App\Models\RefStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class IjazahImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        // Mapping kolom: 
        // no
        // nama_lengkap
        // nis_nisn
        // kelas
        // nomor_ijazah_kosongkan_jika_belum_ada
        
        if (!isset($row['nis_nisn'])) {
            return null; // Skip if no NIS
        }

        $nisParts = explode(' / ', $row['nis_nisn']);
        $nis = trim($nisParts[0]);

        $diplomaNumber = null;
        if (isset($row['nomor_ijazah_kosongkan_jika_belum_ada'])) {
            $diplomaNumber = trim((string)$row['nomor_ijazah_kosongkan_jika_belum_ada']);
        } else if (isset($row['nomor_ijazah'])) {
             $diplomaNumber = trim((string)$row['nomor_ijazah']);
        }

        if (empty($nis) || $diplomaNumber === null || $diplomaNumber === '') {
            return null;
        }

        $student = RefStudent::where('student_number', $nis)->first();
        if ($student) {
            $student->update([
                'diploma_number' => $diplomaNumber
            ]);
        }

        return null; // Return null because we updated manually
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
