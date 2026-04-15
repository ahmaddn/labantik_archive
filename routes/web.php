<?php

use App\Http\Controllers\AdminGoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveFileController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Drive (semua user login) ──────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/drive', [DriveFileController::class, 'index'])->name('drive.index');
    Route::post('/drive/upload', [DriveFileController::class, 'upload'])->name('drive.upload');
    Route::delete('/drive/{id}', [DriveFileController::class, 'destroy'])->name('drive.destroy');
});

// ── Admin Google (Super Admin only) ──────────────
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/google', [AdminGoogleController::class, 'showConnect'])->name('google.connect');
    Route::get('/google/redirect', [AdminGoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [AdminGoogleController::class, 'handleCallback'])->name('google.callback');
    Route::delete('/google/disconnect', [AdminGoogleController::class, 'disconnect'])->name('google.disconnect');
});
