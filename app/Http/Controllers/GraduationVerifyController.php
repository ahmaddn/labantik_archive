<?php

namespace App\Http\Controllers;

use App\Models\GoogleGraduation;
use Illuminate\Support\Facades\DB;

class GraduationVerifyController extends Controller
{
    /**
     * Halaman verifikasi dokumen kelulusan — publik, tanpa login.
     */
    public function show(string $uuid)
    {
        $graduation = GoogleGraduation::with([
            'user.academicYears.class.expertiseProgram',
            'user.academicYears.class.expertiseConcentration',
            'letter',
        ])->where('uuid', $uuid)->first();

        if (!$graduation) {
            abort(404, 'Dokumen tidak ditemukan atau tautan tidak valid.');
        }

        $student = $graduation->user;

        $latestAcademicYear = $student?->academicYears?->first();
        $program  = $latestAcademicYear?->class?->expertiseConcentration;
        $program1 = $latestAcademicYear?->class?->expertiseProgram;
        $letter   = $graduation->letter;

        return view('kelulusan.verify', compact(
            'graduation',
            'student',
            'letter',
            'program',
            'program1'
        ));
    }
}
