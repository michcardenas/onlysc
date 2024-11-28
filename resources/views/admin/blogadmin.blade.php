@extends('layouts.app_login')

@section('content')
<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
        </div>
    </nav>
</header>

<main class="main-admin">
    <div class="admin-grid">
        <!-- Sidebar Izquierdo - Categorías -->
        <section class="sidebar-section">
            <div class="section-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Categorías</h3>
                    <button onclick="mostrarFormularioCategoria('crear')" class="btn-submit-sm">
                        <i class="fas fa-plus-circle"></i> Nueva
                    </button>
                </div>

                <div class="table-admin-sm">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->nombre }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button onclick="mostrarFormularioCategoria('editar', {{ $categoria->id }})" class="btn-icon" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmarEliminarCategoria({{ $categoria->id }})" class="btn-icon-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">
                                    <p>No hay categorías</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario Categorías -->
            <div id="formularioCategoria" class="section-content mt-4" style="display: none;">
                <h4 id="formularioCategoriaTitulo">Nueva Categoría</h4>
                <form id="categoriaForm" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" id="categoriaMergeMethod" value="POST">
                    <div class="form-group">
                        <label for="nombreCategoria">Nombre</label>
                        <input type="text" id="nombreCategoria" name="nombre" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="descripcionCategoria">Descripción</label>
                        <textarea id="descripcionCategoria" name="descripcion" class="form-control"></textarea>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn-submit-sm">Guardar</button>
                        <button type="button" onclick="ocultarFormularioCategoria()" class="btn-cancel-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Contenido Principal - Artículos -->
        <section class="form-section main-content">
            <!-- Lista de Artículos -->
            <div id="listadoArticulos" class="section-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Administración del Blog</h2>
                    <button onclick="blogMostrarFormulario('crear')" class="btn-submit">
                        <i class="fas fa-plus-circle"></i> Nuevo Artículo
                    </button>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-admin">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Categoría</th>
                                <th>Autor</th>
                                <th>Fecha</th>
                                <th>Destacado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articulos as $articulo)
                            <tr>
                                <td>{{ $articulo->id }}</td>
                                <td>{{ $articulo->titulo }}</td>
                                <td>
                                    <span class="badge {{ $articulo->estado === 'publicado' ? 'bg-success' : ($articulo->estado === 'borrador' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ ucfirst($articulo->estado) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $articulo->categories->pluck('nombre')->implode(', ') }}
                                </td>
                                <td>{{ $articulo->user->name ?? 'Usuario Desconocido' }}</td>
                                <td>{{ \Carbon\Carbon::parse($articulo->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button onclick="blogToggleDestacado({{ $articulo->id }})" class="btn-icon">
                                        <i class="fas {{ $articulo->destacado ? 'fa-star' : 'fa-star-o' }}"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button onclick="blogVerArticulo({{ $articulo->id }})" class="btn-icon" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="blogEditarArticulo({{ $articulo->id }})" class="btn-icon" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="blogConfirmarEliminar({{ $articulo->id }})" class="btn-icon-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-pencil-alt fa-3x text-muted mb-3 d-block"></i>
                                        <p class="h5 text-muted mb-3">No hay artículos disponibles</p>
                                        <button onclick="blogMostrarFormulario('crear')" class="btn-submit">
                                            <i class="fas fa-plus-circle"></i> Crear el primer artículo
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de Artículo -->
            <div id="formularioArticulo" class="section-content" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 id="formularioTitulo">Nuevo Artículo</h2>
                    <button onclick="blogMostrarListado()" class="btn-submit">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </button>
                </div>

                <form id="articuloForm" action="{{ route('blogadmin.store') }}" method="POST" class="form-admin" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="articuloId">

                    <div class="form-group mb-4">
                        <label for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="contenido">Contenido</label>
                        <textarea name="contenido" id="contenido" rows="6" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label for="categoria">Categorías</label>
                        <select name="categorias[]" id="categoria" class="form-control" multiple>
                            @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="tags">Tags</label>
                        <select name="tags[]" id="tags" class="form-control" multiple>
                            @foreach($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="imagen">Imagen</label>
                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
                    </div>

                    <div class="form-group mb-4">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="borrador">Borrador</option>
                            <option value="publicado">Publicado</option>
                            <option value="privado">Privado</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="destacado" name="destacado" value="1">
                            <label class="form-check-label" for="destacado">
                                <i class="fas fa-star me-1"></i> Destacar artículo
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Guardar Artículo
                        </button>
                        <button type="button" onclick="blogMostrarListado()" class="btn-cancel">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Sidebar Derecho - Tags -->
        <section class="sidebar-section">
            <div class="section-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Tags</h3>
                    <button onclick="mostrarFormularioTag('crear')" class="btn-submit-sm">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </button>
                </div>

                <div class="table-admin-sm">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                            <tr>
                                <td>{{ $tag->nombre }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button onclick="mostrarFormularioTag('editar', {{ $tag->id }})" class="btn-icon" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmarEliminarTag({{ $tag->id }})" class="btn-icon-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">
                                    <p>No hay tags</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Formulario Tags -->
            <div id="formularioTag" class="section-content mt-4" style="display: none;">
                <h4 id="formularioTagTitulo">Nuevo Tag</h4>
                <form id="tagForm" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" id="tagMergeMethod" value="POST">
                    <div class="form-group">
                        <label for="nombreTag">Nombre</label>
                        <input type="text" id="nombreTag" name="nombre" required class="form-control">
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn-submit-sm">Guardar</button>
                        <button type="button" onclick="ocultarFormularioTag()" class="btn-cancel-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>

<!-- Formularios ocultos para eliminar -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection