<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\AdminGoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveFileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GraduationController;
use App\Http\Controllers\SignatureController;

// ── Root redirect ─────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('drive.index');
    }
    return redirect()->route('login');
})->name('home');

// ── Auth ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Drive (semua user login) ──────────────────────
Route::middleware('auth')->prefix('drive')->name('drive.')->group(function () {
    Route::get('/',        [DriveFileController::class, 'index'])->name('index');
    Route::get('/create',  [DriveFileController::class, 'create'])->name('create');
    Route::post('/store',  [DriveFileController::class, 'store'])->name('store');
    Route::delete('/{id}', [DriveFileController::class, 'destroy'])->name('destroy');

    Route::post('/signature/store',     [SignatureController::class, 'store'])->name('signature.store');
    Route::get('/pernyataan/{id}',      [SignatureController::class, 'showPernyataan'])->name('pernyataan.show');
    Route::get('/transkrip/{id}',       [SignatureController::class, 'showTranskrip'])->name('transkrip.show');
});

// ── Profile (semua user login) ────────────────────
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',         [ProfileController::class, 'edit'])->name('edit');
    Route::put('/',         [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
});

// ── Admin (Super Admin only) ──────────────────────
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {

    // Google Drive OAuth
    Route::get('/google',               [AdminGoogleController::class, 'showConnect'])->name('google.connect');
    Route::get('/google/redirect',      [AdminGoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback',      [AdminGoogleController::class, 'handleCallback'])->name('google.callback');
    Route::delete('/google/disconnect', [AdminGoogleController::class, 'disconnect'])->name('google.disconnect');

    // Categories CRUD
    Route::get('/categories',             [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create',      [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories',            [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit',   [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}',        [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}',     [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Sub-Categories CRUD
    Route::get('/sub-categories',             [SubCategoryController::class, 'index'])->name('sub-categories.index');
    Route::get('/sub-categories/create',      [SubCategoryController::class, 'create'])->name('sub-categories.create');
    Route::post('/sub-categories',            [SubCategoryController::class, 'store'])->name('sub-categories.store');
    Route::get('/sub-categories/{id}/edit',   [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
    Route::put('/sub-categories/{id}',        [SubCategoryController::class, 'update'])->name('sub-categories.update');
    Route::delete('/sub-categories/{id}',     [SubCategoryController::class, 'destroy'])->name('sub-categories.destroy');

    // ─── History Upload ───────────────────────────────────────────────
    Route::get('/history', [AdminUserController::class, 'history'])->name('history.index');

    // ─── Data Siswa ───────────────────────────────────────────────────
    Route::get('/students',      [AdminUserController::class, 'students'])->name('students.index');
    Route::get('/students/{id}', [AdminUserController::class, 'studentShow'])->name('students.show');

    // ─── Data Guru ────────────────────────────────────────────────────
    Route::get('/teachers',      [AdminUserController::class, 'teachers'])->name('teachers.index');
    Route::get('/teachers/{id}', [AdminUserController::class, 'teacherShow'])->name('teachers.show');

    // ─── Data Guru TU (Guru Piket) ────────────────────────────────────
    Route::get('/piket',         [AdminUserController::class, 'piket'])->name('piket.index');
    Route::get('/piket/{id}',    [AdminUserController::class, 'piketShow'])->name('piket.show');

    // ─── Kelulusan ────────────────────────────────────────────────────
    // PENTING: route static harus didaftarkan SEBELUM route dynamic {id}
    // agar tidak tertangkap sebagai parameter

    // Index
    Route::get('/graduation', [GraduationController::class, 'index'])->name('graduation.index');

    // Template
    Route::get('/graduation/download-template', [GraduationController::class, 'downloadTemplate'])->name('graduation.downloadTemplate');

    // Mapel — static routes
    Route::get('/graduation/mapel/create',      [GraduationController::class, 'createMapel'])->name('graduation.createMapel');
    // routes/web.php
    Route::get('/admin/graduation/api/students-by-class',  [GraduationController::class, 'getStudentsByClass'])->name('graduation.studentsByClass');
    Route::get('/admin/graduation/api/mapels-by-class',    [GraduationController::class, 'getMapelsByClass'])->name('graduation.mapelsByClass');
    Route::post('/graduation/mapel/store',      [GraduationController::class, 'storeMapel'])->name('graduation.storeMapel');
    Route::get('/graduation/mapel/{id}/edit',   [GraduationController::class, 'editMapel'])->name('graduation.editMapel');
    Route::put('/graduation/mapel/{id}',        [GraduationController::class, 'updateMapel'])->name('graduation.updateMapel');
    Route::delete('/graduation/mapel/{id}',     [GraduationController::class, 'destroyMapel'])->name('graduation.destroyMapel');

    // Import Mapel
    Route::get('/graduation/mapel/import',      [GraduationController::class, 'showImportMapel'])->name('graduation.showImportMapel');
    Route::post('/graduation/mapel/import',     [GraduationController::class, 'importMapel'])->name('graduation.importMapel');

    // Import Nilai
    Route::get('/graduation/import-nilai',      [GraduationController::class, 'showImportNilai'])->name('graduation.showImportNilai');
    Route::post('/graduation/import-nilai',     [GraduationController::class, 'importNilai'])->name('graduation.importNilai');

    // Import / Export kelulusan
    Route::post('/graduation/import',           [GraduationController::class, 'import'])->name('graduation.import');
    Route::post('/graduation/export',           [GraduationController::class, 'export'])->name('graduation.export');
    Route::post('/graduation/export-pdf',       [GraduationController::class, 'exportPDF'])->name('graduation.exportPDF');
    Route::post('/graduation/export-all',       [GraduationController::class, 'exportAll'])->name('graduation.exportAll');

    // Graduation CRUD — dynamic {id} HARUS paling bawah
    Route::get('/graduation/create',            [GraduationController::class, 'create'])->name('graduation.create');
    Route::post('/graduation/store',            [GraduationController::class, 'store'])->name('graduation.store');
    Route::get('/graduation/{id}',              [GraduationController::class, 'show'])->name('graduation.show');
    Route::delete('/graduation/{id}',           [GraduationController::class, 'destroy'])->name('graduation.destroy');
});
