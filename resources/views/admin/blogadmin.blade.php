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
    <section class="form-section">
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
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
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
                            <td>{{ $articulo->user->name ?? 'Usuario Desconocido' }}</td>
                            <td>{{ \Carbon\Carbon::parse($articulo->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <button 
                                    onclick="blogToggleDestacado({{ $articulo->id }})" 
                                    class="btn btn-link" 
                                    data-article-id="{{ $articulo->id }}">
                                    <i class="fas {{ $articulo->destacado ? 'fa-star' : 'fa-star-o' }}"></i>
                                </button>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button onclick="blogVerArticulo({{ $articulo->id }})" class="btn btn-info btn-sm" title="Ver Artículo">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="blogEditarArticulo({{ $articulo->id }})" class="btn btn-warning btn-sm" title="Editar Artículo">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="blogConfirmarEliminar({{ $articulo->id }})" class="btn btn-danger btn-sm" title="Eliminar Artículo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
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

        <!-- Formulario de Artículo (Crear/Editar) -->
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
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" name="titulo" id="titulo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('titulo')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="contenido" class="block text-sm font-medium text-gray-700">Contenido</label>
                    <textarea name="contenido" id="contenido" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                    @error('contenido')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="imagen" class="block text-sm font-medium text-gray-700">Imagen</label>
                    <input type="file" name="imagen" id="imagen" class="mt-1 block w-full" accept="image/*">
                    @error('imagen')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="borrador">Borrador</option>
                        <option value="publicado">Publicado</option>
                        <option value="privado">Privado</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <div class="form-check">
                        <input type="checkbox"
                            class="form-check-input"
                            id="destacado"
                            name="destacado"
                            value="1">
                        <label class="form-check-label" for="destacado">
                            <i class="fas fa-star me-1"></i> Destacar artículo
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Artículo
                    </button>
                    <button type="button" onclick="blogMostrarListado()" class="text-gray-500 hover:text-gray-700 font-medium">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection