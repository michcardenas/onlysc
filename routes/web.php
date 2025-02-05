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
use App\Http\Controllers\MetaTagController;
use App\Http\Controllers\TarjetaController;
use App\Models\Servicio;
use App\Models\Atributo;
use App\Models\Nacionalidad;
use App\Http\Controllers\PageController;





Auth::routes();
Route::get('/', function () {
    return redirect()->route('home')->with('showLoader', true);
});
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/panel-control', [AdminController::class, 'index'])->name('panel_control');
    Route::get('/perfiles', [AdminController::class, 'Perfiles'])->name('admin.perfiles');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Ruta para mostrar el panel de administración de TyC
    Route::get('/tycadmin', [AdminController::class, 'tycadmin'])
        ->name('tycadmin');

    // Ruta para procesar la actualización de TyC
    Route::put('/tycadmin/update', [AdminController::class, 'update'])
        ->name('tycadmin.update');

    Route::get('/perfiles/login-as/{id}', [AdminController::class, 'loginAsUser'])->name('admin.login.as.user');
    Route::get('/perfiles/return-to-admin', [AdminController::class, 'returnToAdmin'])->name('admin.return');
    Route::delete('/perfil/{id}/eliminar', [AdminController::class, 'eliminarPerfil'])->name('admin.perfil.eliminar');

    Route::get('/ciudades', [CiudadController::class, 'index'])->name('ciudades.index');
    Route::get('/ciudades/{id}/edit', [CiudadController::class, 'edit'])->name('ciudades.edit');
    Route::delete('/ciudades/{id}', [CiudadController::class, 'destroy'])->name('ciudades.destroy');
    Route::put('/ciudades/{id}', [CiudadController::class, 'update'])->name('ciudades.update');
    Route::get('/ciudades/create', [CiudadController::class, 'create'])->name('ciudades.create');
    Route::post('/ciudades', [CiudadController::class, 'store'])->name('ciudades.store');

    Route::get('/seo', [SEOController::class, 'index'])->name('seo');
    Route::get('/seo-paginas', [SEOPaginasController::class, 'index'])->name('seo.paginas');
    Route::get('/seo-inicio', [SEOController::class, 'home'])->name('seo.home');
    Route::get('/seo-inicio-tarjetas', [SEOController::class, 'inicio'])->name('seo.inicio-tarjetas');
    Route::get('/seo-foro', [SEOController::class, 'foroadmin'])->name('seo.foroadmin');
    Route::get('/seo-blog', [SEOController::class, 'blogadmin'])->name('seo.blogadmin');
    Route::get('/seo-favoritos', [SEOController::class, 'favoritos'])->name('seo.favoritos');
    Route::get('/seo-publicar', [SEOController::class, 'publicateForm'])->name('seo.publicate.form');
    Route::get('/seo/templates', [AdminController::class, 'seoTemplates'])->name('seo.template');
    Route::post('/seo/update', [AdminController::class, 'updateSeoTemplate'])->name('seo.templates.update');
    Route::delete('/seo/templates/{id}', [AdminController::class, 'deleteSeoTemplate'])->name('seo.templates.delete');
    Route::get('/seo/templates/ciudad/{ciudadId}', [AdminController::class, 'getTemplatesByCiudad'])->name('seo.templates.by-ciudad');
    Route::post('/seo/templates/update-all', [AdminController::class, 'updateAllTemplates'])->name('seo.templates.update.all');
    Route::put('/seo/update/{page}', [MetaTagController::class, 'update'])->name('seo.update');
    Route::get('seo/seofilters', [MetaTagController::class, 'index'])->name('seo.seofilters');
    Route::put('meta-tags/{page}', [MetaTagController::class, 'updateFilter'])->name('meta-tags.update');
    Route::put('/meta-tags/{page}/{filter?}', [MetaTagController::class, 'updateFilter'])
        ->name('meta-tags.update');
    Route::get('/get-metatag/{ciudad_id}', [MetaTagController::class, 'getMetaTagByCiudad'])->name('metatag.by.ciudad');
    Route::get('/admin/tarjetas', [TarjetaController::class, 'index'])->name('tarjetas.index');
    Route::get('/admin/tarjetas/create', [TarjetaController::class, 'create'])->name('tarjetas.create');
    Route::post('/admin/tarjetas', [TarjetaController::class, 'store'])->name('tarjetas.store');
    Route::get('/admin/tarjetas/{id}/edit', [TarjetaController::class, 'edit'])->name('tarjetas.edit');
    Route::put('/admin/tarjetas/{id}', [TarjetaController::class, 'update'])->name('tarjetas.update');
    Route::delete('/admin/tarjetas/{id}', [TarjetaController::class, 'destroy'])->name('tarjetas.destroy');

    //robots
    Route::get('/robots.txt', [SEOController::class, 'showRobots'])->name('seo.robots');
    Route::get('/admin/robots', [SEOController::class, 'editRobots'])->name('seo.edit_robots'); // Vista de edición
    Route::post('/admin/robots', [SEOController::class, 'updateRobots'])->name('seo.update_robots'); // Guardar cambios

    Route::get('/admin/users-by-city', [AdminController::class, 'getUsersByCity'])->name('admin.users-by-city');
    // Agregar en el grupo de rutas con middleware auth
    Route::post('/usuarios-publicate/toggle-image-block', [UsuarioPublicateController::class, 'toggleImageBlock'])
        ->name('usuarios_publicate.toggleImageBlock');
    Route::get('/usuarios-publicate/{id}/edit', [UsuarioPublicateController::class, 'edit'])->name('usuarios_publicate.edit');
    Route::put('/usuarios-publicate/{id}', [UsuarioPublicateController::class, 'update'])->name('usuarios_publicate.update');
    Route::post('/usuarios-publicate/eliminar-foto', [UsuarioPublicateController::class, 'eliminarFoto'])->name('usuarios_publicate.eliminarFoto');
    Route::post('/validate-fantasia', [UsuarioPublicateController::class, 'validateFantasia']);
    Route::post('/actualizar-posicion-foto', [UsuarioPublicateController::class, 'actualizarPosicionFoto'])->name('actualizar.posicion.foto');

    Route::get('/perfiles/login-as/{id}', [AdminController::class, 'loginAsUser'])->name('admin.login.as.user');
    Route::get('/perfiles/return-to-admin', [AdminController::class, 'returnToAdmin'])->name('admin.return');
    Route::delete('/perfil/{id}/eliminar', [AdminController::class, 'eliminarPerfil'])->name('admin.perfil.eliminar');


    Route::resource('ciudades', AdminController::class);
    Route::resource('sectores', AdminController::class);
    Route::resource('servicios', AdminController::class);
    Route::resource('atributos', AdminController::class);


    // Rutas para Sectores
    Route::get('/sectores', [AdminController::class, 'sectorIndex'])->name('sectores.indexsector');
    Route::get('/sectores/create', [AdminController::class, 'sectorCreate'])->name('sectores.createsector');
    Route::post('/sectores', [AdminController::class, 'sectorStore'])->name('sectores.store');
    Route::get('/sectores/{sector}/editsector', [AdminController::class, 'sectorEdit'])->name('sectores.editsector');
    Route::put('/sectores/{sector}/sectorupdate', [AdminController::class, 'sectorUpdate'])->name('sectores.update');
    Route::delete('/sectores/{sector}', [AdminController::class, 'sectorDestroy'])->name('sectores.destroy');

    // Rutas para Servicios
    Route::get('/servicios', [AdminController::class, 'servicioIndex'])->name('servicios.indexservicio');
    Route::get('/servicios/create', [AdminController::class, 'servicioCreate'])->name('servicios.createservicio');
    Route::post('/servicios', [AdminController::class, 'servicioStore'])->name('servicios.store');
    Route::get('/servicios/{servicio}/edit', [AdminController::class, 'servicioEdit'])->name('servicios.editservicio');
    Route::put('/servicios/{servicio}', [AdminController::class, 'servicioUpdate'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [AdminController::class, 'servicioDestroy'])->name('servicios.destroy');

    // Rutas para Atributos
    Route::get('/atributos', [AdminController::class, 'atributoIndex'])->name('atributos.indexatributo');
    Route::get('/atributos/create', [AdminController::class, 'atributoCreate'])->name('atributos.createatributo');
    Route::post('/atributos', [AdminController::class, 'atributoStore'])->name('atributos.store');
    Route::get('/atributos/{atributo}/edit', [AdminController::class, 'atributoEdit'])->name('atributos.editatributo');
    Route::put('/atributos/{atributo}', [AdminController::class, 'atributoUpdate'])->name('atributos.update');
    Route::delete('/atributos/{atributo}', [AdminController::class, 'atributoDestroy'])->name('atributos.destroy');

    // Rutas para Nacionalidades
    Route::get('/nacionalidades', [AdminController::class, 'nacionalidadIndex'])->name('nacionalidades.indexnacionalidad');
    Route::get('/nacionalidades/create', [AdminController::class, 'nacionalidadCreate'])->name('nacionalidades.createnacionalidad');
    Route::post('/nacionalidades', [AdminController::class, 'nacionalidadStore'])->name('nacionalidades.store');
    Route::get('/nacionalidades/{nacionalidad}/edit', [AdminController::class, 'nacionalidadEdit'])->name('nacionalidades.editnacionalidad');
    Route::put('/nacionalidades/{nacionalidad}', [AdminController::class, 'nacionalidadUpdate'])->name('nacionalidades.update');
    Route::delete('/nacionalidades/{nacionalidad}', [AdminController::class, 'nacionalidadDestroy'])->name('nacionalidades.destroy');

    Route::prefix('seo')->group(function () {
        // Vista principal
        Route::get('/templates-unitarios', [SEOController::class, 'templatesUnitarios'])
            ->name('seo.templates.unitarios');
    
        // API para obtener datos SEO
        Route::get('/servicios/{servicio}/seo', [SEOController::class, 'getServicioSeo'])
            ->name('api.servicios.seo');
        Route::get('/atributos/{atributo}/seo', [SEOController::class, 'getAtributoSeo'])
            ->name('api.atributos.seo');
        Route::get('/nacionalidades/{nacionalidad}/seo', [SEOController::class, 'getNacionalidadSeo'])
            ->name('api.nacionalidades.seo');
        Route::get('/sectores/{sector}/seo', [SEOController::class, 'getSectorSeo'])
            ->name('api.sectores.seo');
    
        // Nuevas rutas para obtener datos SEO
        Route::get('/disponibilidad/seo', [SEOController::class, 'getDisponibilidadSeo'])
            ->name('api.disponibilidad.seo');
        Route::get('/resenas/seo', [SEOController::class, 'getResenasSeo'])
            ->name('api.resenas.seo');
        Route::get('/categorias/{categoria}/seo', [SEOController::class, 'getCategoriaSeo'])
            ->name('api.categorias.seo');
    
        // Rutas para actualizar SEO
        Route::post('/servicios/update', [SEOController::class, 'updateServicioSeo'])
            ->name('seo.servicios.update');
        Route::post('/atributos/update', [SEOController::class, 'updateAtributoSeo'])
            ->name('seo.atributos.update');
        // Corregida esta ruta
        Route::post('/nacionalidades/update', [SEOController::class, 'updateNacionalidadSeo'])
            ->name('seo.nacionalidades.update');
        Route::post('/sectores/update', [SEOController::class, 'updateSectorSeo'])
            ->name('seo.sectores.update');
        
        // Nuevas rutas para actualizar SEO
        Route::post('/disponibilidad/update', [SEOController::class, 'updateDisponibilidadSeo'])
            ->name('seo.disponibilidad.update');
        Route::post('/resenas/update', [SEOController::class, 'updateResenasSeo'])
            ->name('seo.resenas.update');
        Route::post('/categorias/update', [SEOController::class, 'updateCategoriaSeo'])
            ->name('seo.categorias.update');
    });
});

// Rutas del foro separadas con su propio middleware
Route::middleware(['auth', \App\Http\Middleware\ForoMiddleware::class])->group(function () {
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

Route::get('/', [App\Http\Controllers\HomeController::class, 'showHome'])->name('home');
Route::post('/escorts-{nombreCiudad}/aplicar-filtros', [InicioController::class, 'filterUsuarios'])
    ->name('inicio.filtrar');
Route::match(['get', 'post'], '/escorts-{nombreCiudad}/{filtros?}', [InicioController::class, 'show'])
    ->where('nombreCiudad', '[^/]+')
    ->where('filtros', '.*')
    ->name('inicio');

Route::get('/escorts-{nombreCiudad}/{categoria}', [InicioController::class, 'showByCategory'])->name('inicio.categoria');

Route::post('/inicio', [InicioController::class, 'show'])->name('inicio');

Route::get('/escorts/{nombre}', [InicioController::class, 'showPerfil'])
    ->where('nombre', '.*-[0-9]+$') // Solo permitir URLs que terminen en -número
    ->name('perfil.show');

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
Route::get('/tyc', [InicioController::class, 'tyc'])->name('tyc');

// En routes/web.php
Route::get('/test-403', function () {
    abort(403);
});

// Ruta POST si necesitas procesar algún formulario (mantén la que ya tienes)
Route::post('/rta', [InicioController::class, 'rta'])->name('rta.store');

//Foro Admin y Posts

Route::get('/buscar', [ForoController::class, 'buscar'])->name('foro.buscar');

// Rutas para visualización pública
Route::get('/blog', [BlogController::class, 'showBlog'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show_article'])->name('blog.show_article');
Route::get('/category/{slug}', [BlogController::class, 'showCategory'])->name('blog.show_category');

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

Route::get('/get-filter-data', function () {
    $servicios = Servicio::orderBy('posicion')->get();
    $atributos = Atributo::orderBy('posicion')->get();
    $nacionalidades = Nacionalidad::orderBy('nombre')->get();

    return response()->json([
        'servicios' => $servicios,
        'atributos' => $atributos,
        'nacionalidades' => $nacionalidades,
    ]);
});
Route::get('/contacto', [PageController::class, 'contacto'])->name('contacto');
// En routes/web.php
Route::post('/contact/send', [PageController::class, 'send'])->name('contact.send');

