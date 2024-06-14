<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;

// Route untuk halaman utama
Route::get('/', function () {
    return view('welcome');
});

// // Route resource untuk kategori
// Route::resource('kategori', KategoriController::class);
// // Route resource untuk barang
// Route::resource('barang', BarangController::class);
// // Route resource untuk barang masuk
//     Route::resource('barangmasuk', BarangMasukController::class);
// //     // Route resource untuk barang keluar
//     Route::resource('barangkeluar', BarangKeluarController::class);
// //     // Route untuk menampilkan daftar kategori
//     Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');

// Route untuk login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Route untuk logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk registrasi
Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Route resource untuk kategori
    Route::resource('kategori', KategoriController::class);
    // Route resource untuk kategori
    Route::get('kategori/search', [KategoriController::class, 'search'])->name('kategori.search');

    // Route resource untuk barang
    Route::resource('barang', BarangController::class);
    // Route resource untuk barang masuk
    Route::resource('barangmasuk', BarangMasukController::class);
    // Route resource untuk barang keluar
    Route::resource('barangkeluar', BarangKeluarController::class);
    // Route untuk menampilkan daftar kategori
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
});
