<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;



Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');


Route::get('/inicio', [InicioController::class, 'show'])->name('inicio');
