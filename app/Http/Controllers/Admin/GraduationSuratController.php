<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleGraduation;
use Illuminate\Http\Request;

class GraduationSuratController extends Controller
{
    /**
     * Tampilkan surat kelulusan — 1 siswa atau semua (export)
     */
    public function showSuratKelulusan($id)
    {
        if ($id === 'all') {
            return $this->suratKelulusanAll();
        }

        $graduation = GoogleGraduation::with(['user', 'letter', 'mapels.mapel'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student    = $graduation->user;
        $user       = auth()->user();
        $letter     = $graduation->letter;
        $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

        $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? 999)->values();
        $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? 999)->values();

        $scores   = $mapelsData->whereNotNull('score')->pluck('score');
        $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

        $latestAcademicYear = $student->academicYears->first();
        $program            = $latestAcademicYear?->class?->expertiseConcentration;
        $program1           = $latestAcademicYear?->class?->expertiseProgram;

        $signature = (object) ['signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='];

        return view('admin.graduation.surat-kelulusan', compact(
            'graduation',
            'student',
            'user',
            'letter',
            'mapelUmum',
            'mapelJurusan',
            'rataRata',
            'program',
            'program1',
            'signature'
        ));
    }

    /**
     * Tampilkan surat pernyataan — 1 siswa atau semua (export)
     */
    public function showSuratPernyataan($id)
    {
        if ($id === 'all') {
            return $this->suratPernyataanAll();
        }

        $graduation = GoogleGraduation::with(['user', 'letter'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student            = $graduation->user;
        $user               = auth()->user();
        $latestAcademicYear = $student->academicYears->first();
        $program1           = $latestAcademicYear?->class?->expertiseConcentration;

        $statement = \App\Models\GoogleStatement::where('user_id', $student->id)->first();
        $signature = $statement?->signature ?? null;

        return view('admin.graduation.surat-pernyataan', compact(
            'graduation',
            'student',
            'user',
            'program1',
            'signature'
        ));
    }

    // =========================================================================
    // PRIVATE — export semua
    // =========================================================================

    private function suratKelulusanAll()
    {
        $classId     = request('class_id');
        $expertiseId = request('expertise_id');

        $graduations = GoogleGraduation::with(['user', 'letter', 'mapels.mapel'])
            ->whereHas('user.academicYears.class', function ($q) use ($classId, $expertiseId) {
                $q->where('academic_level', 12);
                if ($classId)     $q->where('id', $classId);
                if ($expertiseId) $q->where('expertise_concentration_id', $expertiseId);
            })
            ->get();

        $data = [];
        foreach ($graduations as $graduation) {
            $student    = $graduation->user;
            $user       = auth()->user();
            $letter     = $graduation->letter;
            $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

            $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? 999)->values();
            $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? 999)->values();

            $scores   = $mapelsData->whereNotNull('score')->pluck('score');
            $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

            $latestAcademicYear = $student->academicYears->first();
            $program            = $latestAcademicYear?->class?->expertiseConcentration;
            $program1           = $latestAcademicYear?->class?->expertiseProgram;
            $signature          = (object) ['signature_data' => null];

            $data[] = (object) compact('graduation', 'student', 'user', 'letter', 'mapelUmum', 'mapelJurusan', 'rataRata', 'program', 'program1', 'signature');
        }

        return view('admin.graduation.surat-kelulusan-all', compact('data'));
    }

    private function suratPernyataanAll()
    {
        $classId     = request('class_id');
        $expertiseId = request('expertise_id');

        $graduations = GoogleGraduation::with(['user', 'letter'])
            ->whereHas('user.academicYears.class', function ($q) use ($classId, $expertiseId) {
                $q->where('academic_level', 12);
                if ($classId)     $q->where('id', $classId);
                if ($expertiseId) $q->where('expertise_concentration_id', $expertiseId);
            })
            ->get();

        $data = [];
        foreach ($graduations as $graduation) {
            $student            = $graduation->user;
            $user               = auth()->user();
            $latestAcademicYear = $student->academicYears->first();
            $program1           = $latestAcademicYear?->class?->expertiseConcentration;

            $statement = \App\Models\GoogleStatement::where('user_id', $student->id)->first();
            $signature = $statement?->signature ?? null;

            $data[] = (object) compact('graduation', 'student', 'user', 'program1', 'signature');
        }

        return view('admin.graduation.surat-pernyataan-all', compact('data'));
    }
}
