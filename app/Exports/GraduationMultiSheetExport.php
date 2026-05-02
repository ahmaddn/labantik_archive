<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GraduationMultiSheetExport implements WithMultipleSheets
{
    protected $students;
    protected $mapels;
    protected $templateType;

    public function __construct($students, $mapels, $templateType = 'transcript')
    {
        $this->students     = $students;
        $this->mapels       = $mapels;
        $this->templateType = $templateType;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->students as $student) {
            $latestAcademicYear = $student->academicYears->first();
            $studentClassId     = $latestAcademicYear?->class_id;
            $studentMapels      = $studentClassId ? ($this->mapels->get($studentClassId) ?? collect()) : collect();

            if ($studentMapels->isNotEmpty()) {
                $sheets[] = new GraduationStudentSheet($student, $studentMapels, $this->templateType);
            }
        }

        return $sheets;
    }
}
