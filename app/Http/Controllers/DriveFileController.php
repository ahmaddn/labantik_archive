<?php

namespace App\Http\Controllers;

use App\Models\ExpertiseConcentration;
use App\Models\GoogleDriveCategory;
use App\Models\GoogleDriveFile;
use App\Models\GoogleFileLog;
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

    public function index()
    {
        $user = auth()->user();

        // Ambil semua file milik user, eager load relasi
        $allFiles = GoogleDriveFile::where('user_id', $user->id)
            ->with(['category', 'expertise', 'logs.subCategory'])
            ->latest()
            ->get();

        // Kelompokkan per kategori
        $filesByCategory = $allFiles
            ->groupBy(fn($f) => $f->google_category_id ?? 'uncategorized')
            ->map(function ($files, $catId) {
                $category = $files->first()->category
                    ?? (object)['id' => null, 'name' => 'Tanpa Kategori', 'slug' => 'uncategorized'];
                return [
                    'category' => $category,
                    'files'    => $files,
                ];
            })
            ->values();

        // Tetap kirim $files (paginasi) jika diperlukan fallback
        $files = GoogleDriveFile::where('user_id', $user->id)
            ->with(['category', 'expertise', 'logs.subCategory'])
            ->latest()
            ->paginate(15);

        // Cek koneksi Google Drive
        $isConnected = GoogleToken::where('type', 'admin')->exists();

        // Kuota: 100MB per user
        $quotaLimit = 100 * 1024 * 1024;
        $usedBytes = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        return view('drive.index', compact(
            'files',
            'filesByCategory',
            'isConnected',
            'quotaLimit',
            'remainingBytes'
        ));
    }

    public function create(): View
    {
        $categories = GoogleDriveCategory::with('subCategories.options')->orderBy('name')->get();
        $expertises = ExpertiseConcentration::orderBy('name')->get();
        $isConnected = GoogleToken::where('type', 'admin')->exists();

        // Kuota
        $quotaLimit = 100 * 1024 * 1024;
        $usedBytes = (int) GoogleDriveFile::where('user_id', auth()->id())->sum('size');
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        $currentYear = date('Y');
        $yearRange = range($currentYear - 50, $currentYear + 1);

        return view('drive.create', compact('categories', 'expertises', 'isConnected', 'remainingBytes', 'yearRange', 'currentYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'document_name'              => 'required|string|max:255',
            'file'                       => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'google_category_id'         => 'required|exists:google_drive_categories,id',
            'sub_category_selections'    => 'nullable|array',
            'sub_category_selections.*'  => 'nullable|string|max:255',
            'expertise_id'               => 'nullable|exists:core_expertise_concentrations,id',
            'year'                       => 'nullable|numeric|digits:4|min:1900|max:' . (date('Y') + 1),
        ], [
            'document_name.required'     => 'Nama dokumen wajib diisi.',
            'file.required'              => 'File wajib diunggah.',
            'file.mimes'                 => 'Hanya file PDF atau gambar yang diperbolehkan.',
            'file.max'                   => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'google_category_id.required' => 'Kategori wajib dipilih.',
            'google_category_id.exists'  => 'Kategori tidak valid.',
            'year.numeric'               => 'Tahun harus berupa angka.',
            'year.digits'                => 'Tahun harus terdiri dari 4 digit.',
            'year.min'                   => 'Tahun tidak boleh lebih kecil dari 1900.',
            'year.max'                   => 'Tahun tidak boleh melebihi ' . (date('Y') + 1) . '.',
        ]);

        if (!GoogleToken::where('type', 'admin')->exists()) {
            return back()->with('error', 'Google Drive belum terhubung. Hubungi Super Admin.');
        }

        try {
            $uploadedFile = $request->file('file');

            // Cek kuota per-user: maksimal total 100MB
            $currentTotal = (int) GoogleDriveFile::where('user_id', auth()->id())->sum('size');
            $newSize      = (int) ($uploadedFile->getSize() ?? 0);
            $quotaLimit   = 100 * 1024 * 1024;

            if (($currentTotal + $newSize) > $quotaLimit) {
                $remaining   = max(0, $quotaLimit - $currentTotal);
                $remainingMb = round($remaining / (1024 * 1024), 2);
                return back()->with('error', 'Kuota upload Anda melebihi batas. Sisa kuota: ' . $remainingMb . ' MB.');
            }

            // Build filename untuk Google Drive
            $user            = auth()->user();
            $originalName    = $uploadedFile->getClientOriginalName();
            $sanitizedOriginal = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
            $year            = date('Y');
            $fileName        = $sanitizedOriginal;

            // Upload ke Google Drive
            $folderId = $this->driveService->ensureYearFolder($year);
            $uploaded = $this->driveService->uploadFile($uploadedFile, $fileName, $folderId);

            // Tentukan sub_category_id utama (untuk kolom legacy, ambil sub-cat pertama yang dipilih)
            $primarySubCatId = null;
            $selections = $request->input('sub_category_selections', []);
            foreach ($selections as $subCatId => $optionValue) {
                if (!empty($optionValue)) {
                    $primarySubCatId = $subCatId;
                    break;
                }
            }

            // Simpan metadata file ke database
            $driveFile = GoogleDriveFile::create([
                'user_id'                     => auth()->id(),
                'document_name'               => $request->document_name,
                'google_category_id'          => $request->google_category_id,
                'google_drive_sub_category_id' => $primarySubCatId, // kolom legacy: sub-cat pertama
                'expertise_id'                => $request->expertise_id,
                'year'                        => $request->year,
                'google_file_id'              => $uploaded['google_file_id'],
                'name'                        => $uploaded['name'],
                'mime_type'                   => $uploaded['mime_type'],
                'size'                        => $uploaded['size'],
                'web_view_link'               => $uploaded['web_view_link'],
                'web_content_link'            => $uploaded['web_content_link'],
            ]);

            // Simpan setiap sub-kategori yang dipilih ke google_file_logs
            $logCount = 0;
            foreach ($selections as $subCatId => $optionValue) {
                if (!empty($optionValue)) {
                    GoogleFileLog::create([
                        'google_drive_file_id'        => $driveFile->id,
                        'google_drive_sub_category_id' => $subCatId,
                        'sub_category_option'         => $optionValue,
                    ]);
                    $logCount++;
                }
            }

            $logMsg = $logCount > 0 ? " dengan {$logCount} sub-kategori." : '.';
            return redirect()->route('drive.index')
                ->with('success', 'File "' . $request->document_name . '" berhasil diupload' . $logMsg);
        } catch (\Exception $e) {
            return back()->with('error', 'Upload gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $file = GoogleDriveFile::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $this->driveService->deleteFile($file->google_file_id);
            $file->delete(); // logs akan terhapus otomatis via cascade

            return back()->with('success', 'File "' . $file->document_name . '" berhasil dihapus.');
        } catch (\Exception $e) {
            $file->delete();
            return back()->with('warning', 'File dihapus dari database, tapi gagal dihapus di Drive: ' . $e->getMessage());
        }
    }
}
