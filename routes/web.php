<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\AdminGoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveFileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
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
    Route::get('/pernyataan/{id}',      [SignatureController::class, 'showPernyataan'])->name('pernyataan.show'); // ← BARU
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
    Route::get('/students',          [AdminUserController::class, 'students'])->name('students.index');
    Route::get('/students/{id}',     [AdminUserController::class, 'studentShow'])->name('students.show');

    // ─── Data Guru ────────────────────────────────────────────────────
    Route::get('/teachers',          [AdminUserController::class, 'teachers'])->name('teachers.index');
    Route::get('/teachers/{id}',     [AdminUserController::class, 'teacherShow'])->name('teachers.show');

    // ─── Data Guru TU (Guru Piket) ────────────────────────────────────
    Route::get('/piket',             [AdminUserController::class, 'piket'])->name('piket.index');
    Route::get('/piket/{id}',        [AdminUserController::class, 'piketShow'])->name('piket.show');
});
