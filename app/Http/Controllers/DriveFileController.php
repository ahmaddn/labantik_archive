<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveFile;
use App\Models\GoogleToken;
use App\Services\GoogleDriveAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriveFileController extends Controller
{
    public function __construct(
        protected GoogleDriveAdminService $driveService
    ) {}

    public function index(): View
    {
        $userId = auth()->id();

        $files = GoogleDriveFile::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        $isConnected = GoogleToken::where('type', 'admin')->exists();

        // Kuota: 5MB per user
        $quotaLimit = 5 * 1024 * 1024;
        $usedBytes = (int) GoogleDriveFile::where('user_id', $userId)->sum('size');
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        return view('drive.index', compact('files', 'isConnected', 'quotaLimit', 'usedBytes', 'remainingBytes'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            // Hanya PDF, maksimal 1MB per file
            'file' => 'required|file|mimes:pdf|max:1024', // 1MB (1024 KB)
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Hanya file PDF yang diperbolehkan.',
            'file.max'      => 'Ukuran file tidak boleh lebih dari 1 MB.',
        ]);

        if (!GoogleToken::where('type', 'admin')->exists()) {
            return back()->with('error', 'Google Drive belum terhubung. Hubungi Super Admin.');
        }

        try {
            $uploadedFile = $request->file('file');

            // Cek kuota per-user: maksimal total 5MB (5 * 1024 * 1024 bytes)
            $currentTotal = (int) GoogleDriveFile::where('user_id', auth()->id())->sum('size');
            $newSize = (int) ($uploadedFile->getSize() ?? 0);
            $quotaLimit = 5 * 1024 * 1024;

            if (($currentTotal + $newSize) > $quotaLimit) {
                $remaining = max(0, $quotaLimit - $currentTotal);
                $remainingMb = round($remaining / (1024 * 1024), 2);
                return back()->with('error', 'Kuota upload Anda melebihi batas 5 MB. Sisa kuota: ' . $remainingMb . ' MB. Hapus file lama atau hubungi admin.');
            }

            // Build filename: {user}_{year}_#{sym}_{originalName}
            $user = auth()->user();
            $originalName = $uploadedFile->getClientOriginalName();

            // Sanitize user name and original file name to safe characters
            $sanitizedUser = preg_replace('/[^A-Za-z0-9-_]/', '_', $user->name ?: 'user');
            $sanitizedOriginal = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);

            $year = date('Y');

            $fileName = sprintf('%s_%s_#%s', $sanitizedUser, $sanitizedOriginal, $year);

            // Pastikan folder tahun ada, lalu upload ke folder tersebut
            $folderId = $this->driveService->ensureYearFolder($year);
            $uploaded = $this->driveService->uploadFile($uploadedFile, $fileName, $folderId);

            // Simpan metadata ke database
            GoogleDriveFile::create([
                'user_id'          => auth()->id(),
                'google_file_id'   => $uploaded['google_file_id'],
                'name'             => $uploaded['name'],
                'mime_type'        => $uploaded['mime_type'],
                'size'             => $uploaded['size'],
                'web_view_link'    => $uploaded['web_view_link'],
                'web_content_link' => $uploaded['web_content_link'],
            ]);

            return back()->with('success', '✅ File "' . $uploaded['name'] . '" berhasil diupload!');
        } catch (\Exception $e) {
            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $file = GoogleDriveFile::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $this->driveService->deleteFile($file->google_file_id);
            $file->delete();

            return back()->with('success', 'File "' . $file->name . '" berhasil dihapus.');
        } catch (\Exception $e) {
            // Tetap hapus dari DB meski gagal di Drive
            $file->delete();
            return back()->with('warning', 'File dihapus dari database, tapi gagal dihapus di Drive: ' . $e->getMessage());
        }
    }
}
