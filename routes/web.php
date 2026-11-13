<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\AuthController;

// ── Public routes ─────────────────────────────────────────────
Route::get('/', [AspirasiController::class, 'index'])->name('home');
Route::get('/aspirasi', [AspirasiController::class, 'publicList'])->name('aspirasi.public');

// Form submit — POST, simpan ke DB, redirect ke halaman sukses
Route::post('/simpan', [AspirasiController::class, 'store'])->name('aspirasi.store');

// Halaman sukses dengan kode tiket (GET dengan query param ?tiket=ASP-…)
Route::get('/aspirasi/sukses', [AspirasiController::class, 'sukses'])->name('aspirasi.sukses');

// API: Lacak status tiket (dipanggil via AJAX dari landing page)
Route::get('/api/lacak-tiket', [AspirasiController::class, 'lacakTiket'])->name('api.lacak');

// ── Authentication routes ─────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Admin routes (Protected by auth) ──────────────────────────
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AspirasiController::class, 'adminDashboard'])->name('dashboard');
    Route::post('/aspirasi/{aspirasi}/update', [AspirasiController::class, 'updateStatus'])->name('update-status');
    Route::delete('/aspirasi/{aspirasi}', [AspirasiController::class, 'destroy'])->name('aspirasi.destroy');
});
