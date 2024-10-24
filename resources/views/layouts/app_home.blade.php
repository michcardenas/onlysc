<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Incluir Work Sans desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

</head>
<body class="custom-background">
    <header>
        <div class="logo-container1">
            <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
        </div>
    </header>

    <!-- Contenedor del selector centrado -->
    <main class="content">
        <div class="selector-container">
            <!-- El contenido del selector se carga desde la vista 'home.blade.php' -->
            @yield('selector')
        </div>

        <!-- Contenedor para las tarjetas -->
        <div class="cards-container">
            <!-- Aquí se insertan las tarjetas desde la vista 'home.blade.php' -->
            @yield('content')
        </div>
    </main>
    <footer class="footer">
    <p>© 2024 Only Escorts | Todos los derechos reservados</p>
</footer>

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 3,  // Número de tarjetas visibles
            spaceBetween: 0,   // Reducir espacio entre tarjetas
            loop: true,        // Deja habilitado el loop si lo prefieres
            grabCursor: true,  // Permite agarrar y arrastrar
            // Eliminamos paginación y flechas
            pagination: false,  
            navigation: false,  
        });

        document.getElementById('location-form').addEventListener('submit', function (e) {
        var select = document.getElementById('ciudad');
        if (!select.value) {
            e.preventDefault(); // Previene que el formulario se envíe
            window.location.href = '/inicio'; // Redirige a /inicio
        }
    });
    });

</script>


</body>
</html>
