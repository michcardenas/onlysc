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
            {{-- Inicio disponible para todos los roles --}}
            <li><a href="{{ route('home') }}">Inicio</a></li>

            {{-- Perfil disponible para todos los roles --}}
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>

            @if($usuarioAutenticado->rol == 1)
            {{-- Menú completo solo para rol 1 --}}
            <li><a href="{{ route('panel_control') }}">Chicas</a></li>
            <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
            @endif

            @if($usuarioAutenticado->rol == 3)
            {{-- Foro solo disponible para rol 3 --}}
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            @endif

            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" style="background: none; border: none; padding: 0; color: white; font: inherit; cursor: pointer; text-decoration: none;">
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
        <div class="user-info-admin">
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }}
                @if($usuarioAutenticado->rol == 1)
                (Administrador)
                @elseif($usuarioAutenticado->rol == 2)
                (Chica)
                @elseif($usuarioAutenticado->rol == 3)
                (Usuario)
                @endif
            </p>
        </div>
    </nav>
</header>


<main class="main-admin">
    <section class="form-section">
        <!-- Lista de Posts -->
        <div id="listadoPosts" class="section-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Administración de Posts</h2>
                @if(isset($id_blog) && ($usuarioAutenticado->rol == 1 || $usuarioAutenticado->rol == 3))
                <button onclick="mostrarFormulario('crear')" class="btn-submit">
                    <i class="fas fa-plus-circle"></i> Nuevo Post
                </button>
                @endif
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
                            <th>Autor</th>
                            <th>Foro</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarioAutenticado->rol == 3 ? $posts->where('usuario_id', $usuarioAutenticado->id) : $posts as $post)
                        <tr>
                            <td>{{ $post->id }}</td>
                            <td>{{ $post->titulo }}</td>
                            <td>{{ $post->usuario->name ?? 'Usuario Desconocido' }}</td>
                            <td>{{ $post->foro->titulo ?? 'Foro Desconocido' }}</td>
                            <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button onclick="verPost({{ $post->id_blog }}, {{ $post->id }})" class="btn btn-info btn-sm" title="Ver Post">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($usuarioAutenticado->rol == 1 || ($usuarioAutenticado->rol == 3 && $post->usuario_id == $usuarioAutenticado->id))
                                    <button onclick="editarPost({{ $post->id }})" class="btn btn-warning btn-sm" title="Editar Post">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmarEliminar({{ $post->id }})" class="btn btn-danger btn-sm" title="Eliminar Post">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-center">
                                    <i class="fas fa-comments fa-3x text-muted mb-3 d-block"></i>
                                    <p class="h5 text-muted mb-3">No hay posts disponibles</p>
                                    @if(isset($id_blog) && ($usuarioAutenticado->rol == 1 || $usuarioAutenticado->rol == 3))
                                    <button onclick="mostrarFormulario('crear')" class="btn-submit">
                                        <i class="fas fa-plus-circle"></i> Crear el primer post
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="formularioPost" class="section-content" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 id="formularioTitulo">Nuevo Post</h2>
                <button onclick="mostrarListado()" class="btn-submit">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </button>
            </div>

            <form id="postForm" action="{{ route('foroadmin.storepost') }}" method="POST" class="form-admin">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="postId">
                @if(isset($id_blog))
                <input type="hidden" name="id_blog" value="{{ $id_blog }}">
                @endif

                <div class="form-group mb-4">
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input
                        type="text"
                        name="titulo"
                        id="titulo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        pattern="^(?!.*www)(?!.*https).*$"
                        oninvalid="this.setCustomValidity('El título no puede contener www ni https')"
                        oninput="this.setCustomValidity('')"
                        title="El título no puede contener www ni https"
                        value="{{ old('titulo') }}"
                        required>
                    @error('titulo')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                @if(isset($id_blog) && $id_blog == 16)
   <div class="form-group mb-4">
       <label for="chica_id" class="block text-sm font-medium text-gray-700">Seleccionar Chica</label>
       <select name="chica_id" id="chica_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
           <option value="">Seleccionar chica</option>
           @foreach($chicas->whereIn('estadop', [1, 3]) as $chica)
               <option value="{{ $chica->id }}">{{ $chica->nombre }}</option>
           @endforeach
       </select>
   </div>
@endif

                @if($usuarioAutenticado->rol == 1)
                <div class="form-group mb-4">
                    <div class="form-check">
                        <input type="checkbox"
                            class="form-check-input"
                            id="is_fixed"
                            name="is_fixed"
                            value="1"
                            {{ old('is_fixed', isset($post) && $post->is_fixed ? 'checked' : '') }}>
                        <label class="form-check-label" for="is_fixed">
                            <i class="fas fa-thumbtack me-1"></i> Fijar post en el foro
                        </label>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Post
                    </button>
                    <button type="button" onclick="mostrarListado()" class="text-gray-500 hover:text-gray-700 font-medium">
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