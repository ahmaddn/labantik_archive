<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\AdminGoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveFileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GraduationVerifyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GraduationController;
use App\Http\Controllers\Admin\GraduationLetterController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\Admin\GraduationMapelController;
use App\Http\Controllers\Admin\GraduationImportController;
use App\Http\Controllers\Admin\GraduationSuratController;

// ── Verifikasi Dokumen Kelulusan (PUBLIK — tanpa login) ───────
Route::get('/verify/{uuid}', [GraduationVerifyController::class, 'show'])->name('graduation.verify');


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
    Route::post('/login/select', [AuthController::class, 'selectAccount'])->name('login.select'); // ← tambah sini
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
    Route::post('/track-print',         [SignatureController::class, 'trackPrint'])->name('track-print');
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

    // =========================================================================
    // ─── Kelulusan ────────────────────────────────────────────────────────────
    // PENTING: route static harus didaftarkan SEBELUM route dynamic {id}
    // =========================================================================

    // Index
    Route::get('/graduation',       [GraduationController::class, 'index'])->name('graduation.index');

    // Downloaders List
    Route::get('/graduation/downloaders', [GraduationController::class, 'downloaders'])->name('graduation.downloaders');

    // Template
    Route::get('/graduation/download-template', [GraduationController::class, 'downloadTemplate'])->name('graduation.downloadTemplate');
    // API helpers
    Route::get('/graduation/api/students-by-class', [GraduationController::class, 'getStudentsByClass'])->name('graduation.studentsByClass');
    Route::get('/graduation/api/mapels-by-class',   [GraduationController::class, 'getMapelsByClass'])->name('graduation.mapelsByClass');

    // Apply template ke semua
    Route::post('/graduation/apply-template-all',   [GraduationController::class, 'applyTemplateToAll'])->name('graduation.applyTemplateToAll');
    Route::post('/graduation/apply-transcript-template-all', [GraduationController::class, 'applyTranscriptTemplateToAll'])->name('graduation.applyTranscriptTemplateToAll');

    // ── Mapel ─────────────────────────────────────────────────────────────────
    Route::get('/graduation/mapel/create',       [GraduationMapelController::class, 'create'])->name('graduation.createMapel');
    Route::post('/graduation/mapel/store',       [GraduationMapelController::class, 'store'])->name('graduation.storeMapel');
    Route::get('/graduation/mapel/{id}/edit',    [GraduationMapelController::class, 'edit'])->name('graduation.editMapel');
    Route::put('/graduation/mapel/{id}',         [GraduationMapelController::class, 'update'])->name('graduation.updateMapel');
    Route::delete('/graduation/mapel/{id}',      [GraduationMapelController::class, 'destroy'])->name('graduation.destroyMapel');
    Route::post('/graduation/mapel/update-order', [GraduationMapelController::class, 'updateOrder'])->name('graduation.updateMapelOrder');
    Route::post('graduation/mapel/bulk-delete', [GraduationMapelController::class, 'destroyBulk'])->name('graduation.destroyMapelBulk');

    // ── Import Mapel ──────────────────────────────────────────────────────────
    Route::get('/graduation/mapel/import',       [GraduationImportController::class, 'showImportMapel'])->name('graduation.showImportMapel');
    Route::post('/graduation/mapel/import',      [GraduationImportController::class, 'importMapel'])->name('graduation.importMapel');
    Route::post('/graduation/import-mapel-auto', [GraduationImportController::class, 'importMapelAuto'])->name('graduation.importMapelAuto');

    // ── Import Nilai ──────────────────────────────────────────────────────────
    Route::get('/graduation/import-nilai',       [GraduationImportController::class, 'showImportNilai'])->name('graduation.showImportNilai');
    Route::post('/graduation/import-nilai',      [GraduationImportController::class, 'importNilai'])->name('graduation.importNilai');

    // ── Download Template ─────────────────────────────────────────────────────
    Route::get('/graduation/download-template',  [GraduationImportController::class, 'downloadTemplate'])->name('graduation.downloadTemplate');

    // ── Surat (static, harus sebelum {id}) ───────────────────────────────────
    Route::get('/graduation/{id}/surat-kelulusan',  [GraduationSuratController::class, 'showSuratKelulusan'])->name('graduation.showSuratKelulusan');
    Route::get('/graduation/{id}/surat-pernyataan', [GraduationSuratController::class, 'showSuratPernyataan'])->name('graduation.showSuratPernyataan');
    Route::get('/graduation/{id}/transkrip-nilai',  [GraduationSuratController::class, 'showTranskripNilai'])->name('graduation.showTranskripNilai');

    // ── Graduation Letter (Template Surat) ────────────────────────────────────
    Route::post('/graduation/letter',            [GraduationLetterController::class, 'store'])->name('graduation.letter.store');
    Route::get('/graduation/letter/{id}',        [GraduationLetterController::class, 'show'])->name('graduation.letter.show');
    Route::put('/graduation/letter/{id}',        [GraduationLetterController::class, 'update'])->name('graduation.letter.update');
    Route::delete('/graduation/letter/{id}',     [GraduationLetterController::class, 'destroy'])->name('graduation.letter.destroy');

    // ── Graduation CRUD — dynamic {id} HARUS paling bawah ────────────────────
    Route::get('/graduation/downloaders',       [GraduationController::class, 'downloaders'])->name('graduation.downloaders');
    Route::get('/graduation/create',             [GraduationController::class, 'create'])->name('graduation.create');
    Route::get('/graduation/create-transcript',  [GraduationController::class, 'createTranscript'])->name('graduation.createTranscript');
    Route::post('/graduation/store',             [GraduationController::class, 'store'])->name('graduation.store');
    Route::post('/graduation/store-transcript',    [GraduationController::class, 'storeTranscript'])->name('graduation.storeTranscript');
    Route::post('graduation/score/update',      [GraduationController::class, 'updateScore'])->name('graduation.updateScore');
    Route::post('graduation/score/update-bulk', [GraduationController::class, 'updateScoreBulk'])->name('graduation.updateScoreBulk');
    Route::post('graduation/generate-tokens',   [GraduationController::class, 'generateTokens'])->name('graduation.generateTokens');
    Route::post('graduation/generate-tokens-class', [GraduationController::class, 'generateTokensClass'])->name('graduation.generateTokensClass');
    Route::post('graduation/generate-token-student', [GraduationController::class, 'generateTokenStudent'])->name('graduation.generateTokenStudent');
    Route::get('graduation/export-tokens',      [GraduationController::class, 'exportTokens'])->name('graduation.exportTokens');
    Route::get('/graduation/{id}',               [GraduationController::class, 'show'])->name('graduation.show');
    Route::delete('/graduation/{id}',            [GraduationController::class, 'destroy'])->name('graduation.destroy');
});
