<?php

use App\Http\Controllers\KantinController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Halaman khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('kategori', KategoriController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('kantin', KantinController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

// Halaman untuk admin dan penjual
Route::middleware(['auth', 'role:admin,penjual'])->group(function () {
    Route::resource('menu', MenuController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

// Halaman khusus pembeli
Route::middleware(['auth', 'role:pembeli'])->group(function () {
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');

    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
    Route::put('/keranjang/{menu}', [KeranjangController::class, 'ubah'])->name('keranjang.ubah');
    Route::delete('/keranjang/{menu}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
    Route::delete('/keranjang', [KeranjangController::class, 'kosongkan'])->name('keranjang.kosongkan');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/buat', [PesananController::class, 'buat'])->name('pesanan.buat');
    Route::post('/pesanan', [PesananController::class, 'simpan'])->name('pesanan.simpan');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/batal', [PesananController::class, 'batalkan'])->name('pesanan.batalkan');
});

require __DIR__.'/auth.php';