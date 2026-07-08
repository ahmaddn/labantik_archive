<?php

namespace App\Imports;

use App\Models\RefStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class IjazahImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private $checkedHeaders = false;
    private $nisKey = null;
    private $nisnKey = null;
    private $namaKey = null;
    private $ijazahKey = null;

    // Debugging properties
    public $processedCount = 0;
    public $updatedCount = 0;
    public $failedRows = [];

    public function model(array $row)
    {
        if (!$this->checkedHeaders) {
            foreach ($row as $key => $value) {
                $normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $key));
                
                // Specific matches first
                if ($normalized === 'nis' || $normalized === 'nisnisn') {
                    $this->nisKey = $key;
                } elseif ($normalized === 'nisn') {
                    $this->nisnKey = $key;
                } elseif ($normalized === 'namalengkap' || $normalized === 'nama') {
                    $this->namaKey = $key;
                } elseif (str_contains($normalized, 'ijazah')) {
                    $this->ijazahKey = $key;
                }
            }

            // Substring/fallback searches if not found yet
            foreach ($row as $key => $value) {
                $normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $key));
                
                if (!$this->nisKey && str_contains($normalized, 'nis') && $normalized !== 'nisn') {
                    $this->nisKey = $key;
                }
                if (!$this->namaKey && (str_contains($normalized, 'nama') || str_contains($normalized, 'lengkap'))) {
                    $this->namaKey = $key;
                }
                if (!$this->ijazahKey && str_contains($normalized, 'ijazah')) {
                    $this->ijazahKey = $key;
                }
            }

            // If we only have NISN but no separate NIS, assign it
            if (!$this->nisKey) {
                $this->nisKey = $this->nisnKey;
            }

            if (!$this->nisKey) {
                throw new \Exception("Kolom NIS / NISN tidak ditemukan dalam file Excel. Harap gunakan template yang diunduh.");
            }
            if (!$this->ijazahKey) {
                throw new \Exception("Kolom Nomor Ijazah tidak ditemukan dalam file Excel. Harap gunakan template yang diunduh.");
            }

            $this->checkedHeaders = true;
        }

        $nisVal = null;
        if ($this->nisKey) {
            $nisVal = trim((string)($row[$this->nisKey] ?? ''));
        }
        $nisnVal = null;
        if ($this->nisnKey) {
            $nisnVal = trim((string)($row[$this->nisnKey] ?? ''));
        }
        $fullName = null;
        if ($this->namaKey) {
            $fullName = trim((string)($row[$this->namaKey] ?? ''));
        }

        $diplomaNumber = trim((string)($row[$this->ijazahKey] ?? ''));

        if ($diplomaNumber === '') {
            return null; // Skip if no ijazah number to update
        }

        $this->processedCount++;

        // Extract NIS and NISN from nisVal if it contains a slash /
        $nis = '';
        $nisn = '';
        if (str_contains($nisVal, '/')) {
            $parts = explode('/', str_replace(' ', '', $nisVal));
            $nis = $parts[0] ?? '';
            $nisn = $parts[1] ?? '';
        } else {
            $nis = $nisVal;
            $nisn = $nisnVal;
        }

        // Clean up Excel numeric formatting
        $nis = $this->sanitizeStudentNumber($nis);
        $nisn = $this->sanitizeStudentNumber($nisn);

        if (empty($nis) && empty($nisn) && empty($fullName)) {
            $this->failedRows[] = "Baris Kosong / Tanpa Identitas (Ijazah: {$diplomaNumber})";
            return null;
        }

        $student = null;

        // Gather all search candidates to match against database columns
        $candidates = [];
        if (!empty($nis)) {
            $candidates[] = ['column' => 'student_number', 'value' => $nis];
            $candidates[] = ['column' => 'national_student_number', 'value' => $nis];
            if (is_numeric($nis) && strlen($nis) < 10) {
                $candidates[] = ['column' => 'national_student_number', 'value' => str_pad($nis, 10, '0', STR_PAD_LEFT)];
            }
        }
        if (!empty($nisn)) {
            $candidates[] = ['column' => 'national_student_number', 'value' => $nisn];
            $candidates[] = ['column' => 'student_number', 'value' => $nisn];
            if (is_numeric($nisn) && strlen($nisn) < 10) {
                $candidates[] = ['column' => 'national_student_number', 'value' => str_pad($nisn, 10, '0', STR_PAD_LEFT)];
            }
        }

        // 1. Try finding the student using the NIS/NISN candidates
        foreach ($candidates as $candidate) {
            $student = RefStudent::where($candidate['column'], $candidate['value'])->first();
            if ($student) {
                break;
            }
        }

        // 2. Fallback: Find by exact Name (case-insensitive) if NIS/NISN match failed
        if (!$student && !empty($fullName)) {
            $student = RefStudent::where('full_name', $fullName)->first();
            if (!$student) {
                $student = RefStudent::whereRaw('LOWER(full_name) = ?', [strtolower($fullName)])->first();
            }
        }

        if ($student) {
            $student->update([
                'diploma_number' => $diplomaNumber
            ]);
            $this->updatedCount++;
        } else {
            $this->failedRows[] = ($fullName ?: 'Tanpa Nama') . " (NIS/NISN: " . ($nis ?: ($nisn ?: 'N/A')) . ")";
        }

        return null;
    }

    private function sanitizeStudentNumber($number)
    {
        // Remove trailing .0 if present from Excel float parsing
        if (str_ends_with($number, '.0')) {
            $number = substr($number, 0, -2);
        }
        return $number;
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
