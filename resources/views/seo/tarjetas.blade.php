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
            <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
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
            @if(isset($usuarioAutenticado))
                <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} 
                ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
            @else
                <p style="color:white;">Usuario no autenticado</p>
            @endif
        </div>
    </nav>
</header>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-white">Editar Tarjetas</h2>
        <a href="{{ route('tarjetas.create') }}" class="btn btn-primary">Crear Nueva Tarjeta</a>
    </div>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Enlace</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tarjetas as $tarjeta)
            <tr>
                <td>{{ $tarjeta->id }}</td>
                <td>{{ $tarjeta->titulo }}</td>
                <td>{{ $tarjeta->descripcion }}</td>
                <td><a href="{{ $tarjeta->enlace }}" target="_blank">{{ $tarjeta->link }}</a></td>
                <td><img src="{{ asset('storage/' . $tarjeta->imagen) }}" alt="Imagen" width="100">
                </td>
                <td>
                    <!-- Botón para editar -->
                    <a href="{{ route('tarjetas.edit', $tarjeta->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <!-- Botón para eliminar -->
                    <form action="{{ route('tarjetas.destroy', $tarjeta->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta tarjeta?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
