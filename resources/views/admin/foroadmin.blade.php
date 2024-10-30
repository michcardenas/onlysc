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

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Administración de Foros</h2>
                <a href="{{ route('foroadmin.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Crear Nuevo Foro
                </a>
            </div>

            @if(session('success'))
                <div>
                    {{ session('success') }}
                </div>
            @endif

            <main class="mt-6">
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
                                    <form action="{{ route('foroadmin.destroy', $foro->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 mx-2"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar este foro?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</div>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
