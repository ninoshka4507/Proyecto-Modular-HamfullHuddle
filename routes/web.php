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

Route::get('/', function () {
    return view('welcome');
});

//Home
Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home'); 

//Perfil y Selección de Géneros (Todo lo que requiere estar logueado)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //ESTAS SON LAS ÚNICAS QUE DEBEN QUEDAR PARA GÉNEROS
    Route::get('/select_genres', [UserPreferenceController::class, 'showSelector'])->name('genres.select');
    Route::post('/save_genres', [UserPreferenceController::class, 'save'])->name('genres.save');
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
});

//Auth routes (Breeze)
require __DIR__.'/auth.php';

//Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

//Search Controllers
Route::get('/search',[SearchController::class,'search']);
Route::get('/select/{id}/{type}',[SearchController::class,'selectMovie']);
Route::get('/recommendations/{busqueda}',[RecommendationController::class,'show']);

//Ruta para descargar imagen recomendaciones
Route::get('/share-card/{busqueda}', 
[RecommendationController::class,'shareCard']);

//Explore Controllers
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/view/{id}/{type}', [SearchController::class, 'showDetails']);

//Content Cotroller
Route::get('/view/{id}/{type}', [ContentController::class, 'show']);
Route::post('/visto', [ContentController::class, 'marcarVisto']);
Route::post('/calificar', [ContentController::class, 'calificar']);