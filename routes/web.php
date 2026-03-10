<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
});


// Dashboard protegido
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'genres'])->name('dashboard');


// Perfil
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


// Auth routes (Breeze)
require __DIR__.'/auth.php';


// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback');


// Selección de géneros (solo usuarios logueados)
Route::get('/select-genres', function () {
    return view('registro.select-genres');
})->middleware('auth')->name('genres.select');

Route::post('/save-genres', function () {

    session(['genres_selected' => true]);

    return redirect()->route('dashboard');

})->middleware('auth')->name('genres.save');