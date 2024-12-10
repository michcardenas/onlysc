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

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
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
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
        </div>
    </nav>
</header>

<main class="main-admin">
    <section class="form-section">
        <h2>Crear Foro</h2>

        <form action="{{ route('foroadmin.store') }}" method="POST" enctype="multipart/form-data" class="form-admin">
            @csrf

            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required>
            </div>

            <div class="form-group">
                <label for="subtitulo">Subtítulo</label>
                <input type="text" name="subtitulo" value="{{ old('subtitulo') }}" required>
            </div>

            <div class="form-group">
                <label for="contenido">Contenido</label>
                <textarea id="contenido" name="contenido" rows="5" required>{{ old('contenido') }}</textarea>
            </div>

            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="file" name="foto" accept="image/*" required>
            </div>


            <button type="submit" class="btn-submit">Crear Foro</button>
        </form>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection