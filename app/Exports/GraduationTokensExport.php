<?php

namespace App\Exports;

use App\Models\RefStudent;
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
        // Ambil semua siswa kelas 12, meskipun belum ada di tabel google_graduation
        return RefStudent::with(['academicYears.class', 'user', 'graduation'])
            ->whereHas('academicYears.class', function ($q) {
                $q->where('academic_level', 12);
            })->get();
    }

    public function map($student): array
    {
        $latestYear = $student->academicYears->first();
        $className = $latestYear?->class
            ? $latestYear->class->academic_level . ' ' . $latestYear->class->name
            : '-';

        // Password default sesuai permintaan user: 12345678 jika ada email
        $password = $student->user && $student->user->email ? '12345678' : '-';

        return [
            $student->full_name ?? 'User Terhapus',
            $student->user?->email ?? '-',
            $password,
            $className,
            $student->graduation->token ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Email (Login)',
            'Password (Default)',
            'Kelas',
            'Token',
        ];
    }
}
