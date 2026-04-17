<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleDriveFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    // ─── Helpers ────────────────────────────────────────────────────────

    /**
     * Ambil users berdasarkan kode role.
     * Asumsi: User::role($code) pakai Spatie / relasi roles.
     * Sesuaikan query ini dengan relasi yang ada di project kamu.
     */
    private function getUsersByRole(string $roleCode, Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search  = trim($request->get('search', ''));

        $query = User::whereHas('roles', fn($q) => $q->where('code', $roleCode))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            }))
            ->latest();

        return $query->paginate($perPage)->withQueryString();
    }

    private function getFilesByCategory(int|string $userId)
    {
        $allFiles = GoogleDriveFile::where('user_id', $userId)
            ->with(['category', 'expertise'])
            ->latest()
            ->get();

        return $allFiles
            ->groupBy(fn($f) => $f->google_category_id ?? 'uncategorized')
            ->map(function ($files) {
                $category = $files->first()->category
                    ?? (object)['id' => null, 'name' => 'Tanpa Kategori', 'slug' => 'uncategorized'];
                return ['category' => $category, 'files' => $files];
            })
            ->values();
    }

    // ─── Siswa ──────────────────────────────────────────────────────────

    public function students(Request $request): View
    {
        $users = $this->getUsersByRole('siswa', $request);
        return view('admin.students.index', [
            'users'   => $users,
            'search'  => $request->get('search', ''),
            'perPage' => $request->get('per_page', 10),
        ]);
    }

    public function studentShow(string $id): View
    {
        $user          = User::whereHas('roles', fn($q) => $q->where('code', 'siswa'))->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles    = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes     = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit    = 5 * 1024 * 1024;
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        return view('admin.students.show', compact('user', 'filesByCategory', 'totalFiles', 'usedBytes', 'quotaLimit', 'remainingBytes'));
    }

    // ─── Guru ────────────────────────────────────────────────────────────

    public function teachers(Request $request): View
    {
        $users = $this->getUsersByRole('guru', $request);
        return view('admin.teachers.index', [
            'users'   => $users,
            'search'  => $request->get('search', ''),
            'perPage' => $request->get('per_page', 10),
        ]);
    }

    public function teacherShow(string $id): View
    {
        $user           = User::whereHas('roles', fn($q) => $q->where('code', 'guru'))->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles     = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes      = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit     = 5 * 1024 * 1024;
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        return view('admin.teachers.show', compact('user', 'filesByCategory', 'totalFiles', 'usedBytes', 'quotaLimit', 'remainingBytes'));
    }

    // ─── Guru Piket (TU) ─────────────────────────────────────────────────

    public function piket(Request $request): View
    {
        $users = $this->getUsersByRole('guru-piket', $request);
        return view('admin.piket.index', [
            'users'   => $users,
            'search'  => $request->get('search', ''),
            'perPage' => $request->get('per_page', 10),
        ]);
    }

    public function piketShow(string $id): View
    {
        $user           = User::whereHas('roles', fn($q) => $q->where('code', 'guru-piket'))->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles     = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes      = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit     = 5 * 1024 * 1024;
        $remainingBytes = max(0, $quotaLimit - $usedBytes);

        return view('admin.piket.show', compact('user', 'filesByCategory', 'totalFiles', 'usedBytes', 'quotaLimit', 'remainingBytes'));
    }
}
