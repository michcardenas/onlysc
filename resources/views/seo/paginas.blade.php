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

<section class="seo-section">
    <h1 class="seo-title">Bienvenido a la página de SEO</h1>
    <div class="seo-buttons">
        <a href="{{ route('seo.home') }}" class="btn-seo">Inicio</a>
        <a href="{{ route('seo.template') }}" class="btn-seo">Filtros H2</a>
        <a href="{{ route('seo.foroadmin') }}" class="btn-seo">Foro</a>
        <a href="{{ route('seo.blogadmin') }}" class="btn-seo">Blog</a>
        <a href="{{ route('seo.publicate.form') }}" class="btn-seo">Publicar</a>
    </div>
</section>


<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Only scorts chile. Todos los derechos reservados.</p>
</footer>

<style>
    .seo-title {
        color: white;
        text-align: center;
        margin-top: 20px;
        font-size: 24px;
    }

    .seo-buttons {
        text-align: center;
        margin-top: 20px;
    }

    .btn-seo {
        display: inline-block;
        margin: 0 10px;
        padding: 10px 20px;
        background-color: #f1033d;
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-seo:hover {
        background-color: #d00235;
        transform: scale(1.05);
    }
</style>
@endsection
