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
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p>Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 1 ? 'Administrador' : 'Usuario' }})</p>
        </div>
    </nav>
</header>

<main>
    <section>
        <h2>Administración de Foros</h2>
        <div class="admin-actions">
            <a href="{{ route('foroadmin.create') }}" class="btn-admin">
                Crear Nuevo Foro
            </a>
            <a href="{{ route('foroadmin.posts') }}" class="btn-admin btn-posts">
                Ver Todos los Posts
            </a>
        </div>

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
                            <a href="{{ route('foroadmin.edit', $foro->id) }}">
                                {{ $foro->titulo }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('foroadmin.edit', $foro->id) }}">
                                {{ $foro->subtitulo }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($foro->fecha)->format('d/m/Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('foroadmin.posts', ['id_blog' => $foro->id]) }}" class="btn-view">
                                    Ver Posts
                                </a>
                                <form action="{{ route('foroadmin.destroy', $foro->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn-delete"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este foro?')">
                                        Eliminar
                                    </button>
                                </form>
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
