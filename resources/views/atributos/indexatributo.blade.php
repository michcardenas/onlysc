@extends('layouts.app_login')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

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
            <p>Bienvenido, {{ Auth::user()->name }} ({{ Auth::user()->role == 1 ? 'Administrador' : 'Usuario' }})</p>
        </div>
    </nav>
</header>

<div class="container">
    <h1 style="color:white;">Lista de Atributos</h1>
    <div class="mb-3">
        <a href="{{ route('atributos.createatributo') }}" class="btn" style="background-color: #ff0f58; color: white;">
            Agregar Atributo
        </a>
    </div>

    <table class="table-admin">
        <thead>
            <tr>
                <th>Posición</th>
                <th>Nombre</th>
                <th>URL</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atributos->sortBy('posicion') as $atributo)
            <tr>
                <td>{{ $atributo->posicion ?? 'N/A' }}</td>
                <td>{{ $atributo->nombre }}</td>
                <td><span>https://onlyescorts.cl/{{ $atributo->url }}</span></td>
                <td>
                    <a href="{{ route('atributos.editatributo', $atributo->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('atributos.destroy', $atributo->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este atributo?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection