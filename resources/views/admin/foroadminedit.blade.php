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
        <h2>Editar Foro</h2>

        <form action="{{ route('foroadmin.update', $foro->id) }}" method="POST" enctype="multipart/form-data" class="form-admin">
            @csrf
            @method('PUT')

            <div class="form-group mb-4">
                <label for="id_blog" class="block text-sm font-medium text-gray-700">ID del Blog</label>
                <input type="text"
                    id="id_blog"
                    name="id_blog"
                    value="{{ $foro->id_blog }}"
                    readonly
                    class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-not-allowed">
                <span class="text-sm text-gray-500">
                </span>
                @error('id_blog')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" name="titulo"
                    id="titulo"
                    value="{{ old('titulo', $foro->titulo) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                @error('titulo')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="subtitulo" class="block text-sm font-medium text-gray-700">Subtítulo</label>
                <input type="text" name="subtitulo"
                    id="subtitulo"
                    value="{{ old('subtitulo', $foro->subtitulo) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                @error('subtitulo')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="foto" class="block text-sm font-medium text-gray-700">Foto</label>
                @if($foro->foto)
                <div class="mt-2 mb-4">
                    <img src="{{ asset('storage/' . $foro->foto) }}"
                        alt="Foto actual"
                        style="max-width: 200px; max-height: 150px;"
                        class="object-cover rounded"> <!-- Forzamos tamaño con CSS inline -->
                    <p class="mt-1 text-sm text-gray-500">Foto actual</p>
                </div>
                @endif
                <input type="file"
                    name="foto"
                    id="foto"
                    class="mt-1 block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-md file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100">
                @error('foto')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="contenido" class="block text-sm font-medium text-gray-700">Contenido</label>
                <textarea name="contenido"
                    id="contenido"
                    rows="6"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>{{ old('contenido', $foro->contenido) }}</textarea>
                @error('contenido')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                    class="btn-submit">
                    Guardar Cambios
                </button>
                <a href="{{ route('foroadmin') }}"
                    class="text-gray-500 hover:text-gray-700 font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection