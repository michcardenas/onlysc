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
use App\Http\Controllers\BlogController;
use App\Models\UsuarioPublicate;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\SEOController;
use App\Http\Controllers\SEOPaginasController;

Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');
Route::post('/escorts-{nombreCiudad}/aplicar-filtros', [InicioController::class, 'filterUsuarios'])
->name('inicio.filtrar');
Route::match(['get', 'post'], '/escorts-{nombreCiudad}/{filtros?}', [InicioController::class, 'show'])
    ->where('nombreCiudad', '[^/]+')
    ->where('filtros', '.*')
    ->name('inicio');
Route::get('/escorts-{nombreCiudad}/{categoria}', [InicioController::class, 'showByCategory'])->name('inicio.categoria');

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

// Ruta GET para mostrar la vista
Route::get('/rta', [InicioController::class, 'RTA'])->name('rta');

// Ruta POST si necesitas procesar algún formulario (mantén la que ya tienes)
Route::post('/rta', [InicioController::class, 'rta'])->name('rta.store');

//panel
Route::get('/panel-control', [AdminController::class, 'index'])->name('panel_control');
Route::get('/usuarios-publicate/{id}/edit', [UsuarioPublicateController::class, 'edit'])->name('usuarios_publicate.edit');
Route::put('/usuarios-publicate/{id}', [UsuarioPublicateController::class, 'update'])->name('usuarios_publicate.update');
Route::post('/usuarios-publicate/eliminar-foto', [UsuarioPublicateController::class, 'eliminarFoto'])->name('usuarios_publicate.eliminarFoto');
Route::post('/validate-fantasia', [UsuarioPublicateController::class, 'validateFantasia']);
Route::post('/actualizar-posicion-foto', [UsuarioPublicateController::class, 'actualizarPosicionFoto'])->name('actualizar.posicion.foto');

//Perfiles en general
Route::get('/perfiles', [AdminController::class, 'Perfiles'])->name('admin.perfiles');
Route::get('/perfiles/login-as/{id}', [AdminController::class, 'loginAsUser'])->name('admin.login.as.user');
Route::get('/perfiles/return-to-admin', [AdminController::class, 'returnToAdmin'])->name('admin.return');
Route::delete('/perfil/{id}/eliminar', [AdminController::class, 'eliminarPerfil'])->name('admin.perfil.eliminar');

//Foro Admin y Posts
Route::middleware(['auth'])->group(function () {
    // Rutas para CRUD de foros
    Route::prefix('foroadmin')->group(function () {
        Route::get('/', [ForoController::class, 'foroadmin'])->name('foroadmin');
        Route::get('/edit/{id}', [ForoController::class, 'edit'])->name('foroadmin.edit');
        Route::put('/update/{id}', [ForoController::class, 'update'])->name('foroadmin.update');
        Route::delete('/delete/{id}', [ForoController::class, 'destroy'])->name('foroadmin.destroy');
        Route::get('/create', [ForoController::class, 'create'])->name('foroadmin.create');
        Route::post('/store', [ForoController::class, 'store'])->name('foroadmin.store');

        // Rutas para administración de posts
        Route::get('/posts/{id_blog?}', [ForoController::class, 'showPosts'])->name('foroadmin.posts');
        Route::get('/createpost/{id_blog}', [ForoController::class, 'createpost'])->name('foroadmin.createpost');
        Route::post('/storepost', [ForoController::class, 'storepost'])->name('foroadmin.storepost');
        Route::get('/post/{id}/edit', [ForoController::class, 'editpost'])->name('foroadmin.editpost');
        Route::put('/post/{id}', [ForoController::class, 'updatepost'])->name('foroadmin.updatepost');
        Route::delete('/post/{id}', [ForoController::class, 'destroypost'])->name('foroadmin.destroypost');
        Route::post('/posts/{id}/toggle-fixed', [ForoController::class, 'toggleFixed'])->name('posts.toggle-fixed');
    });
});

// Rutas para visualización pública
Route::get('/blog', [BlogController::class, 'showBlog'])->name('blog');
Route::get('/blog/{id}', [BlogController::class, 'show_article'])->name('blog.show_article');
Route::get('/blog/categoria/{id}', [BlogController::class, 'showCategory'])->name('blog.show_category');

// Rutas para administración (siguiendo el patrón de tu foroadmin)
Route::middleware(['auth'])->group(function () {
    Route::prefix('blogadmin')->group(function () {
        Route::get('/', [BlogController::class, 'blogadmin'])->name('blogadmin');
        Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('blogadmin.edit');
        Route::post('/store', [BlogController::class, 'store'])->name('blogadmin.store');
        Route::match(['post', 'put'], '/update/{id}', [BlogController::class, 'update'])->name('blogadmin.update');
        Route::delete('/delete/{id}', [BlogController::class, 'destroy'])->name('blogadmin.destroy');
        Route::post('/toggle-destacado/{id}', [BlogController::class, 'toggleDestacado'])->name('blogadmin.toggle-destacado');

        // Rutas para categorías
        Route::post('/categories/store', [BlogController::class, 'storeCategory'])->name('blogadmin.categories.store');
        Route::put('/categories/update/{id}', [BlogController::class, 'updateCategory'])->name('blogadmin.categories.update');
        Route::delete('/categories/delete/{id}', [BlogController::class, 'destroyCategory'])->name('blogadmin.categories.destroy');
        Route::get('/categories/edit/{id}', [BlogController::class, 'editCategory'])->name('blogadmin.categories.edit');

        // Rutas para tags
        Route::post('/tags/store', [BlogController::class, 'storeTag'])->name('blogadmin.tags.store');
        Route::put('/tags/update/{id}', [BlogController::class, 'updateTag'])->name('blogadmin.tags.update');
        Route::delete('/tags/delete/{id}', [BlogController::class, 'destroyTag'])->name('blogadmin.tags.destroy');
        Route::get('/tags/edit/{id}', [BlogController::class, 'editTag'])->name('blogadmin.tags.edit');
    });
});

//Disponibilidad
Route::post('/disponibilidad', [DisponibilidadController::class, 'store'])->name('disponibilidad.store');
Route::get('/disponibilidad', [DisponibilidadController::class, 'index'])->name('disponibilidad.index');

//Perfil
Route::get('/escorts/{id}', [PerfilController::class, 'show'])->name('escorts.show');

// Perfil Admin (Nuevas rutas)
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('admin.profile');
    Route::post('/perfil/update', [PerfilController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::post('/perfil/update-photo', [PerfilController::class, 'updatePhoto'])->name('admin.updatePhoto');
    Route::post('/perfil/crear-estado', [PerfilController::class, 'crearEstado'])->name('admin.crear-estado');
    Route::delete('/perfil/eliminar-estado/{id}', [PerfilController::class, 'eliminarEstado'])->name('admin.eliminar-estado');
    Route::post('/estados/marcar-visto', [InicioController::class, 'marcarComoVisto'])->name('estados.marcar-visto');
    Route::post('/favorite/{id}', [PerfilController::class, 'toggleFavorite'])->name('favorite.toggle');
    Route::get('/mis-favoritos', [PerfilController::class, 'showFavorites'])->name('favoritos.show');
});

Route::get('/usuario/{id}', [PerfilController::class, 'getUsuario'])->name('usuario.get');

//ciudades
Route::get('/ciudades', [CiudadController::class, 'index'])->name('ciudades.index');
Route::get('/ciudades/{id}/edit', [CiudadController::class, 'edit'])->name('ciudades.edit');
Route::delete('/ciudades/{id}', [CiudadController::class, 'destroy'])->name('ciudades.destroy');
Route::put('/ciudades/{id}', [CiudadController::class, 'update'])->name('ciudades.update');
Route::get('/ciudades/create', [CiudadController::class, 'create'])->name('ciudades.create');
Route::post('/ciudades', [CiudadController::class, 'store'])->name('ciudades.store');

//SEO
Route::get('/seo', [SEOController::class, 'index'])->name('seo');
Route::get('/seo-paginas', [SEOPaginasController::class, 'index'])->name('seo.paginas');
// Rutas para la sección de SEO
Route::get('/seo-inicio', [SEOController::class, 'home'])->name('seo.home');
Route::get('/seo-foro', [SEOController::class, 'foroadmin'])->name('seo.foroadmin');
Route::get('/seo-blog', [SEOController::class, 'blogadmin'])->name('seo.blogadmin');
Route::get('/seo-publicar', [SEOController::class, 'publicateForm'])->name('seo.publicate.form');
