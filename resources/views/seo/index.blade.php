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
            <li><a href="{{ route('seo') }}">SEO</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
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
    <a href="{{ route('seo.paginas') }}" class="btn-seo">Página</a>
    <a href="https://search.google.com/search-console/" target="_blank" class="btn-seo">Google Console</a>
        <a href="https://analytics.google.com/" target="_blank" class="btn-seo">Analytics</a>
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