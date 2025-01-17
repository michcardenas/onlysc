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
    @if(isset($meta))
        <title>{{ $meta->meta_title }}</title>
        <meta name="description" content="{{ $meta->meta_description }}">
        <meta name="keywords" content="{{ $meta->meta_keywords }}">
        <meta name="robots" content="{{ $meta->meta_robots }}">
        @if($meta->canonical_url)
            <link rel="canonical" href="{{ $meta->canonical_url }}">
        @endif
    @endif
    <style>
    .visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0,0,0,0) !important;
    white-space: nowrap !important;
    border: 0 !important;
    opacity: 0;
}

.seo-container {
    position: absolute;
    overflow: hidden;
    clip: rect(0 0 0 0);
    height: 1px;
    width: 1px;
    margin: -1px;
    padding: 0;
    border: 0;
}
.meta-inicio-titulos{
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #fff;
    
   
}
.zonas-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.zona-card {
    background: rgba(255, 255, 255, 0.1); /* Fondo semitransparente */
    color: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); /* Sombra para efecto de profundidad */
    backdrop-filter: blur(10px); /* Efecto de desenfoque */
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2); /* Borde ligero */
    width: 250px;
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.zona-card:hover {
    transform: scale(1.05); /* Aumenta ligeramente el tamaño al pasar el cursor */
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2); /* Más profundidad */
}

.zona-card h3 {
    font-size: 1.5em;
    border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    margin-bottom: 10px;
    padding-bottom: 5px;
    font-weight: bold;
}

.zona-card ul {
    list-style: none;
    padding: 0;
}

.zona-card ul li {
    margin-bottom: 10px;
}

.zona-card ul li a {
    color: #fff; /* Texto blanco */
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease-in-out;
}

.zona-card ul li a:hover {
    color: #ddd; /* Cambia el color del texto al pasar el cursor */
}
/* Estilo para el wrapper */
.wrapper {
    display: flex;
    justify-content: center; /* Centrar horizontalmente */
    align-items: center; /* Centrar verticalmente */
    width: 100%; /* Ancho completo */
    margin: 0;
    padding: 0;
    background: url('https://via.placeholder.com/1920x1080') no-repeat center center/cover; /* Fondo de ejemplo */
}

/* Estilo para el contenedor glass */
.glass-container {
    background: rgba(255, 255, 255, 0.1); /* Transparencia para glassmorphism */
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(10px); /* Difuminado */
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2); /* Borde tenue */
    max-width: 800px; /* Ancho máximo */
    text-align: center;
    color: white; /* Texto blanco */
    width: 100%;
    height: auto;
    min-height: max-content;
    position: relative;
    overflow: visible;
}


    .mission-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .mission-text,
.extended-text {
    width: 100%;
    height: auto;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
}

    .mission-text.expanded {
        max-height: 300px; /* Altura expandida */
    }

    .btn-glass {
        margin-top: 20px;
        padding: 10px 20px;
        border: none;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-weight: bold;
        border-radius: 25px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.3s ease;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(1.05);
    }
    .content-wrapper {
    width: 100%;
    height: auto;
    position: relative;
}
.custom-background {
    background-image: url('{{ $meta->fondo ? asset('storage/' . $meta->fondo) : asset('images/IMAGEN-scaled-e1727837771908.jpg') }}');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    height: 100vh;
    font-family: 'Montserrat', sans-serif;
}
.preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.preloader.hidden {
    opacity: 0;
    pointer-events: none;
}

.loader-content {
    text-align: center;
}

.loading-logo {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    animation: pulseOnce 2s ease-out forwards;
}

.loading-text {
    color: #ffffff;
    margin-top: 20px;
    font-family: 'Work Sans', sans-serif;
    font-size: 1.2em;
}

/* Animación para un solo "latido" */
@keyframes pulseOnce {
    0% {
        transform: scale(1);
        opacity: 1; /* Visible desde el inicio */
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1; /* Mantiene visibilidad */
    }
}
/* Estilos generales */
.swiper {
    width: 100%;
    height: auto;
}

.swiper-wrapper {
    display: flex;
}

.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Estilos de las tarjetas */
.zona-card {
    flex: 0 0 auto;
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
/* Estilos base del carrusel */
.home-swiper {
    width: 100%;
    padding: 20px 0;
}

/* Estilos de las tarjetas */

.card:hover {
    transform: translateY(-5px);
}

.card-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px 8px 0 0;
}

.card-content {
    padding: 15px;
}

.card-title {
    font-size: 1.2rem;
    margin: 10px 0;
    color: #333;
}

.card-description {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
    line-height: 1.4;
}

/* Estilos de la paginación */
.swiper-pagination {
    position: relative;
    margin-top: 20px;
}

.swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #ddd;
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background: #007bff;
}

/* Media queries para ajustar el tamaño de las tarjetas según el número */
@media (max-width: 567px) {
    .home-swiper {
        padding: 0;
    }
    
    .swiper-wrapper {
        display: flex;
        align-items: center;
    }
    
    .swiper-slide {
        width: 100% !important; /* Ancho fijo para móviles */
        margin: 0 auto;
    }
    
    .card {
        margin: 0 auto;
        width: 100%;
    }
    
    /* Aseguramos que el contenedor principal no tenga márgenes que afecten el centrado */
    .swiper-container {
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        list-style: none;
        padding: 0;
    }
}

@media (min-width: 568px) and (max-width: 767px) {
    .swiper-slide {
        width: calc(33.33% - 20px);
        margin: 0 10px;
    }
    .carru{
        width: 32rem;
    }
    .card img {
        height: 34rem;
    }
    .home-swiper{
        width: 60%;
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .swiper-slide {
        width: calc(33.33% - 20px);
        margin: 0 10px;
    }
}

@media (min-width: 1024px) {
    .swiper-slide {
        width: calc(25% - 20px);
        margin: 0 10px;
    }
}
/* Responsivo */
@media (max-width: 768px) {
    .zona-card {
        width: 90%;
    }
}



    </style>

</head>
<body class="custom-background">
<div id="preloader" class="preloader">
    <div class="loader-content">
    <img src="{{ asset('images/icono.png') }}" alt="Cargando..." class="loading-logo" />
    <p class="loading-text">onlyescorts.cl</p>
    </div>
</div>
    <header>
        <div class="logo-container1">
            <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
        </div>
    </header>

    <!-- Contenedor del selector centrado -->
    <main class="content">
    <div class="meta-inicio-titulos">
    @if(isset($meta))
                @if($meta->heading_h1)
                    <h1>{{ $meta->heading_h1 }}</h1>
                @endif

                @if($meta->heading_h2)
                    <h2>{{ $meta->heading_h2 }}</h2>
                @endif
            @endif
    </div>
        <div class="selector-container">
            <!-- El contenido del selector se carga desde la vista 'home.blade.php' -->
            @yield('selector')
        </div>
        <div class="swiper swiper-zonas">
    <div class="swiper-wrapper">
        @foreach(['Zona Norte', 'Zona Centro', 'Zona Sur'] as $zona)
            @if(isset($ciudadesPorZona[$zona]) && $ciudadesPorZona[$zona]->isNotEmpty())
                <div class="swiper-slide">
                    <div class="zona-card glass">
                        <h3>{{ $zona }}</h3>
                        <ul>
                            @foreach($ciudadesPorZona[$zona]->sortBy('posicion') as $ciudad)
                                <li>
                                    <a href="/escorts-{{ strtolower($ciudad->url) }}">{{ $ciudad->nombre }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Paginación -->
    <div class="swiper-pagination zonas-pagination"></div>
    <!-- Botones de navegación -->
    <div class="swiper-button-prev zonas-prev"></div>
    <div class="swiper-button-next zonas-next"></div>
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

function debugLog(message) {
        console.log(`[Preloader Debug] ${message}`);
    }

    document.addEventListener('DOMContentLoaded', function() {
          // Carrusel de Zonas
    const swiperZonas = new Swiper('.swiper-zonas', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 10,
        pagination: {
            el: '.zonas-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.zonas-next',
            prevEl: '.zonas-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
        },
    });

    // Otro Carrusel (Ejemplo: Productos)
    const swiperProductos = new Swiper('.swiper-productos', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 10,
        pagination: {
            el: '.productos-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.productos-next',
            prevEl: '.productos-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 4, // Por ejemplo, 4 productos visibles en pantallas grandes
                spaceBetween: 30,
            },
        },
    });




        //LOADERRRR
        debugLog('DOMContentLoaded ejecutado');
        
        const preloader = document.getElementById('preloader');
        const progressSpan = document.getElementById('progress');
        
        if (!preloader) {
            debugLog('ERROR: No se encontró el elemento preloader');
            return;
        }
        
        debugLog('Preloader encontrado');
        
        // Forzar visibilidad inicial
        preloader.style.display = 'flex';
        debugLog('Preloader display establecido a flex');

        // Contar imágenes
        const images = document.querySelectorAll('img');
        let loadedImages = 0;
        const totalImages = images.length;
        
        debugLog(`Total de imágenes a cargar: ${totalImages}`);
        
        function updateProgress() {
            const progress = Math.round((loadedImages / totalImages) * 100);
            if (progressSpan) {
                progressSpan.textContent = `${progress}%`;
            }
            debugLog(`Progreso: ${progress}%`);
        }

        function imageLoaded() {
            loadedImages++;
            updateProgress();
            debugLog(`Imagen cargada (${loadedImages}/${totalImages})`);
            
            if (loadedImages === totalImages) {
                debugLog('Todas las imágenes cargadas');
                hidePreloader();
            }
        }

        function hidePreloader() {
            debugLog('Intentando ocultar preloader');
            if (preloader && !preloader.classList.contains('hidden')) {
                preloader.classList.add('hidden');
                debugLog('Clase hidden añadida');
                
                setTimeout(() => {
                    preloader.style.display = 'none';
                    debugLog('Preloader ocultado completamente');
                }, 500);
            }
        }

        // Verificar cada imagen
        images.forEach((img, index) => {
            debugLog(`Verificando imagen ${index + 1}/${totalImages}`);
            if (img.complete) {
                debugLog(`Imagen ${index + 1} ya estaba cargada`);
                imageLoaded();
            } else {
                img.addEventListener('load', () => {
                    debugLog(`Imagen ${index + 1} cargada con evento`);
                    imageLoaded();
                });
                img.addEventListener('error', () => {
                    debugLog(`Error al cargar imagen ${index + 1}`);
                    imageLoaded();
                });
            }
        });

        // Failsafe timer
        setTimeout(() => {
            debugLog('Tiempo de espera máximo alcanzado');
            hidePreloader();
        }, 5000);
    });

    // Debug adicional para carga de página
    window.addEventListener('load', function() {
        debugLog('Evento window.load ejecutado');
    });

document.addEventListener("DOMContentLoaded", function() {
    const preloader = document.getElementById('preloader');
        const images = document.querySelectorAll('img');
        let loadedImages = 0;
        
        function imageLoaded() {
            loadedImages++;
            if (loadedImages === images.length) {
                hidePreloader();
            }
        }
        
        images.forEach(img => {
            if (img.complete) {
                imageLoaded();
            } else {
                img.addEventListener('load', imageLoaded);
                img.addEventListener('error', imageLoaded);
            }
        });
        
        setTimeout(hidePreloader, 5000);
        
        function hidePreloader() {
            if (preloader && !preloader.classList.contains('hidden')) {
                preloader.classList.add('hidden');
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        }

    //vermas
    const btnVerMas = document.getElementById('btn-ver-mas');
    const additionalText = document.getElementById('additional-text');
    // Verificar si hay contenido adicional
    if (additionalText.textContent.trim()) {
        additionalText.style.display = 'none'; // Ocultar inicialmente si hay contenido
    } else {
        btnVerMas.style.display = 'none'; // Ocultar el botón si no hay contenido
    }

    btnVerMas.addEventListener('click', function() {
        const isHidden = additionalText.style.display === 'none';
        additionalText.style.display = isHidden ? 'block' : 'none';
        btnVerMas.textContent = isHidden ? 'Ver menos' : 'Ver más';
    });

        //CARRUSEL
     // Configuración del carrusel
const homeSwiper = new Swiper('.home-swiper', {
    loop: true,
    slidesPerView: 1, // Esto permite que el número de slides sea dinámico
    spaceBetween: 8,
    centeredSlides: true, // Centramos las slides en móviles
    slideToClickedSlide: true,
    snapOnRelease: true,
    snapGrid: [8], // Coincide con el spaceBetween
    threshold: 5, // Sensibilidad del deslizamiento
     // Mejora la precisión del deslizamiento
     watchSlidesProgress: true,
    normalizeSlideIndex: true,
    
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true, // Bullets dinámicos que se ajustan al número de slides
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        568: {
            slidesPerView: 4.2,
          
        },
        570: {
            slidesPerView: 4.2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 4.2,
            spaceBetween: 20,
        },
    },
    on: {
        init: function() {
            adjustPagination(this);
        },
        resize: function() {
            adjustPagination(this);
        }
    }
});

// Función para ajustar la paginación según el número de tarjetas
function adjustPagination(swiper) {
    const totalSlides = swiper.slides.length;
    const paginationEl = swiper.pagination.el;
    
    if (totalSlides <= 1) {
        paginationEl.style.display = 'none';
    } else {
        paginationEl.style.display = 'block';
    }
}
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
