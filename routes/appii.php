<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TarifPembayaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Api\KelasController;

// pengguna
Route::get('/pengguna', [PenggunaController::class, 'index']);
Route::post('/pengguna', [PenggunaController::class, 'store']);
Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);

// siswa
Route::get('/siswa', [SiswaController::class, 'index']);
Route::post('/siswa', [SiswaController::class, 'store']);
Route::get('/siswa/{nis}', [SiswaController::class, 'show']);
Route::put('/siswa/{nis}', [SiswaController::class, 'update']);
Route::delete('/siswa/{nis}', [SiswaController::class, 'destroy']);

// admin
Route::get('/admin', [AdminController::class, 'index']);
Route::post('/admin', [AdminController::class, 'store']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::put('/admin/{id}', [AdminController::class, 'update']);
Route::delete('/admin/{id}', [AdminController::class, 'destroy']);

// tarif pembayaran
Route::get('/tarif', [TarifPembayaranController::class, 'index']);
Route::post('/tarif', [TarifPembayaranController::class, 'store']);
Route::get('/tarif/{id}', [TarifPembayaranController::class, 'show']);
Route::put('/tarif/{id}', [TarifPembayaranController::class, 'update']);
Route::delete('/tarif/{id}', [TarifPembayaranController::class, 'destroy']);

// pembayaran
Route::get('/pembayaran', [PembayaranController::class, 'index']);
Route::post('/pembayaran', [PembayaranController::class, 'store']);
Route::get('/pembayaran/{id}', [PembayaranController::class, 'show']);
Route::put('/pembayaran/{id}', [PembayaranController::class, 'update']);
Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy']);

// notifikasi
Route::get('/notifikasi', [NotifikasiController::class, 'index']);
Route::post('/notifikasi', [NotifikasiController::class, 'store']);
Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show']);
Route::put('/notifikasi/{id}', [NotifikasiController::class, 'update']);
Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy']);

// kelas
Route::prefix('kelas')->group(function () {
    Route::get('/', [KelasController::class, 'index']);
    Route::post('/store', [KelasController::class, 'store']);
    Route::put('/update/{id}', [KelasController::class, 'update']);
    Route::delete('/delete/{id}', [KelasController::class, 'destroy']);
});
