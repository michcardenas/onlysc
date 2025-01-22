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


<main>
    <section>
        <h2>Administración de Foros</h2>
        @if($usuarioAutenticado->rol == 1)
        <div class="admin-actions">
            <a href="{{ route('foroadmin.create') }}" class="btn" style="background-color: #e00037; color: white;">
                Crear Nuevo Foro
            </a>
            <a href="{{ route('foroadmin.posts') }}" class="btn" style="background-color: #e00037; color: white;">
                Ver Todos los Posts
            </a>
        </div>
        @endif

        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        <table class="table-admin">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($foros as $foro)
                <tr>
                    <td>
                        @if($usuarioAutenticado->rol == 1)
                            <a href="{{ route('foroadmin.edit', $foro->id) }}">
                                {{ $foro->titulo }}
                            </a>
                        @else
                            {{ $foro->titulo }}
                        @endif
                    </td>
                    <td>
                        @if($usuarioAutenticado->rol == 1)
                            <a href="{{ route('foroadmin.edit', $foro->id) }}">
                                {{ $foro->subtitulo }}
                            </a>
                        @else
                            {{ $foro->subtitulo }}
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($foro->fecha)->format('d/m/Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('foroadmin.posts', ['id_blog' => $foro->id]) }}" class="btn-view">
                                Ver Posts
                            </a>
                            @if($usuarioAutenticado->rol == 1)
                            <form action="{{ route('foroadmin.destroy', $foro->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn-delete"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este foro?')">
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</main>


<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection