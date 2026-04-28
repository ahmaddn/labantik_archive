<?php

namespace App\Http\Controllers;

use App\Models\StudentSignature;
use App\Models\GoogleGraduation;
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

        // Check if user has graduation record (moved to google_graduation)
        $hasGraduation = GoogleGraduation::where('user_id', Auth::id())->exists();

        if (!$hasGraduation) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki data kelulusan.',
            ], 422);
        }

        // Save signature data with a unique identifier based on user
        StudentSignature::updateOrCreate(
            ['id' => Auth::id()],
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

        // Check if user has graduation record (user_id moved to google_graduation)
        $hasGraduation = GoogleGraduation::where('user_id', $id)->exists();

        if (!$hasGraduation) {
            return redirect()->back()->with('error', 'Selesaikan Surat Pernyataan terlebih dahulu.');
        }

        // Ambil signature dari tabel terpisah
        $signature = StudentSignature::find($id);

        if (!$signature) {
            return redirect()->back()->with('error', 'Selesaikan Surat Pernyataan terlebih dahulu.');
        }

        return view('kelulusan.pernyataan', compact('user', 'signature'));
    }
}
