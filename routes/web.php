<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    //Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //Perfil y Opciones
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/email', [ProfileController::class, 'updateEmail'])->name('profile.update_email');
    Route::delete('/profile/reset-data', [ProfileController::class, 'resetData'])->name('profile.reset_data');

    //Selección de Géneros
    Route::get('/select_genres', [UserPreferenceController::class, 'showSelector'])->name('genres.select');
    Route::post('/save_genres', [UserPreferenceController::class, 'save'])->name('genres.save');

    //Ajustes de Cuenta (Avatar, Nombre visible)
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

    //Explorar
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
    Route::get('/genero/{nombre}', [ExploreController::class, 'genre'])->name('explore.genre');

    //Búsqueda y Recomendaciones
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/select/{id}/{type}', [SearchController::class, 'selectMovie']);
    Route::get('/recommendations/{busqueda}', [RecommendationController::class, 'show']);
    
    //Ruta para descargar imagen recomendaciones
    Route::get('/share-card/{busqueda}', [RecommendationController::class, 'shareCard']);

    //Contenido e Interacciones
    Route::get('/view/{id}/{type}', [ContentController::class, 'show']);
    Route::post('/visto', [ContentController::class, 'marcarVisto']);
    Route::post('/calificar', [ContentController::class, 'calificar']);

    //Listas
    Route::get('/listas', [ListController::class, 'index'])->name('listas');

});

//Auth routes (Breeze - Login, Register, Verify Email, Passwords)
require __DIR__.'/auth.php';

//Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');