<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PublicateController;
use App\Http\Controllers\ForoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\UsuarioPublicateController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\PerfilController;
use App\Models\UsuarioPublicate;

Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');
Route::get('/inicio', [InicioController::class, 'show'])->name('inicio');
Route::post('/inicio', [InicioController::class, 'show'])->name('inicio');
Route::get('/perfil/{id}', [InicioController::class, 'showPerfil'])->name('perfil.show');

//Publicate
Route::get('/publicate', [PublicateController::class, 'showRegistrationForm'])->name('publicate.form');
Route::post('/publicate', [PublicateController::class, 'store'])->name('publicate.store');

//Foro
Route::get('/foro', [ForoController::class, 'showForo'])->name('foro');
Route::get('/foros/{categoria}', [ForoController::class, 'show_foro'])->name('foro.show_foro');
Route::get('/foros/{id_blog}/{id}', [PostsController::class, 'showPost'])->name('post.show');
Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentario.store');



//panel
Route::get('/panel-control', [AdminController::class, 'index'])->name('panel_control');
Route::get('/usuarios-publicate/{id}/edit', [UsuarioPublicateController::class, 'edit'])->name('usuarios_publicate.edit');
Route::put('/usuarios-publicate/{id}', [UsuarioPublicateController::class, 'update'])->name('usuarios_publicate.update');
Route::post('/usuarios-publicate/eliminar-foto', [UsuarioPublicateController::class, 'eliminarFoto'])->name('usuarios_publicate.eliminarFoto');
Route::post('/validate-fantasia', [UsuarioPublicateController::class, 'validateFantasia']);

//Foro Admin
Route::get('/foroadmin', [ForoController::class, 'foroadmin'])->name('foroadmin');

//Disponibilidad
Route::post('/disponibilidad', [DisponibilidadController::class, 'store'])->name('disponibilidad.store');
Route::get('/disponibilidad', [DisponibilidadController::class, 'index'])->name('disponibilidad.index');

//Perfil
Route::get('/escorts/{id}', [PerfilController::class, 'show'])->name('escorts.show');


// Rutas para CRUD de foros
Route::get('/foroadmin/edit/{id}', [ForoController::class, 'edit'])->name('foroadmin.edit');
Route::put('/foroadmin/update/{id}', [ForoController::class, 'update'])->name('foroadmin.update');
Route::delete('/foroadmin/delete/{id}', [ForoController::class, 'destroy'])->name('foroadmin.destroy');
Route::get('/foroadmin/create', [ForoController::class, 'create'])->name('foroadmin.create');
Route::post('/foroadmin/store', [ForoController::class, 'store'])->name('foroadmin.store');
