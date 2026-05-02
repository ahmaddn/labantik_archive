<?php

namespace App\Http\Controllers;

use App\Models\StudentSignature;
use App\Models\GoogleGraduation;
use App\Models\GoogleStatement;
use App\Models\User;
use App\Models\GoogleGraduationMapel;
use App\Models\RefClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'signature_data' => ['required', 'string', 'starts_with:data:image/png;base64,'],
        ]);

        $signature = StudentSignature::updateOrCreate(
            ['id' => Auth::id()],
            ['signature_data' => $request->signature_data]
        );

        DB::table('google_statement')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'uuid'         => Str::uuid(),
                'signature_id' => $signature->id,
                'updated_at'   => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tanda tangan berhasil disimpan.']);
    }

    /**
     * Track setiap kali siswa menekan tombol Print.
     * Increment print_count di google_statement untuk user yang sedang login.
     */
    public function trackPrint(Request $request)
    {
        $userId = Auth::id();

        DB::table('google_statement')
            ->where('user_id', $userId)
            ->update([
                'print_count' => DB::raw('print_count + 1'),
                'last_print_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function showTranskrip($id)
    {
        if (Auth::id() !== $id && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        // ── Resolve student dari user_id ──────────────────────────────────────
        // ref_students.user_id = users.id
        $student = \App\Models\RefStudent::where('user_id', $id)->first();

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan');
        }

        // ── Tanda tangan ──────────────────────────────────────────────────────
        $statement = DB::table('google_statement')->where('user_id', $id)->first();
        $signature = $statement?->signature_id
            ? StudentSignature::find($statement->signature_id)
            : null;

        // ── Graduation pakai ref_students.id, bukan users.id ─────────────────
        $graduation = GoogleGraduation::with([
            'user',
            'mapels.mapel.class',
            'mapels.mapel.expertise',
            'letter',
        ])->where('user_id', $student->id)->first();

        $letter = $graduation?->letter;

        // ── Program Keahlian & Konsentrasi via academic year ──────────────────
        // ref_student_academic_years.student_id = ref_students.id
        $latestAcademicYear = \App\Models\RefStudentAcademicYear::with([
            'class.expertiseConcentration',
            'class.expertiseProgram',
        ])
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        $refClass = $latestAcademicYear?->class;
        $program  = $refClass
            ? (object) ['program_name'  => $refClass->expertiseConcentration?->name ?? '-']
            : null;
        $program1 = $refClass
            ? (object) ['program1_name' => $refClass->expertiseProgram?->name ?? '-']
            : null;

        // ── Mapel umum & jurusan ──────────────────────────────────────────────
        $mapelUmum    = [];
        $mapelJurusan = [];
        $rataRata     = null;

        if ($graduation && $graduation->mapels) {
            foreach ($graduation->mapels as $graduationMapel) {
                if (!$graduationMapel->mapel) continue;

                $item = (object) [
                    'uuid'  => $graduationMapel->mapel->uuid,
                    'name'  => $graduationMapel->mapel->name,
                    'type'  => $graduationMapel->mapel->type,
                    'score' => $graduationMapel->score,
                    'order' => $graduationMapel->order,
                    'join'  => $graduationMapel->join,
                ];

                if ($graduationMapel->mapel->type === 'umum') {
                    $mapelUmum[] = $item;
                } elseif ($graduationMapel->mapel->type === 'jurusan') {
                    $mapelJurusan[] = $item;
                }
            }

            usort($mapelUmum,    fn($a, $b) => $a->order <=> $b->order);
            usort($mapelJurusan, fn($a, $b) => $a->order <=> $b->order);

            $scores = array_filter(
                array_map(fn($m) => $m->score, array_merge($mapelUmum, $mapelJurusan)),
                fn($v) => $v !== null
            );

            if (!empty($scores)) {
                $rataRata = number_format(array_sum($scores) / count($scores), 2);
            }
        } // end if graduation && mapels

        $transkripUmum = collect();
        $transkripJurusan = collect();
        if ($graduation) {
            $transkripUmum = $graduation->mapels->filter(fn($m) => $m->mapel && $m->mapel->type === 'umum')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
            $transkripJurusan = $graduation->mapels->filter(fn($m) => $m->mapel && $m->mapel->type === 'jurusan')->sortBy(fn($m) => $m->mapel->order ?? '-')->values();
        }

        return view('kelulusan.kelulusan', compact(
            'user',
            'student',
            'signature',
            'graduation',
            'letter',
            'program',
            'program1',
            'mapelUmum',
            'mapelJurusan',
            'rataRata',
            'transkripUmum',
            'transkripJurusan'
        ));
    }
}