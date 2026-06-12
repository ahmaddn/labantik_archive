<?php

namespace App\Exports;

use App\Models\GoogleGraduation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IjazahTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $no = 1;

    public function collection()
    {
        return GoogleGraduation::with(['user.academicYears.class'])
            ->whereHas('user.academicYears', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })
            ->get()
            ->sortBy(function ($graduation) {
                $latestYear = $graduation->user->academicYears->first();
                $className = $latestYear?->class
                    ? $latestYear->class->academic_level . ' ' . $latestYear->class->name
                    : 'ZZZ';
                return $className . ' ' . ($graduation->user->full_name ?? '');
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'NIS / NISN',
            'KELAS',
            'NOMOR IJAZAH (KOSONGKAN JIKA BELUM ADA)'
        ];
    }

    public function map($graduation): array
    {
        $student = $graduation->user;
        $latestYear = $student->academicYears->first();
        $className = $latestYear?->class ? ($latestYear->class->academic_level . ' ' . $latestYear->class->name) : '-';
        
        $nis_nisn = $student->student_number . ' / ' . $student->national_student_number;

        return [
            $this->no++,
            $student->full_name,
            $nis_nisn,
            $className,
            $student->diploma_number
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');
        
        // Auto filter
        $sheet->setAutoFilter('A1:E1');

        // Protect specific columns to prevent accidental edit, optionally. We skip protection for now for simplicity.
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
