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

        $graduation = \App\Models\GoogleGraduation::with(['user.academicYears.class.expertiseProgram', 'user.academicYears.class.expertiseConcentration', 'letter', 'mapels.mapel'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student    = $graduation->user;
        $user       = auth()->user();
        $letter     = $graduation->letter;
        $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

        $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
        $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();

        $scores   = $mapelsData->filter(fn($m) => ($m->mapel->has_na ?? true) && $m->score !== null)->pluck('score');
        $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

        $latestAcademicYear = $student->academicYears->first();
        $program            = $latestAcademicYear?->class?->expertiseConcentration;
        $program1           = $latestAcademicYear?->class?->expertiseProgram;

        $signature = (object) ['signature_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='];
        $principal = $this->getPrincipal($letter->headmaster_id ?? null);

        $principal = $this->getPrincipal($letter->headmaster_id ?? null);
        $sigMode = request('sig_mode', 'both'); // none, sig, both

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
            'signature',
            'principal',
            'sigMode'
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

        $graduation = \App\Models\GoogleGraduation::with(['user.academicYears.class.expertiseConcentration', 'letter'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student            = $graduation->user;
        $user               = auth()->user();
        $latestAcademicYear = $student->academicYears->first();
        $program1           = $latestAcademicYear?->class?->expertiseConcentration;

        $statement = \App\Models\GoogleStatement::where('user_id', $student->id)->first();
        $signature = $statement?->signature ?? null;
        $principal = $this->getPrincipal($letter->headmaster_id ?? null);

        return view('admin.graduation.surat-pernyataan', compact(
            'graduation',
            'student',
            'user',
            'program1',
            'signature',
            'principal',
            'letter'
        ));
    }

    /**
     * Tampilkan transkrip nilai — 1 siswa atau semua (export)
     */
    public function showTranskripNilai($id)
    {
        if ($id === 'all') {
            return $this->transkripNilaiAll();
        }

        $graduation = \App\Models\GoogleGraduation::with(['user.academicYears.class.expertiseProgram', 'user.academicYears.class.expertiseConcentration', 'letter', 'transcriptLetter', 'mapels.mapel'])
            ->where('uuid', $id)
            ->firstOrFail();

        $student    = $graduation->user;
        $user       = auth()->user();
        $letter     = $graduation->transcriptLetter ?? $graduation->letter;
        $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

        $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
        $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();

        $scores   = $mapelsData->filter(fn($m) => ($m->mapel->has_na ?? true) && $m->score !== null)->pluck('score');
        $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

        $latestAcademicYear = $student->academicYears->first();
        $program            = $latestAcademicYear?->class?->expertiseConcentration;
        $program1           = $latestAcademicYear?->class?->expertiseProgram;
        $principal          = $this->getPrincipal($letter->headmaster_id ?? null);

        $principal          = $this->getPrincipal($letter->headmaster_id ?? null);
        $sigMode            = request('sig_mode', 'both');

        return view('admin.graduation.transkrip-nilai', compact(
            'graduation',
            'student',
            'user',
            'letter',
            'mapelUmum',
            'mapelJurusan',
            'rataRata',
            'program',
            'program1',
            'principal',
            'sigMode'
        ));
    }

    // =========================================================================
    // PRIVATE — export semua
    // =========================================================================

    private function getPrincipal($headmasterId = null)
    {
        if ($headmasterId) {
            $user = \App\Models\User::find($headmasterId);
        } else {
            $user = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('code', 'kepala-sekolah');
            })->first();
        }

        if ($user) {
            $employee = \App\Models\Employee::where('user_id', $user->id)->first();
            $user->setRelation('employee', $employee);
        }

        return $user;
    }

    private function suratKelulusanAll()
    {
        $classId     = request('class_id');
        $expertiseId = request('expertise_id');

        $graduations = \App\Models\GoogleGraduation::with(['user.academicYears.class', 'letter', 'mapels.mapel'])
            ->whereHas('user.academicYears.class', function ($q) use ($classId, $expertiseId) {
                $q->where('academic_level', 12);
                if ($classId)     $q->where('id', $classId);
                if ($expertiseId) $q->where('expertise_concentration_id', $expertiseId);
            })
            ->get()
            ->sortBy(function($g) {
                return ($g->user->academicYears->first()?->class?->name ?? '') . ' ' . ($g->user->full_name ?? '');
            });

        $data = [];
        foreach ($graduations as $graduation) {
            $student    = $graduation->user;
            $user       = auth()->user();
            $letter     = $graduation->letter;
            $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

            $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
            $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();

            $scores   = $mapelsData->filter(fn($m) => ($m->mapel->has_na ?? true) && $m->score !== null)->pluck('score');
            $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

            $latestAcademicYear = $student->academicYears->first();
            $program            = $latestAcademicYear?->class?->expertiseConcentration;
            $program1           = $latestAcademicYear?->class?->expertiseProgram;
            $signature          = (object) ['signature_data' => null];
            $principal          = $this->getPrincipal($letter->headmaster_id ?? null);

            $data[] = (object) compact('graduation', 'student', 'user', 'letter', 'mapelUmum', 'mapelJurusan', 'rataRata', 'program', 'program1', 'signature', 'principal');
        }

        $sigMode = request('sig_mode', 'both');

        return view('admin.graduation.surat-kelulusan-all', compact('data', 'sigMode'));
    }

    private function suratPernyataanAll()
    {
        $classId     = request('class_id');
        $expertiseId = request('expertise_id');

        $graduations = \App\Models\GoogleGraduation::with(['user.academicYears.class', 'letter'])
            ->whereHas('user.academicYears.class', function ($q) use ($classId, $expertiseId) {
                $q->where('academic_level', 12);
                if ($classId)     $q->where('id', $classId);
                if ($expertiseId) $q->where('expertise_concentration_id', $expertiseId);
            })
            ->get()
            ->sortBy(function($g) {
                return ($g->user->academicYears->first()?->class?->name ?? '') . ' ' . ($g->user->full_name ?? '');
            });

        $data = [];
        foreach ($graduations as $graduation) {
            $student            = $graduation->user;
            $user               = auth()->user();
            $latestAcademicYear = $student->academicYears->first();
            $program1           = $latestAcademicYear?->class?->expertiseConcentration;

            $statement = \App\Models\GoogleStatement::where('user_id', $student->id)->first();
            $signature = $statement?->signature ?? null;
            $principal = $this->getPrincipal($graduation->letter->headmaster_id ?? null);

            $data[] = (object) compact('graduation', 'student', 'user', 'program1', 'signature', 'principal');
        }

        return view('admin.graduation.surat-pernyataan-all', compact('data'));
    }
    
    private function transkripNilaiAll()
    {
        $classId     = request('class_id');
        $expertiseId = request('expertise_id');

        $graduations = \App\Models\GoogleGraduation::with(['user.academicYears.class.expertiseProgram', 'user.academicYears.class.expertiseConcentration', 'letter', 'transcriptLetter', 'mapels.mapel'])
            ->whereHas('user.academicYears.class', function ($q) use ($classId, $expertiseId) {
                $q->where('academic_level', 12);
                if ($classId)     $q->where('id', $classId);
                if ($expertiseId) $q->where('expertise_concentration_id', $expertiseId);
            })
            ->get()
            ->sortBy(function($g) {
                return ($g->user->academicYears->first()?->class?->name ?? '') . ' ' . ($g->user->full_name ?? '');
            });

        $data = [];
        foreach ($graduations as $graduation) {
            $student    = $graduation->user;
            $user       = auth()->user();
            $letter     = $graduation->transcriptLetter ?? $graduation->letter;
            $mapelsData = $graduation->mapels()->with('mapel')->orderBy('mapel_id')->get();

            $mapelUmum    = $mapelsData->filter(fn($m) => $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
            $mapelJurusan = $mapelsData->filter(fn($m) => $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();

            $scores   = $mapelsData->filter(fn($m) => ($m->mapel->has_na ?? true) && $m->score !== null)->pluck('score');
            $rataRata = $scores->isNotEmpty() ? number_format($scores->avg(), 2) : '';

            $latestAcademicYear = $student->academicYears->first();
            $program            = $latestAcademicYear?->class?->expertiseConcentration;
            $program1           = $latestAcademicYear?->class?->expertiseProgram;
            $principal          = $this->getPrincipal($letter->headmaster_id ?? null);

            $data[] = (object) compact('graduation', 'student', 'user', 'letter', 'mapelUmum', 'mapelJurusan', 'rataRata', 'program', 'program1', 'principal');
        }

        $sigMode = request('sig_mode', 'both');

        return view('admin.graduation.transkrip-nilai-all', compact('data', 'principal', 'sigMode'));
    }
}
