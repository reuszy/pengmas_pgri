<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\TarifPembayaranController;
use Illuminate\Support\Facades\Route;

// JWT
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login',       [AuthController::class, 'login']);
    Route::post('logout',      [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh',     [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('me',          [AuthController::class, 'me'])->middleware('auth:api');
});


// API Kelas
Route::prefix('kelas')->middleware('auth:api')->group(function (){
    Route::get('/',          [KelasController::class, 'index']);
    Route::get('/{id}',      [KelasController::class, 'show']);
    Route::post('/',         [KelasController::class, 'store']);
    Route::put('/{id}',      [KelasController::class, 'update']);
    Route::delete('/{id}',   [KelasController::class, 'destroy']);
});


// API Siswa
Route::prefix('siswa')->middleware('auth:api')->group(function () {
    Route::get('/',         [SiswaController::class, 'index']);
    Route::post('/',        [SiswaController::class, 'store']);

    Route::get('/profil',          [SiswaController::class, 'profil']);
    Route::put('/profil',          [SiswaController::class, 'updateProfil']);
    Route::put('/ganti-password',  [SiswaController::class, 'gantiPassword']);

    Route::get('/{nis}',    [SiswaController::class, 'show']);
    Route::put('/{nis}',    [SiswaController::class, 'update']);
    Route::delete('/{nis}', [SiswaController::class, 'destroy']);
});


// API Admin
Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::get('/',        [AdminController::class, 'index']);
    Route::get('/{id}',    [AdminController::class, 'show']);
    Route::post('/',       [AdminController::class, 'store']);
    Route::put('/{id}',    [AdminController::class, 'update']);
    Route::delete('/{id}', [AdminController::class, 'destroy']);
});


// API Tarif Pembayaran
Route::prefix('tarif')->middleware('auth:api')->group(function () {
    Route::get('/',        [TarifPembayaranController::class, 'index']);
    Route::get('/{id}',    [TarifPembayaranController::class, 'show']);
    Route::post('/',       [TarifPembayaranController::class, 'store']);
    Route::put('/{id}',    [TarifPembayaranController::class, 'update']);
    Route::delete('/{id}', [TarifPembayaranController::class, 'destroy']);
});


// API Pembayaran
Route::prefix('pembayaran')->middleware('auth:api')->group(function () {
    Route::get('/',             [PembayaranController::class, 'index']);
    Route::get('/{id}',         [PembayaranController::class, 'show']);
    Route::post('/snap-token',  [PembayaranController::class, 'snapToken']);
    Route::post('/notification',[PembayaranController::class, 'notification']);
});