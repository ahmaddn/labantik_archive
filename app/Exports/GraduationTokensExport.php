<?php

namespace App\Exports;

use App\Models\GoogleGraduation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GraduationTokensExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return GoogleGraduation::with('user.academicYears.class')
            ->whereHas('user.academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })->get();
    }

    public function map($graduation): array
    {
        $latestYear = $graduation->user->academicYears->first();
        $className = $latestYear?->class
            ? $latestYear->class->academic_level . ' ' . $latestYear->class->name
            : '-';

        return [
            $graduation->user->full_name ?? 'User Terhapus',
            $className,
            $graduation->token ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'Token',
        ];
    }
}
