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
        <h2>Editar Términos y Condiciones</h2>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('tycadmin.update') }}" method="POST" class="form-admin">
            @csrf
            @method('PUT')

            <div class="form-group mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text"
                    name="title"
                    id="title"
                    value="{{ old('title', $tyc->title ?? 'Términos y Condiciones') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700">Contenido</label>
                <textarea name="content"
                    id="content"
                    rows="6"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>{{ preg_replace('/<br\s*\/?>/', "\n", old('content', $tyc->content ?? 'OnlyEscorts está calificado con la etiqueta RTA. Padres, pueden bloquear fácilmente el acceso a este sitio. Por favor, lean esta página')) }}</textarea>
                @error('content')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                    class="btn-submit">
                    Guardar Cambios
                </button>
                <a href="{{ route('home') }}"
                    class="text-gray-500 hover:text-gray-700 font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} OnlyEscorts. Todos los derechos reservados.</p>
</footer>