<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\SiswaAuthController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\KelasController;
use App\Http\Controllers\Web\Siswa\PembayaranController as SiswaPembayaranController;
use App\Http\Controllers\Web\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Web\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\NotifikasiController;

Route::get('/', function () {
    return view('beranda');
})->name('beranda');

/*
|--------------------------------------------------------------------------
| PENDAFTARAN SISWA
|--------------------------------------------------------------------------
*/
Route::get('/daftar',   [SiswaAuthController::class, 'showDaftarForm'])->name('siswa.daftar.form');
Route::post('/daftar',  [SiswaAuthController::class, 'daftar'])->name('siswa.daftar');


/*
|--------------------------------------------------------------------------
| MIDTRANS NOTIFICATION (Public Route)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/notification', [SiswaPembayaranController::class, 'notification'])->name('midtrans.notification');


/*
|--------------------------------------------------------------------------
| ROUTE SISWA
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->group(function () {

    // LOGIN
    Route::get('/login',       fn() => view('siswa.login'))->name('siswa.login');
    Route::post('/login',      [SiswaAuthController::class, 'login'])->name('siswa.login.submit');
    Route::post('/logout',     [SiswaAuthController::class, 'logout'])->name('siswa.logout');

    // HALAMAN
    Route::get('/dashboard',        [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/beranda',          [SiswaController::class, 'beranda'])->name('siswa.beranda');
    Route::get('/pembayaran',       [SiswaController::class, 'pembayaran'])->name('siswa.pembayaran');
    Route::get('/pembayaran/qris',  [SiswaController::class, 'pembayaranQris'])->name('siswa.pembayaran.qris');

    // MIDTRANS
    Route::post('/midtrans/snap-token',  [SiswaPembayaranController::class, 'getSnapToken'])->name('midtrans.snap.token');
    Route::post('/midtrans/create-qris', [SiswaPembayaranController::class, 'getSnapToken'])->name('midtrans.create.qris');
    Route::post('/midtrans/finish',      [SiswaPembayaranController::class, 'notification'])->name('midtrans.finish');

    // BUKTI & SUKSES
    Route::get('/pembayaran-qris/success',        [SiswaPembayaranController::class, 'success'])->name('pembayaran.qris.success');
    Route::get('/pembayaran-qris/bukti/{id}',     [SiswaPembayaranController::class, 'bukti'])->name('pembayaran.qris.bukti');
    Route::get('/pembayaran/bukti-stream/{nis}',  [SiswaPembayaranController::class, 'streamBuktiPdf'])->name('pembayaran.bukti.stream');

    // LUPA PASSWORD
    Route::get('/lupa-password',    [SiswaAuthController::class, 'showLupaPasswordForm'])->name('siswa.lupa_password');
    Route::post('/lupa-password',   [SiswaAuthController::class, 'lupaPasswordSubmit'])->name('siswa.lupaPassword.submit');

    // ATUR PASSWORD
    Route::get('/atur-password',    [SiswaAuthController::class, 'showAturPasswordForm'])->name('siswa.atur_password');
    Route::post('/atur-password',   [SiswaAuthController::class, 'aturPasswordSubmit'])->name('siswa.atur_password.submit');

    // PENGATURAN AKUN
    Route::get('/pengaturan',          [SiswaController::class, 'pengaturan'])->name('siswa.pengaturan');
    Route::put('/pengaturan/profil',   [SiswaController::class, 'updateProfil'])->name('siswa.pengaturan.profil');
    Route::put('/pengaturan/password', [SiswaController::class, 'gantiPassword'])->name('siswa.pengaturan.password');
});


/*
|--------------------------------------------------------------------------
| ROUTE ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    // AUTH
    Route::get('/masuk',  [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/masuk', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout',[AdminAuthController::class, 'logout'])->name('admin.logout');

    // DASHBOARD & DATA SISWA
    Route::get('/dashboard',           [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/data-siswa',          [AdminController::class, 'dataSiswa'])->name('admin.dataSiswa');
    Route::post('/siswa/store',        [AdminController::class, 'storeSiswa'])->name('admin.siswa.store');
    Route::get('/siswa/edit/{nis}',    [AdminController::class, 'editSiswa'])->name('admin.siswa.edit');
    Route::put('/siswa/update/{nis}',  [AdminController::class, 'updateSiswa'])->name('admin.siswa.update');

    // DATA PEMBAYARAN
    Route::get('/data-pembayaran',             [AdminController::class, 'dataPembayaran'])->name('admin.dataPembayaran');
    Route::get('/data-pembayaran/export-xlsx', [AdminController::class, 'exportPembayaranXlsx'])->name('admin.pembayaran.export.xlsx');
    Route::get('/data-pembayaran/export-pdf',  [AdminController::class, 'exportPembayaranPdf'])->name('admin.pembayaran.export.pdf');
    Route::get('/pembayaran/filter', [AdminPembayaranController::class, 'filter'])->name('admin.pembayaran.filter');

    // DATA KELAS
    Route::get('/kelas',               [KelasController::class, 'index'])->name('admin.dataKelas');
    Route::post('/kelas/store',        [KelasController::class, 'store'])->name('admin.kelas.store');
    Route::get('/kelas/edit/{id}',     [KelasController::class, 'edit'])->name('admin.kelas.edit');
    Route::put('/kelas/update/{id}',   [KelasController::class, 'update'])->name('admin.kelas.update');
    Route::delete('/kelas/delete/{id}',[KelasController::class, 'destroy'])->name('admin.kelas.destroy');

    // RESOURCE
    Route::resource('pengguna',   PenggunaController::class);
    Route::resource('notifikasi', NotifikasiController::class);
});



