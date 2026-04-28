<?php

namespace App\Http\Controllers;

use App\Models\StudentSignature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignatureController extends Controller
{
    /**
     * Simpan base64 signature ke tabel student_signatures.
     * POST /signature/store
     */
    public function store(Request $request)
    {
        $request->validate([
            'signature_data' => ['required', 'string', 'starts_with:data:image/png;base64,'],
        ]);

        // updateOrCreate — jika sudah pernah tanda tangan, timpa saja
        StudentSignature::updateOrCreate(
            ['user_id' => Auth::id()],
            ['signature_data' => $request->signature_data]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil disimpan.',
        ]);
    }

    /**
     * Tampilkan Transkrip Nilai.
     * GET /transkrip/{id}
     */
    public function showTranskrip($id)
    {
        $user = User::findOrFail($id);

        // Hanya pemilik atau superadmin yang boleh akses
        if (Auth::id() !== $user->id && Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        // Ambil signature dari tabel terpisah
        $signature = StudentSignature::where('user_id', $user->id)->first();

        if (!$signature) {
            return redirect()->back()->with('error', 'Selesaikan Surat Pernyataan terlebih dahulu.');
        }

        return view('kelulusan.pernyataan', compact('user', 'signature'));
    }
}
