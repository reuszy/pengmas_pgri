<?php

use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TarifPembayaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\MidtransController;

Route::get('/', function () {
    return view('beranda');
})->name('beranda');

/*
|--------------------------------------------------------------------------
| PENDAFTARAN SISWA
|--------------------------------------------------------------------------
*/
Route::get('/daftar', [SiswaController::class, 'showDaftarForm'])
    ->name('siswa.daftar.form');

Route::post('/daftar', [SiswaController::class, 'daftar'])
    ->name('siswa.daftar');


/*
|--------------------------------------------------------------------------
| MIDTRANS NOTIFICATION (Public Route)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

/*
|--------------------------------------------------------------------------
| ROUTE SISWA
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->group(function () {

    // LOGIN
    Route::get('/login', fn() => view('siswa.login'))->name('siswa.login');
    Route::post('/login', [SiswaController::class, 'loginSubmit'])->name('siswa.login.submit');
    Route::post('/logout', [SiswaController::class, 'logout'])->name('siswa.logout');

    // DASHBOARD
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/beranda', [SiswaController::class, 'beranda'])->name('siswa.beranda');

    // PEMBAYARAN
    Route::get('/pembayaran', [SiswaController::class, 'pembayaran'])->name('siswa.pembayaran');
    Route::get('/pembayaran/qris', [SiswaController::class, 'pembayaranQris'])->name('siswa.pembayaran.qris');
    Route::get('/pembayaran-qris/success', [PembayaranController::class, 'success'])->name('pembayaran.qris.success');
    Route::get('/pembayaran-qris/bukti/{id}', [PembayaranController::class, 'bukti'])->name('pembayaran.qris.bukti');

    // MIDTRANS
    Route::post('/midtrans/create-transaction', [MidtransController::class, 'createTransaction'])->name('midtrans.create');
    Route::post('/midtrans/create-qris', [MidtransController::class, 'createQrisTransaction'])->name('midtrans.create.qris');
    Route::post('/midtrans/simulate-payment', [MidtransController::class, 'simulatePayment'])->name('midtrans.simulate');
    Route::post('/midtrans/snap-token', [MidtransController::class, 'getSnapToken'])->name('midtrans.snap.token');
    Route::post('/midtrans/finish', [MidtransController::class, 'notification'])->name('midtrans.finish');

    Route::get('/pembayaran/tambah', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/bukti-stream/{id}', [PembayaranController::class, 'streamBuktiPdf'])->name('pembayaran.bukti.stream');

    // LUPA PASSWORD
    Route::get('/lupa-password', [SiswaController::class, 'lupaPassword'])->name('siswa.lupa_password');
    Route::post('/lupa-password', [SiswaController::class, 'lupaPasswordSubmit'])->name('siswa.lupaPassword.submit');

    // ATUR PASSWORD
    Route::get('/atur-password', [SiswaController::class, 'aturPassword'])->name('siswa.atur_password');
    Route::post('/atur-password', [SiswaController::class, 'aturPasswordSubmit'])->name('siswa.atur_password.submit');
});


/*
|--------------------------------------------------------------------------
| ROUTE ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/masuk', fn() => view('admin.login'))->name('admin.login');
    Route::post('/masuk', [AdminController::class, 'loginSubmit'])->name('admin.login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/data-siswa', [AdminController::class, 'dataSiswa'])->name('admin.dataSiswa');
    Route::get('/data-pembayaran', [AdminController::class, 'dataPembayaran'])->name('admin.dataPembayaran');
    Route::get('/data-pembayaran/export-xlsx', [AdminController::class, 'exportPembayaranXlsx'])->name('admin.pembayaran.export.xlsx');
    Route::get('/data-pembayaran/export-pdf', [AdminController::class, 'exportPembayaranPdf'])->name('admin.pembayaran.export.pdf');

    // Admin: simpan siswa baru
    Route::post('/siswa/store', [AdminController::class, 'storeSiswa'])->name('admin.siswa.store');
    
    // Admin: edit dan update siswa
    Route::get('/siswa/edit/{nis}', [AdminController::class, 'editSiswa'])->name('admin.siswa.edit');
    Route::put('/siswa/update/{nis}', [AdminController::class, 'updateSiswa'])->name('admin.siswa.update');

    Route::get('/ambil-siswa/{kelas}', [PembayaranController::class, 'ambilSiswa']);
    Route::get('/tambah-pembayaran', [PembayaranController::class, 'create'])->name('admin.tambahPembayaran');
    Route::get('/pembayaran/filter', [PembayaranController::class, 'filterPembayaran'])->name('admin.pembayaran.filter');

    // Admin: Data Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('admin.dataKelas');
    Route::post('/kelas/store', [KelasController::class, 'store'])->name('admin.kelas.store');
    Route::get('/kelas/edit/{id}', [KelasController::class, 'edit'])->name('admin.kelas.edit');
    Route::put('/kelas/update/{id}', [KelasController::class, 'update'])->name('admin.kelas.update');
    Route::delete('/kelas/delete/{id}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');

    // Resource khusus admin
    Route::resource('pengguna', PenggunaController::class);
    Route::resource('tarif', TarifPembayaranController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('notifikasi', NotifikasiController::class);

    // Rute awal yang memanggil fungsi dasar integrasi
    Route::get('/midtrans/init', [MidtransController::class, 'index']);
});



