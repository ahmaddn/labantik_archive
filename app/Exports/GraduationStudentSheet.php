<?php

namespace App\Exports;

use App\Models\GoogleGraduationMapel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GraduationStudentSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    protected $student;
    protected $mapels;
    protected $templateType;

    public function __construct($student, $mapels, $templateType = 'transcript')
    {
        $this->student      = $student;
        $this->mapels       = $mapels;
        $this->templateType = $templateType;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->mapels;
    }

    public function title(): string
    {
        // Limit sheet title length (max 31 chars for Excel)
        $name = $this->student->full_name;
        return substr($name, 0, 31);
    }

    public function headings(): array
    {
        if ($this->templateType === 'graduation') {
            return ['Nama Siswa', 'Kelas', 'Id Mapel', 'Nama Mapel', 'NA (Nilai Akhir)', 'NIS'];
        }

        return [
            'Nama Siswa',
            'Kelas',
            'Id Mapel',
            'Nama Mapel',
            'S1',
            'S2',
            'S3',
            'S4',
            'S5',
            'S6',
            'NR',
            'NA',
            'NIS'
        ];
    }

    public function map($mapel): array
    {
        $latestAcademicYear = $this->student->academicYears->first();
        $kelasLabel         = ($latestAcademicYear?->class?->academic_level ?? '') . ' ' . ($latestAcademicYear?->class?->name ?? '');

        // Get existing scores if any
        $existing = GoogleGraduationMapel::where('graduation_id', function ($q) {
            $q->select('uuid')->from('google_graduation')->where('user_id', $this->student->id)->limit(1);
        })->where('mapel_id', $mapel->uuid)->first();

        if ($this->templateType === 'graduation') {
            return [
                $this->student->full_name,
                $kelasLabel,
                $mapel->uuid,
                $mapel->name,
                $existing?->score ?? '',
                $this->student->student_number ?? '',
            ];
        }

        return [
            $this->student->full_name,
            $kelasLabel,
            $mapel->uuid,
            $mapel->name,
            $existing?->sem_1 ?? '',
            $existing?->sem_2 ?? '',
            $existing?->sem_3 ?? '',
            $existing?->sem_4 ?? '',
            $existing?->sem_5 ?? '',
            $existing?->sem_6 ?? '',
            $existing?->nr ?? '',
            $existing?->score ?? '',
            $this->student->student_number ?? '',
        ];
    }
}
