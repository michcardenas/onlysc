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
        <div class="zonas-container">
    @foreach(['Zona Norte', 'Zona Centro', 'Zona Sur'] as $zona)
        @if(isset($ciudadesPorZona[$zona]) && $ciudadesPorZona[$zona]->isNotEmpty())
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
        @endif
    @endforeach
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
    function applyDynamicStyles(totalCards) {
        // Configuraciones específicas para diferentes números de tarjetas
        const styleConfigs = {
            10: {
                '320-360': { width: '426%', cardWidth: '18rem' },
                '360-420': { width: '430%', cardWidth: '19rem' },
                '420-480': { width: '388%', cardWidth: '20rem' },
                '480-600': { width: '190%', cardWidth: '22rem' },
                '600-768': { width: '170%', cardWidth: '23rem' },
                '768-1024': { width: '252%' },
                '1024-1200': { width: '217%' },
                '1200-1400': { width: '161%' },
                '1400-1920': { width: '206%' },
                '1920+': { width: '190%' }
            },
            9: {
                '320-360': { width: '332%', cardWidth: '18rem' },
                '360-420': { width: '324%', cardWidth: '19rem' },
                '420-480': { width: '292%', cardWidth: '20rem' },
                '480-600': { width: '259%', cardWidth: '22rem' },
                '600-768': { width: '160%', cardWidth: '23rem' },
                '768-1024': { width: '197%' },
                '1024-1200': { width: '180%' },
                '1200-1400': { width: '135%' },
                '1400-1920': { width: '180%' },
                '1920+': { width: '180%' }
            },
            8: {
                '320-360': { width: '280%', cardWidth: '18rem' },
                '360-420': { width: '343%', cardWidth: '19rem' },
                '420-480': { width: '306%', cardWidth: '20rem' },
                '480-600': { width: '170%', cardWidth: '22rem' },
                '600-768': { width: '150%', cardWidth: '23rem' },
                '768-1024': { width: '201%' },
                '1024-1200': { width: '184%' },
                '1200-1400': { width: '140%' },
                '1400-1920': { width: '159%' },
                '1920+': { width: '170%' }
            },
            7: {  // Tus estilos exactos actuales
                '320-360': { width: '260%', cardWidth: '18rem' },
                '360-420': { width: '294%', cardWidth: '19rem' },
                '420-480': { width: '262%', cardWidth: '20rem' },
                '480-600': { width: '150%', cardWidth: '22rem' },
                '600-768': { width: '130%', cardWidth: '23rem' },
                '768-1024': { width: '173%' },
                '1024-1200': { width: '150%' },
                '1200-1400': { width: '135%' },
                '1400-1920': { width: '137%' },
                '1920+': { width: '150%' }
            },
            6: {
                '320-360': { width: '336%', cardWidth: '18rem' },
                '360-420': { width: '343%', cardWidth: '19rem' },
                '420-480': { width: '307%', cardWidth: '20rem' },
                '480-600': { width: '261%', cardWidth: '22rem' },
                '600-768': { width: '120%', cardWidth: '23rem' },
                '768-1024': { width: '129%' },
                '1024-1200': { width: '182%' },
                '1200-1400': { width: '136%' },
                '1400-1920': { width: '115%' },
                '1920+': { width: '102%' }
            },
            5: {
                '320-360': { width: '96%', cardWidth: '18rem' },
                '360-420': { width: '96%', cardWidth: '19rem' },
                '420-480': { width: '84%', cardWidth: '20rem' },
                '480-600': { width: '130%', cardWidth: '22rem' },
                '600-768': { width: '110%', cardWidth: '23rem' },
                '768-1024': { width: '144%' },
                '1024-1200': { width: '151%' },
                '1200-1400': { width: '112%' },
                '1400-1920': { width: '98%' },
                '1920+': { width: '130%' }
            },
            4: {
                '320-360': { width: '200%', cardWidth: '18rem' },
                '360-420': { width: '185%', cardWidth: '19rem' },
                '420-480': { width: '175%', cardWidth: '20rem' },
                '480-600': { width: '120%', cardWidth: '22rem' },
                '600-768': { width: '100%', cardWidth: '23rem' },
                '768-1024': { width: '110%' },
                '1024-1200': { width: '120%' },
                '1200-1400': { width: '118%' },
                '1400-1920': { width: '98%' },
                '1920+': { width: '115%' }
            },
            3: {
                '320-360': { width: '237%', cardWidth: '18rem' },
                '360-420': { width: '224%', cardWidth: '19rem' },
                '420-480': { width: '225%', cardWidth: '20rem' },
                '480-600': { width: '110%', cardWidth: '22rem' },
                '600-768': { width: '90%', cardWidth: '23rem' },
                '768-1024': { width: '141%' },
                '1024-1200': { width: '110%' },
                '1200-1400': { width: '80%' },
                '1400-1920': { width: '100%' },
                '1920+': { width: '110%' }
            },
            2: {
                '320-360': { width: '237%', cardWidth: '18rem' },
                '360-420': { width: '94%', cardWidth: '19rem' },
                '420-480': { width: '87%', cardWidth: '20rem' },
                '480-600': { width: '110%', cardWidth: '22rem' },
                '600-768': { width: '90%', cardWidth: '23rem' },
                '768-1024': { width: '141%' },
                '1024-1200': { width: '110%' },
                '1200-1400': { width: '80%' },
                '1400-1920': { width: '63%' },
                '1920+': { width: '110%' }
            }
        };

        // Obtener la configuración para el número de tarjetas actual
        const config = styleConfigs[totalCards] || styleConfigs[7];

        // Crear los estilos dinámicos
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            @media (min-width: 320px) and (max-width: 360px) {
                .swiper-container {
                    width: ${config['320-360'].width};
                    padding: 20px 0;
                }
                .swiper-container .card {
                    width: ${config['320-360'].cardWidth};
                }
                .swiper-container .card:hover {
                    transform: none;
                    box-shadow: none;
                }
            }

            @media (min-width: 360px) and (max-width: 420px) {
                .swiper-container {
                    width: ${config['360-420'].width};
                    padding: 20px 0;
                }
                .swiper-container .card {
                    width: ${config['360-420'].cardWidth};
                }
            }

            @media (min-width: 420px) and (max-width: 480px) {
                .swiper-container {
                    width: ${config['420-480'].width};
                    padding: 20px 0;
                }
                .swiper-container .card {
                    width: ${config['420-480'].cardWidth};
                }
            }

            @media (min-width: 480px) and (max-width: 600px) {
                .swiper-container {
                    width: ${config['480-600'].width};
                    padding: 25px 0;
                }
                .swiper-container .card {
                    width: ${config['480-600'].cardWidth};
                }
            }

            @media (min-width: 600px) and (max-width: 768px) {
                .swiper-container {
                    width: ${config['600-768'].width};
                    padding: 30px 0;
                }
                .swiper-container .card {
                    width: ${config['600-768'].cardWidth};
                }
            }

            @media (min-width: 768px) and (max-width: 1024px) {
                .swiper-container {
                    width: ${config['768-1024'].width};
                    padding: 30px 0;
                }
                .swiper-container .card:hover {
                    transform: scale(0.95);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }

            @media (min-width: 1024px) and (max-width: 1200px) {
                .swiper-container {
                    width: ${config['1024-1200'].width};
                    padding: 35px 0;
                }
                       .swiper-container .card:hover {
                    transform: scale(0.95);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }

            @media (min-width: 1200px) and (max-width: 1400px) {
                .swiper-container {
                    width: ${config['1200-1400'].width};
                    padding: 35px 0;
                }
                       .swiper-container .card:hover {
                    transform: scale(0.95);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }

            @media (min-width: 1400px) and (max-width: 1920px) {
                .swiper-container {
                    width: ${config['1400-1920'].width};
                    padding: 40px 0;
                }
                       .swiper-container .card:hover {
                    transform: scale(0.95);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }

            @media (min-width: 1920px) {
                .swiper-container {
                    width: ${config['1920+'].width};
                    padding: 60px 0;
                }
                       .swiper-container .card:hover {
                    transform: scale(0.95);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                }
            }
        `;

        // Eliminar estilos anteriores si existen
        const oldStyle = document.getElementById('dynamic-carousel-styles');
        if (oldStyle) {
            oldStyle.remove();
        }

        styleSheet.id = 'dynamic-carousel-styles';
        document.head.appendChild(styleSheet);
    }

    // Contar el número de tarjetas
    var totalTarjetas = document.querySelectorAll('.swiper-wrapper .swiper-slide').length;

    // Aplicar estilos dinámicos iniciales
    applyDynamicStyles(totalTarjetas);

    // Actualizar estilos cuando cambie el tamaño de la ventana
    window.addEventListener('resize', function() {
        applyDynamicStyles(totalTarjetas);
    });
    // Contar el número de tarjetas
    var totalTarjetas = document.querySelectorAll('.swiper-wrapper .swiper-slide').length;

// Calcular slidesPerView según el número de tarjetas
var slidesPerView, slidesPerViewMedium, slidesPerViewSmall;

if (totalTarjetas >= 10) {
    slidesPerView = 9.5;
    slidesPerViewMedium = 6.5;
    slidesPerViewSmall = 4.5;
} else if (totalTarjetas === 9) {
    slidesPerView = 8.5;
    slidesPerViewMedium = 5.5;
    slidesPerViewSmall = 3.5;
} else if (totalTarjetas === 8) {
    slidesPerView = 7.5;
    slidesPerViewMedium = 5.5;
    slidesPerViewSmall = 3.5;
} else if (totalTarjetas === 7) {
    slidesPerView = 6.5;
    slidesPerViewMedium = 5.5;
    slidesPerViewSmall = 3;
} else if (totalTarjetas === 6) {
    slidesPerView = 5.5;
    slidesPerViewMedium = 5.5;
    slidesPerViewSmall = 3.5;
} else if (totalTarjetas === 5) {
    slidesPerView = 4.5;
    slidesPerViewMedium = 4.5;
    slidesPerViewSmall = 2.5;
} else if (totalTarjetas === 4) {
    slidesPerView = 3.5;
    slidesPerViewMedium = 3.5;
    slidesPerViewSmall = 2;
} else if (totalTarjetas === 3) {
    slidesPerView = 2.5;
    slidesPerViewMedium = 2.5;
    slidesPerViewSmall = 2.5;
} else {
    slidesPerView = 1.5; // Por defecto
    slidesPerViewMedium = 1.5;
    slidesPerViewSmall = 1;
}
console.log(totalTarjetas, slidesPerView, slidesPerViewMedium, slidesPerViewSmall);
// Inicializar Swiper con la configuración dinámica
var swiper = new Swiper('.swiper-container', {
    slidesPerView: slidesPerView,
    spaceBetween: 20,
    loop: totalTarjetas > slidesPerView, // Loop solo si hay más tarjetas que slides visibles
    grabCursor: true,
    centeredSlides: slidesPerView < 3, // Centrar solo si hay menos de 3 slides visibles
    initialSlide: 1,
    breakpoints: {
        1424: {
            slidesPerView: slidesPerView, // Para pantallas grandes
            spaceBetween: 30,
            centeredSlides: false,
        },
        1024: {
            slidesPerView: slidesPerViewMedium, // Para pantallas medianas
            spaceBetween: 20,
            centeredSlides: false,
        },
        768: {
            slidesPerView: slidesPerViewSmall, // Para pantallas pequeñas
            spaceBetween: 15,
            centeredSlides: false,
        },
        320: {
            slidesPerView: slidesPerViewSmall, // Para pantallas móviles
            spaceBetween: 10,
            effect: "coverflow", // Solo aquí aplicamos coverflow
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
            },
            centeredSlides: true,
        },
    },
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
