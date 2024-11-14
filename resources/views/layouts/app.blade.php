<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Título de la pestaña -->
    <title>Escorts</title>

    <!-- Icono de la pestaña (favicon) -->
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">


    <!--Iconos-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Poppins" rel="stylesheet">

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.escortperfil-banner-slider');
    const slides = document.querySelectorAll('.escortperfil-banner-img');
    const prevBtn = document.querySelector('.carousel-control.prev');
    const nextBtn = document.querySelector('.carousel-control.next');
    const indicators = document.querySelectorAll('.carousel-indicator');
    let currentSlide = 0;

    // Función para actualizar las clases active
    function updateActiveClasses() {
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });
        
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });
    }

    // Función para ir a un slide específico
    function goToSlide(index) {
        currentSlide = index;
        const offset = -(currentSlide * (100 / 3)); // Ajustado para el nuevo layout
        slider.style.transform = `translateX(${offset}vw)`;
        updateActiveClasses();
    }

    // Inicializar el primer slide como activo
    updateActiveClasses();

    // Event listeners
    prevBtn.addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        goToSlide(currentSlide);
    });

    nextBtn.addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % slides.length;
        goToSlide(currentSlide);
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            goToSlide(index);
        });
    });
});



    
</script>

</body>

</html>