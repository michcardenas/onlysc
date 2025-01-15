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
        </ul>
    </nav>
</header>

<div class="container mt-4">
    <h2 class="text-white">Crear Nueva Tarjeta</h2>
    <form action="{{ route('tarjetas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Título -->
        <div class="form-group">
            <label for="titulo" class="text-white">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <!-- Descripción -->
        <div class="form-group">
            <label for="descripcion" class="text-white">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
        </div>

        <!-- Enlace -->
        <div class="form-group">
    <label for="link">Enlace</label>
    <input type="url" name="link" id="link" class="form-control" placeholder="http://tusitio.com" required>
</div>

        <!-- Imagen -->
        <div class="form-group">
            <label for="imagen" class="text-white">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="form-control-file" required>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('tarjetas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
