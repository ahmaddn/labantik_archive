<?php

namespace App\Http\Controllers;

use App\Models\GoogleToken;
use App\Services\GoogleDriveAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminGoogleController extends Controller
{
    public function __construct(
        protected GoogleDriveAdminService $driveService
    ) {}

    public function showConnect(): View
    {
        $isConnected = GoogleToken::where('type', 'admin')->exists();
        return view('admin.google-connect', compact('isConnected'));
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return redirect($this->driveService->getAuthUrl());
    }

    public function handleCallback(): RedirectResponse
    {
        $code = request()->get('code');

        if (!$code) {
            return redirect()->route('admin.google.connect')
                ->with('error', 'Otorisasi Google dibatalkan.');
        }

        try {
            $token = $this->driveService->handleCallback($code);

            // Simpan/update token (selalu satu record admin)
            GoogleToken::updateOrCreate(
                ['type' => 'admin'],
                [
                    'access_token'     => $token['access_token'],
                    'refresh_token'    => $token['refresh_token'] ?? GoogleToken::where('type','admin')->value('refresh_token'),
                    'expires_in'       => $token['expires_in'],
                    'token_created_at' => now(),
                    'created_by'       => auth()->id(),
                    'updated_by'       => auth()->id(),
                ]
            );

            return redirect()->route('admin.google.connect')
                ->with('success', '✅ Google Drive berhasil terhubung!');

        } catch (\Exception $e) {
            return redirect()->route('admin.google.connect')
                ->with('error', 'Gagal connect: ' . $e->getMessage());
        }
    }

    public function disconnect(): RedirectResponse
    {
        GoogleToken::where('type', 'admin')->delete();

        return redirect()->route('admin.google.connect')
            ->with('success', 'Google Drive berhasil diputuskan.');
    }
}
