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
    <!-- Logo y barra de búsqueda en la misma fila -->
    <div class="navbar-left">
        <a href="/" class="logo2c">
            <img src="{{ asset('images/logo_v2.png') }}" alt="Logo" class="logo2">
        </a>
        <div class="search-bar">
            <input type="text" placeholder="Buscar por nombre, servicio o atributo...">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <!-- Enlaces de navegación -->
    <div class="navbar-right">
        <a href="/" class="nav-link">INICIO</a>
        <a href="#" class="nav-link">CATEGORÍAS</a>
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
</body>
</html>
