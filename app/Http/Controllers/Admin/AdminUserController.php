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
     * Ambil users berdasarkan kode role dengan eager load data tambahan.
     */
    private function getUsersByRole(string $roleCode, Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $search  = trim($request->get('search', ''));

        $query = User::whereHas('roles', fn($q) => $q->where('code', $roleCode))
            ->with(['employee', 'latestStudentAcademicYear.refClass', 'refClass'])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn($eq) => $eq->where('nip', 'like', "%{$search}%"));
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

    // ─── History ─────────────────────────────────────────────────────────

    public function history(Request $request): View
    {
        $perPage    = (int) $request->get('per_page', 15);
        $perPage    = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;
        $search     = trim($request->get('search', ''));
        $roleFilter = trim($request->get('role', ''));

        $query = GoogleDriveFile::with(['user.roles', 'category'])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('document_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            }))
            ->when($roleFilter, fn($q) => $q->whereHas('user.roles', fn($rq) => $rq->where('code', $roleFilter)))
            ->latest();

        $files = $query->paginate($perPage)->withQueryString();

        $totalUploads   = GoogleDriveFile::count();
        $totalSize      = (int) GoogleDriveFile::sum('size');
        $totalUploaders = GoogleDriveFile::distinct('user_id')->count('user_id');

        return view('admin.history.index', compact(
            'files',
            'search',
            'perPage',
            'roleFilter',
            'totalUploads',
            'totalSize',
            'totalUploaders'
        ));
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
        $user            = User::whereHas('roles', fn($q) => $q->where('code', 'siswa'))
            ->with(['refClass', 'latestStudentAcademicYear.refClass'])
            ->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles      = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes       = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit      = 100 * 1024 * 1024;
        $remainingBytes  = max(0, $quotaLimit - $usedBytes);

        return view('admin.students.show', compact(
            'user',
            'filesByCategory',
            'totalFiles',
            'usedBytes',
            'quotaLimit',
            'remainingBytes'
        ));
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
        $user            = User::whereHas('roles', fn($q) => $q->where('code', 'guru'))
            ->with(['employee'])
            ->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles      = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes       = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit      = 100 * 1024 * 1024;
        $remainingBytes  = max(0, $quotaLimit - $usedBytes);

        return view('admin.teachers.show', compact(
            'user',
            'filesByCategory',
            'totalFiles',
            'usedBytes',
            'quotaLimit',
            'remainingBytes'
        ));
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
        $user            = User::whereHas('roles', fn($q) => $q->where('code', 'guru-piket'))
            ->with(['employee'])
            ->findOrFail($id);
        $filesByCategory = $this->getFilesByCategory($user->id);
        $totalFiles      = GoogleDriveFile::where('user_id', $user->id)->count();
        $usedBytes       = (int) GoogleDriveFile::where('user_id', $user->id)->sum('size');
        $quotaLimit      = 100 * 1024 * 1024;
        $remainingBytes  = max(0, $quotaLimit - $usedBytes);

        return view('admin.piket.show', compact(
            'user',
            'filesByCategory',
            'totalFiles',
            'usedBytes',
            'quotaLimit',
            'remainingBytes'
        ));
    }
}
