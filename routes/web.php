<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PublicateController;
use App\Http\Controllers\ForoController;
use App\Http\Controllers\AdminController;




Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');
Route::get('/inicio', [InicioController::class, 'show'])->name('inicio');
Route::post('/inicio', [InicioController::class, 'show'])->name('inicio');

//Publicate
Route::get('/publicate', [PublicateController::class, 'showRegistrationForm'])->name('publicate.form');
Route::post('/publicate', [PublicateController::class, 'store'])->name('publicate.store');

//Foro
Route::get('/foro', [ForoController::class, 'showForo'])->name('foro');
Route::get('/foros/{categoria}', [ForoController::class, 'show_foro'])->name('foro.show_foro');

//panel
Route::get('/panel-control', [AdminController::class, 'index'])->name('panel_control');
