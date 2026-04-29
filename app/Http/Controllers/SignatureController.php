<?php

namespace App\Http\Controllers;

use App\Models\StudentSignature;
use App\Models\GoogleGraduation;
use App\Models\User;
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

        // Rekam ke google_statement (user_id = student, signature_id = signature yang baru disimpan)
        DB::table('google_statement')->updateOrInsert(
            ['user_id' => Auth::id()],
            [
                'uuid' => Str::uuid(),
                'signature_id' => $signature->id,
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Tanda tangan berhasil disimpan.']);
    }

    /**
     * Tampilkan Surat Pernyataan/Fakta Integritas (setelah sudah TTD)
     * GET /pernyataan/{id}
     */
    public function showPernyataan($id)
    {
        if (Auth::id() !== $id && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $userId = auth()->id();
        $user = DB::table('core_users')->where('id', $userId)->first();

        $student = DB::table('ref_students')->where('user_id', $userId)->first();

        // Ambil program keahlian lewat class_id dari user
        $program = DB::table('ref_classes')
            ->join('core_expertise_concentrations', 'ref_classes.expertise_concentration_id', '=', 'core_expertise_concentrations.id')
            ->where('ref_classes.id', $user->class_id)
            ->select('core_expertise_concentrations.name as program_name')
            ->first();


        $statement = DB::table('google_statement')->where('user_id', $id)->first();
        if (!$statement) {
            return redirect()->back()->with('error', 'Belum ada tanda tangan.');
        }

        $signature = StudentSignature::find($statement->signature_id);
        if (!$signature) {
            return redirect()->back()->with('error', 'Belum ada tanda tangan.');
        }

        return view('kelulusan.surat_pernyataan', compact('user', 'student', 'signature', 'program'));
    }

    /**
     * Tampilkan Surat Keterangan Lulus
     * GET /transkrip/{id}
     */
    public function showTranskrip($id)
    {
        if (Auth::id() !== $id && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $userId = auth()->id();
        $user = DB::table('core_users')->where('id', $userId)->first();

        $student = DB::table('ref_students')->where('user_id', $userId)->first();

        // Ambil program keahlian lewat class_id dari user
        $program = DB::table('ref_classes')
            ->join('core_expertise_concentrations', 'ref_classes.expertise_concentration_id', '=', 'core_expertise_concentrations.id')
            ->where('ref_classes.id', $user->class_id)
            ->select('core_expertise_concentrations.name as program_name')
            ->first();
        $program1 = DB::table('ref_classes')
            ->join('core_expertise_programs', 'ref_classes.expertise_program_id', '=', 'core_expertise_programs.id')
            ->where('ref_classes.id', $user->class_id)
            ->select('core_expertise_programs.name as program1_name')
            ->first();

        $statement = DB::table('google_statement')->where('user_id', $id)->first();
        if (!$statement) {
            return redirect()->back()->with('error', 'Belum ada tanda tangan.');
        }

        $signature = StudentSignature::find($statement->signature_id);
        if (!$signature) {
            return redirect()->back()->with('error', 'Belum ada tanda tangan.');
        }

        $graduation = GoogleGraduation::where('user_id', $id)->first();

        return view('kelulusan.kelulusan', compact('user', 'student', 'signature', 'graduation', 'program', 'program1'));
    }
}
