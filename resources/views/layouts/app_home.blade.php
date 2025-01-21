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
    margin-bottom: 4rem;

    
   
}
.zonas-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.zona-card {
    background: rgba(255, 255, 255, 0.5); /* Fondo semitransparente */
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
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    text-align: center;
}
.texto-adicional {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.8); /* Ajustar color del texto adicional */
    margin-bottom: 1rem;
    text-align: center;
}
.divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.3); /* Línea divisoria semitransparente */
    margin: 1rem 0;
}
.zona-card ul {
    list-style: none;
    padding: 0;
}

.zona-card ul li {
    margin-bottom: 10px;
}
.ciudades {
    font-size: 0.9rem;
    text-align: left;
    color: rgba(255, 255, 255, 0.9); /* Ajustar color del texto de las ciudades */
}

.ciudades a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
}

.ciudades a:hover {
    text-decoration: underline;
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
    margin-top: 50px;
    padding: 0;
    background: url('https://via.placeholder.com/1920x1080') no-repeat center center/cover; /* Fondo de ejemplo */
}

/* Estilo para el contenedor glass */
.glass-container {
    background: rgba(105, 93, 93, 0.1); /* Transparencia para glassmorphism */
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
    font-family: 'Montserrat';
}
.preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0, 0, 0);
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
    margin-bottom: 20px;
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
.zona-card p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
}

.zona-card a {
    color:rgb(242, 243, 243); /* Color azul para los enlaces */
    text-decoration: none;
}

.zona-card a:hover {
    text-decoration: underline;
    color: #e00037;
}

.zona-card {
    flex: 0 0 auto;
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-height: 20rem;
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
        width: 75%;
    }
}

/* Contenedor principal */
.glass-container {
    background: rgb(16 14 14);
        border-radius: 16px;
    padding: 24px;
    max-width: 900px;
    width: 100%;
    margin: 0 auto;
    color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

/* Estilo del título */
.mission-title {
    font-size: 3rem;
    line-height: 1.2;
    margin: 0;
    background: linear-gradient(90deg, #FF99CC, #33CCFF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: left;
    flex: 1; /* Ocupa más espacio a la izquierda */
}

/* Contenedor del texto y botón */
.content-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end; /* Texto y botón alineados a la derecha */
    text-align: right;
}

/* Estilo del texto */
.mission-text {
    line-height: 1.6;
    margin-bottom: 10px;
    text-align: right;
}

/* Estilo del botón */
.btn-glass {
    padding: 12px 24px;
    background-color: #e00037;
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
}

.btn-glass:hover {
    background-color:rgb(194, 0, 48);
}

/* Estilo del texto extendido (oculto por defecto) */
.extended-text {
    display: none;
}

/* Diseño responsivo */
@media (max-width: 768px) {

    .glass-container {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }

    .mission-title {
        font-size: 2rem;
        margin-bottom: 16px;
    }

    .content-wrapper {
        align-items: center;
    }

    .mission-text {
        text-align: left;
    }

    .btn-glass {
        width: 100%; /* Botón ocupa todo el ancho en móvil */
    }
    .meta-inicio-titulos .main-title {
        font-size: 2rem;
    }

    .meta-inicio-titulos .title-secondary h2 {
        font-size: 1.5rem;
    }
    
}
/* Estilo del título */
/* Estilo del título */
.tarjetasflex {
    display: flex;
    margin-bottom: 20px;
    width: 100%;
}
.titulo-tarjetas {
    font-size: 2.5rem; /* Tamaño del texto */
    font-weight: 400; /* Peso regular para el texto */
    color: #ffff; /* Color del texto principal */
    margin-bottom: 20px; /* Espaciado inferior */
}

/* Estilo para la última palabra destacada */
.titulo-tarjetas .highlight {
    font-weight: 700; /* Negrita */
    color: #e63946; /* Cambia este color según tu diseño */
}

.meta-inicio-titulos h1{
    font-size: 5rem;
  
}
.meta-inicio-titulos h2 {
    font-weight: normal;
    max-width: 50rem;
    margin: 0 auto;
}
.titulosec{
    text-align: justify !important;

}
 {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.meta-inicio-titulos .main-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}



.meta-inicio-titulos .title-secondary h2 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.4;
    font-weight: normal;

}
.content{
    background: #000;
}
.ciudades-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 20px;
    justify-content: flex-start;
}

.ciudad-item {
    width: calc(50% - 10px);
    margin-bottom: 8px;
}

/* Centramos el último elemento cuando es impar */
.ciudades-container .ciudad-item:last-child:nth-child(odd) {
    width: 60%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

/* Aseguramos que los enlaces mantengan sus estilos */
.ciudad-item a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ciudad-item a:hover {
    text-decoration: underline;
}

    </style>

</head>
<body class="custom-background">
<div id="preloader" class="preloader">
    <div class="loader-content">
    <img src="{{ asset('images/logo_S.png') }}" alt="Cargando..." class="loading-logo" />
    <p class="loading-text">onlyescorts.cl</p>
    </div>
</div>
<header style="position: fixed; top: 0; left: 0; width: 100%; background-color: #000; z-index: 1000; border-bottom: 1px solid #ffffff78;">
    <div class="logo-container1" style="padding-bottom: 6px; display: flex; justify-content: center; align-items: center;"> 
        <img src="{{ asset('images/logo_S.png') }}" alt="Logo" style="max-width: 200px; height: auto;">
    </div>
</header>

<div class="selector-container">
            <!-- El contenido del selector se carga desde la vista 'home.blade.php' -->
            @yield('selector')
        </div>
        <div class="meta-inicio-titulos">
    @if(isset($meta))
        @if($meta->heading_h1)
            <h1 class="main-title">{{ $meta->heading_h1 }}</h1>
        @endif

        @if($meta->heading_h2)
            <div class="title-secondary">
                <h2>{{ $meta->heading_h2 }}</h2>
            </div>
        @endif
    @endif
</div>

<div class="swiper swiper-zonas">
    <div class="swiper-wrapper">
        @foreach(['Zona Norte', 'Zona Centro', 'Zona Sur'] as $zona)
            @if(isset($ciudadesPorZona[$zona]) && $ciudadesPorZona[$zona]->isNotEmpty())
                <div class="swiper-slide">
                    <div class="zona-card glass">
                        <h3>{{ $zona }}</h3>
                        
                        <p class="texto-adicional">
                            @if($zona === 'Zona Norte')
                                {{ $meta->texto_zonas }}
                            @elseif($zona === 'Zona Centro')
                                {{ $meta->texto_zonas_centro }}
                            @elseif($zona === 'Zona Sur')
                                {{ $meta->texto_zonas_sur }}
                            @endif
                        </p>
                        
                        <hr class="divider" />
                        
                        <div class="ciudades-container">
                            @foreach($ciudadesPorZona[$zona]->sortBy('posicion') as $ciudad)
                                <div class="ciudad-item">
                                    <a href="/escorts-{{ strtolower($ciudad->url) }}">{{ $ciudad->nombre }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="swiper-pagination zonas-pagination"></div>
    <div class="swiper-button-prev zonas-prev"></div>
    <div class="swiper-button-next zonas-next"></div>
</div>
</div>
    <!-- Contenedor del selector centrado -->
    <main class="content" >
   
   

<!-- titulo tarjetas aqui-->
        <!-- Contenedor para las tarjetas -->
      

        <div class="cards-container">
            <div class="tarjetasflex">
        <h2 class="titulo-tarjetas">
    {!! preg_replace('/\s(\S+)$/', ' <span class="highlight">$1</span>', e($meta->titulo_tarjetas ?? 'Sin título definido')) !!}
</h2>
</div>
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
