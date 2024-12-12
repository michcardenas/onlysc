<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Only Escorts</title>
    
    <!-- Icono de la pestaña (favicon) -->
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">

    <!-- Transiciones -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

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
    <footer class="footerhome">
    <p>© 2024 Only Escorts | Todos los derechos reservados</p>
</footer>

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script>
document.addEventListener("DOMContentLoaded", function() {
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 3,
        spaceBetween: 20,
        loop: true,
        grabCursor: true,


        breakpoints: {
            1024: {
                slidesPerView: 3,
                spaceBetween: 30,
                centeredSlides: false
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 15,
                centeredSlides: false
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 1,
                effect: "coverflow", // Solo aquí aplicamos coverflow
                coverflowEffect: {
                    rotate: 0,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: false,
                },
                centeredSlides: true
            }
        }
    });
});
document.getElementById('location-form').addEventListener('submit', function(e) {
        const ciudadSelect = document.getElementById('ciudad');
        const ciudadNombre = ciudadSelect.value;

        if (!ciudadNombre) {
            e.preventDefault();
            alert('Por favor selecciona una ciudad antes de continuar.');
            return;
        }

        // Cambiar la acción del formulario para usar el nombre
        this.action = `/escorts-${ciudadNombre}`;
    });
</script>


</body>
</html>
