<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

</head>
<body>
<header>
    <nav class="navbar navbar-top">
        <!-- Sección izquierda con el botón "PUBLICATE" -->
        <div class="navbar-left">
            <button class="btn-publish">PUBLICATE</button>
        </div>

        <!-- Sección derecha con el selector de ubicación y botones de login/register -->
        <div class="navbar-right">
        <div class="location-dropdown">
            <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon">
            <select name="location" id="location">
                <option value="" disabled selected>Seleccionar ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id }}">{{ ucfirst($ciudad->nombre) }}</option>
                @endforeach
            </select>
        </div>



            <!-- Verificamos si el usuario está autenticado -->
            @if(Auth::check())
                <a class="btn-user">{{ Auth::user()->name }}</a>
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button class="btn-logout">SALIR</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">ACCEDER</a>
                <a href="{{ route('register') }}" class="btn-register">REGISTRARSE</a>
            @endif
        </div>
       

    </nav>
    <div class="navbar-bottom">
    <!-- Logo, barra de búsqueda y botón de filtros -->
    <div class="navbar-left">
        <a href="/" class="logo">
            <img src="{{ asset('images/logo_v2.png') }}" alt="Logo" class="logo1">
        </a>

        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <input type="text" placeholder="Buscar por nombre, servicio o atributo...">
            <button type="submit" class="btn-search">
                <img src="{{ asset('images/search.svg') }}" alt="search icon" class="search-icon">
            </button>
        </div>

        <!-- Botón de Filtros -->
        <button class="btn-filters">
            <img src="{{ asset('images/filtro.svg') }}" alt="Filtros" class="icon-filter"> Filtros
        </button>
    </div>

    <!-- Enlaces de navegación con Select de CATEGORÍAS -->
    <div class="navbar-right">
        <a href="/" class="nav-link">INICIO</a>

        <!-- Select de categorías -->
        <select name="categorias" id="categorias" class="nav-link">
            <option value="" disabled selected>CATEGORÍAS</option>
            <option value="DELUXE">DELUXE</option>
            <option value="VIP">VIP</option>
            <option value="PREMIUM">PREMIUM</option>
            <option value="MASAJES">MASAJES</option>
        </select>

        <a href="#" class="nav-link">FAVORITOS</a>
        <a href="#" class="nav-link">BLOG</a>
        <a href="#" class="nav-link">FORO</a>
    </div>
</div>



</header>

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <footer class="footer">

   <div class="footer-top">
        <div class="container">
        <h3 class="footer-title">Escorts disponibles por ubicación</h3>

            <div class="locations-grid">
                <ul>
                    <li><a href="#">Escorts en Santiago</a></li>
                    <li><a href="#">Escorts en Antofagasta</a></li>
                    <li><a href="#">Escorts en Calama</a></li>
                    <li><a href="#">Escorts en Chillan</a></li>
                </ul>
                <ul>
                    <li><a href="#">Escorts en Concepcion</a></li>
                    <li><a href="#">Escorts en Copiapo</a></li>
                    <li><a href="#">Escorts en Curico</a></li>
                    <li><a href="#">Escorts en Iquique</a></li>
                </ul>
                <ul>
                    <li><a href="#">Escorts en La Serena</a></li>
                    <li><a href="#">Escorts en Linares</a></li>
                    <li><a href="#">Escorts en Los Angeles</a></li>
                    <li><a href="#">Escorts en Osorno</a></li>
                </ul>
                <ul>
                    <li><a href="#">Escorts en Pucon</a></li>
                    <li><a href="#">Escorts en Puerto Montt</a></li>
                    <li><a href="#">Escorts en Punta Arenas</a></li>
                    <li><a href="#">Escorts en Quillque</a></li>
                </ul>
                <ul>
                    <li><a href="#">Escorts en Rancagua</a></li>
                    <li><a href="#">Escorts en San Fernando</a></li>
                    <li><a href="#">Escorts en Talca</a></li>
                    <li><a href="#">Escorts en Temuco</a></li>
                </ul>
                <ul>
                    <li><a href="#">Escorts en Valdivia</a></li>
                    <li><a href="#">Escorts en Viña del Mar</a></li>
                </ul>
            </div>
        </div>
    </div>
  <!-- Sección del logo, enlaces y redes sociales -->
<div class="custom-footer-bottom">
    <div class="custom-container">
        <!-- Logo centrado -->
        <div class="custom-footer-logo">
            <img src="{{ asset('images/logo_XL-2.png') }}" alt="OnlyEscorts Logo" class="custom-logo-footer">
        </div>

        <!-- Enlaces en tres filas -->
        <div class="custom-footer-middle">
            <div class="custom-links">
                <div class="custom-link-row">
                    <a href="#">FORO</a>
                    <a href="#">BLOG</a>
                </div>
                <div class="custom-link-row">
                    <a href="#">CONTACTO</a>
                    <a href="#">POLÍTICA DE PRIVACIDAD</a>
                </div>
                <div class="custom-link-row">
                    <a href="#">REGISTRO</a>
                    <a href="#">PUBLICATE</a>
                </div>
            </div>
        </div>

        <!-- Redes sociales centradas -->
        <div class="custom-social-links">
            <a href="#"><img src="{{ asset('images/facebook.svg') }}" alt="Facebook" class="custom-social-icon"></a>
            <a href="#"><img src="{{ asset('images/instagram.svg') }}" alt="Instagram" class="custom-social-icon"></a>
            <a href="#"><img src="{{ asset('images/x.svg') }}" alt="X" class="custom-social-icon"></a>
        </div>
    </div>
</div>


        <!-- Derechos reservados y aviso -->
        <div class="footer-info">
            <p>© 2024 Only Escorts | Todos los derechos reservados</p>
        </div>
    </div>
</footer>


</body>
</html>
