<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PublicateController;



Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');
Route::get('/inicio', [InicioController::class, 'show'])->name('inicio');
Route::get('/publicate', [PublicateController::class, 'showRegistrationForm'])->name('publicate.form');
