<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Título de la pestaña -->
         <link rel="icon" href="{{ asset('images/icono.png') }}?v=2" type="image/png">    

         <title>
         {{ $meta->meta_title ?? 'OnlyEscorts' }}
</title>
    <!-- Icono de la pestaña (favicon) -->
    <!-- Meta tags dinámicos -->
    <meta name="description" content="{{ $metaDescription ?? ($meta->meta_description ?? '') }}">
    <meta name="keywords" content="{{ $metaKeywords ?? ($meta->meta_keywords ?? '') }}">
    <meta name="robots" content="{{ $metaRobots ?? ($meta->meta_robots ?? 'index,follow') }}">

    <!-- Canonical URL -->
    @if(isset($canonicalUrl) || (isset($meta) && $meta->canonical_url))
    <link rel="canonical" href="{{ $canonicalUrl ?? $meta->canonical_url }}" />
    @endif

    <!--Iconos-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.3.2/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        .heading-container {
    display: flex;
    flex-direction: column;
    gap: 1rem; /* Espacio entre h1 y h2 */
}

.texto_banner h1 {
    margin: 0; /* Reset margin */
}

.texto_banner h2 {
    margin: 0; /* Reset margin */
}

.thin {
    font-weight: 300;
}

.bold {
    font-weight: bold;
}

/* cARDS */
.inicio-card::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 150px;
    height: 150px;
    background-image: url('{{ isset($meta->marca_agua) ? Storage::url($meta->marca_agua) : asset('images/logo_XL-2.png') }}');
        background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.2;
    z-index: 2;
    pointer-events: none;
}

/* Contenedor para la marca de agua */
.watermark-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Estilo de la marca de agua */
.watermark {
    background-image: url('{{ isset($meta->marca_agua) ? Storage::url($meta->marca_agua) : asset('images/logo_XL-2.png') }}');
        background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    width: 100px;
    height: 100px;
    opacity: 0.9;
}
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-top">
            <div class="navbar-left">
                <button class="btn-publish">PUBLICATE</button>
            </div>

            <div class="navbar-right">
            <div class="location-dropdown">
                <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon">
                <select name="location" id="location">
                    <option value="" disabled>Seleccionar ciudad</option>
                    @foreach($ciudades as $ciudad)
                        <option value="{{ strtolower($ciudad->url) }}" 
                            {{ session('ciudad_actual') == $ciudad->nombre ? 'selected' : '' }}>
                            {{ ucfirst($ciudad->nombre) }}
                        </option>
                    @endforeach
                </select>
            </div>


                @if(Auth::check())
                @if(Auth::user()->rol === '1')
                <a href="{{ route('admin.profile') }}" class="btn-user">{{ Auth::user()->name }}</a>
                @elseif(Auth::user()->rol === '2')
                <a href="{{ route('admin.profile') }}" class="btn-user">{{ Auth::user()->name }}</a>
                @else
                <a href="{{ route('admin.profile') }}" class="btn-user">{{ Auth::user()->name }}</a>
                @endif
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
            <div class="navbar-left">
                <a href="/" class="logo">
                    <img src="{{ asset('images/logo_v2.png') }}" alt="Logo" class="logo1">
                </a>


                <button class="btn-filters">
                    <img src="{{ asset('images/filtro.svg') }}" alt="Filtros" class="icon-filter"> Filtro Avanzado
                </button>
            </div>

            <div class="navbar-right">
                <a href="/" class="nav-link">INICIO</a>

                <div class="dropdown">
    <button class="dropdown-button nav-link">CIUDADES</button>
    <div class="dropdown-content">
        @php
            $ciudadesPorZona = $ciudades->groupBy('zona');
            $ordenZonas = ['Zona Norte', 'Zona Centro', 'Zona Sur'];
        @endphp

        @foreach($ordenZonas as $zona)
            @if(isset($ciudadesPorZona[$zona]))
                <div class="dropdown-column">
                    <h3>{{ $zona }}</h3>
                    @foreach($ciudadesPorZona[$zona] as $ciudad)
                        <a href="/escorts-{{ $ciudad->url }}" 
                           class="ciudad-link">
                            {{ strtoupper($ciudad->nombre) }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

                <a href="/mis-favoritos/" class="nav-link">FAVORITOS</a>
                <a href="/blog/" class="nav-link">BLOG</a>
                <a href="/foro/" class="nav-link">FORO ESCORTS </a>
            </div>
        </div>
    </header>

    @php
    use App\Models\Servicio;
    use App\Models\Atributo;
    $servicios = Servicio::orderBy('posicion')->get();
    $atributos = Atributo::orderBy('posicion')->get();
@endphp
<!-- Modal -->
<div class="modal fade filtro-modal" id="filterModal" tabindex="-1">
    <div class="filtro-alert-container"></div>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="filters-container">
                 <!-- Ciudad selection -->
                 <div class="filter-ciudad">
                    <div class="filter-section">
                        <h6 class="range-title">Ciudad</h6>
                        <select id="ciudadSelect" class="form-select" required>
                            <option value="">Seleccionar ciudad</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->url }}"
                                    {{ isset($ciudadSeleccionada) && $ciudadSeleccionada->url == $ciudad->url ? 'selected' : '' }}>
                                    {{ $ciudad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sector selection -->
                <div class="filtro-nac" id="barrioContainer" style="display: none;">
                    <h6 class="range-title">Sector</h6>
                    <select id="barrioSelect" class="form-select">
                        <option value="">Seleccionar sector</option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector->url }}" 
                                {{ isset($sectorSeleccionado) && $sectorSeleccionado == $sector->url ? 'selected' : '' }}>
                                {{ $sector->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nationality selection -->
                <div class="filtro-nac">
    <div class="filter-section">
        <h6 class="range-title">Nacionalidad</h6>
        <select name="nacionalidad" id="nacionalidadSelect" class="form-select">
            <option value="">Todas las nacionalidades</option>
            @foreach($nacionalidades as $nacionalidad)
    @if(is_object($nacionalidad) && isset($nacionalidad->url))
        <option value="{{ $nacionalidad->url }}">{{ $nacionalidad->nombre }}</option>
    @else
        <option value="">Dato inválido</option>
    @endif
@endforeach
        </select>
    </div>
</div>
</div>

            <form id="filterForm">
                <div class="modal-body">
                    <!-- Rango de edad -->
                    <div class="filter-section">
                        <h6 class="range-title">Edad</h6>
                        <div class="range-container">
                            <div id="edadRange"></div>
                            <div class="range-values">
                                <span>18 años</span>
                                <span>50 años</span>
                            </div>
                        </div>
                        <input type="hidden" name="edadMin" id="edadMin">
                        <input type="hidden" name="edadMax" id="edadMax">
                    </div>

                    <!-- Rango de precio -->
                    <div class="filter-section">
                        <h6 class="range-title">Precio</h6>
                        <div class="price-categories">
                            <div class="price-category" data-min="0" data-max="300000" data-categorias="Under" style="visibility: hidden; pointer-events: none;">
                                <span class="category-name">Under</span>
                            </div>
                            <div class="price-category" data-min="0" data-max="300000" data-categorias="Under">
                                <span class="category-name">Under</span>
                            </div>
                            <div class="price-category" data-min="0" data-max="300000" data-categorias="Under" style="visibility: hidden; pointer-events: none;">
                                <span class="category-name">Under</span>
                            </div>
                        </div>
                        <div class="price-categories">
                            <div class="price-category" data-min="0" data-max="70000" data-categorias="premium">
                                <span class="category-name">Premium</span>
                            </div>
                            <div class="price-category" data-min="70000" data-max="130000" data-categorias="vip">
                                <span class="category-name">VIP</span>
                            </div>
                            <div class="price-category" data-min="130000" data-max="250000" data-categorias="de_lujo">
                                <span class="category-name">De Lujo</span>
                            </div>
                        </div>
                        <input type="hidden" name="categorias" id="categoriasFilter">
                        <div class="range-container">
                            <div id="precioRange"></div>
                            <div class="range-values">
                                <span>$0</span>
                                <span>$300.000</span>
                            </div>
                        </div>
                        <input type="hidden" name="precioMin" id="precioMin">
                        <input type="hidden" name="precioMax" id="precioMax">
                    </div>

                    <!-- Nuevos checkboxes -->
                    <div class="extra-filters">
                        <div class="filter-section" style="display: flex; gap: 20px;">
                            <div>
                                <h6 class="range-title">Disponibilidad</h6>
                                <div id="disponibleCheck" class="review-container">
                                    <span class="review-text">Disponible</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="range-title">Reseñas</h6>
                                <div id="resenaCheck" class="review-container">
                                    <span class="review-text">Tiene una reseña</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="filter-section1">
                        <h6 class="range-title1">Servicios</h6>
                        <div id="serviciosContainer" class="servicios-grid">
                        </div>
                        <div class="review-container" id="showMoreServices">
                            <span class="review-text">Mostrar más</span>
                        </div>
                    </div>

                    <!-- Attributes -->
                    <div class="filter-section1">
                        <h6 class="range-title1">Atributos</h6>
                        <div id="atributosContainer" class="servicios-grid">
                        </div>
                        <div class="review-container" id="showMoreAttributes">
                            <span class="review-text">Mostrar más</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="resetFilters">Resetear</button>
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="custom-footer-bottom">
            <div class="custom-container">
                <div class="custom-footer-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo_XL-2.png') }}" alt="OnlyEscorts Logo" class="custom-logo-footer">
                    </a>
                    <a href="/rta/">
                        <img src="{{ asset('images/RTA-removebg-preview1.png') }}" alt="RTA" class="custom-logo-footer1">
                    </a>
                </div>
                <div class="custom-footer-middle">
                    <div class="custom-links">
                        <div class="custom-link-row">
                            <a href="/foro/">FORO ESCORTS</a>
                            <a href="/blog/">BLOG</a>
                        </div>
                        <div class="custom-link-row">
                            <a href="{{ route('contacto') }}">CONTACTO</a>
                            <a href="#">POLÍTICA DE PRIVACIDAD</a>
                        </div>
                        <div class="custom-link-row">
                            <a href="/register/">REGISTRO</a>
                            <a href="/publicate/">PUBLICATE</a>
                        </div>
                    </div>
                </div>

                <div class="custom-social-links">
                    <a href="#"><img src="{{ asset('images/facebook.svg') }}" alt="Facebook" class="custom-social-icon"></a>
                    <a href="#"><img src="{{ asset('images/instagram.svg') }}" alt="Instagram" class="custom-social-icon"></a>
                    <a href="#"><img src="{{ asset('images/x.svg') }}" alt="X" class="custom-social-icon"></a>
                    <a href="#"><img src="{{ asset('images/YouTube1.svg') }}" alt="Youtube" class="custom-social-icon"></a>
                </div>
            </div>
        </div>

        <div class="footer-info">
            <p>© 2024 Only Escorts | Todos los derechos reservados</p>
        </div>
    </footer>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCE-YA3ZXTQ0uMGWjENmAG274nUWOM7-Kc"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const escortperfilSwiper = new Swiper('.escortperfil-swiper', {
        slidesPerView: 'auto', // Cambiado para respetar el ancho definido
        spaceBetween: 0, // Sin espacio entre slides
        loop: true,
        centeredSlides: false, // Quitamos el centrado
        effect: "slide",
        navigation: {
            nextEl: '.escortperfil-swiper-button-next',
            prevEl: '.escortperfil-swiper-button-prev',
        },
        pagination: {
            el: '.escortperfil-swiper-pagination',
            clickable: true
        },
    });
});

        document.getElementById('location').addEventListener('change', function() {
            const ciudadNombre = this.value;

            if (!ciudadNombre) {
                alert('Por favor selecciona una ciudad.');
                return;
            }

            // Redirigir a la ruta dinámica
            window.location.href = `/escorts-${ciudadNombre}`;
        });

        document.getElementById('categorias').addEventListener('change', function() {
            const categoria = this.value.toLowerCase(); // Convertir a minúsculas
            let currentUrl = window.location.pathname;

            if (!categoria) {
                alert('Por favor selecciona una categoría.');
                return;
            }

            // Eliminar cualquier categoría existente en la URL
            currentUrl = currentUrl.replace(/\/(deluxe|vip|premium|masajes)$/i, ''); // Ignorar mayúsculas y eliminar categoría existente

            // Asegurarse de que la URL no termine con '/'
            if (currentUrl.endsWith('/')) {
                currentUrl = currentUrl.slice(0, -1);
            }

            // Construir la nueva URL con la categoría en minúsculas
            window.location.href = `${currentUrl}/${categoria}`;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.inicio-card');
            if (cards.length < 12) {
                const container = document.querySelector('.inicio-container');
                if (container) {
                }
            }
        });
        </script>
            <script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales dentro del scope
    let currentHistorias = [];
    let currentIndex = 0;
    let timer;
    let currentUserId = null;
    let currentVideo = null;
    let touchstartX = 0;
    let touchendX = 0;

    // Función para detener todos los videos
    function stopAllVideos() {
        const videos = document.querySelectorAll('#historiaModal video');
        videos.forEach(video => {
            video.pause();
            video.currentTime = 0;
        });
        currentVideo = null;
    }

    // Event Listeners principales
    function initializeEventListeners() {
        // Event listeners para las historias
        document.querySelectorAll('.historia-item').forEach(item => {
            item.addEventListener('click', function() {
                const estados = JSON.parse(this.dataset.estados);
                const usuarioId = this.dataset.usuarioId;
                mostrarHistorias(estados, usuarioId);
            });
        });

        // Configurar los event listeners para controles táctiles
        const historiaModal = document.getElementById('historiaModal');
        if (historiaModal) {
            historiaModal.addEventListener('touchstart', e => {
                touchstartX = e.changedTouches[0].screenX;
            });

            historiaModal.addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                handleGesture();
            });

            // Configurar el cierre del modal
            historiaModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    stopAllVideos();
                    this.style.display = 'none';
                    clearTimeout(timer);
                }
            });
        }

        // Configurar el botón de cerrar
        const cerrarModal = document.querySelector('.cerrar-modal');
        if (cerrarModal) {
            cerrarModal.addEventListener('click', function() {
                const modal = document.getElementById('historiaModal');
                if (modal) {
                    stopAllVideos();
                    modal.style.display = 'none';
                    clearTimeout(timer);
                }
            });
        }

        // Configurar controles de teclado
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('historiaModal');
            if (modal && modal.style.display === 'flex') {
                if (e.key === 'ArrowRight') nextHistoria();
                else if (e.key === 'ArrowLeft') previousHistoria();
                else if (e.key === 'Escape') {
                    stopAllVideos();
                    modal.style.display = 'none';
                    clearTimeout(timer);
                }
            }
        });

        // Botones de navegación
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', previousHistoria);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', nextHistoria);
        }
    }

    function mostrarHistorias(estado, userId) {
        currentHistorias = Array.isArray(estado) ? estado : [estado];
        currentIndex = 0;
        currentUserId = userId;

        const modal = document.getElementById('historiaModal');
        if (!modal) {
            console.error('Modal no encontrado');
            return;
        }
        modal.style.display = 'flex';

        // Actualizar la información del usuario en el modal
        const profileImage = document.getElementById('modal-profile-image');
        const usuarioNombre = document.getElementById('modal-usuario-nombre');

        if (!currentHistorias[0] || !currentHistorias[0].usuarios_publicate_id) {
            console.error('ID de usuario no válido');
            return;
        }

        // Obtener la información del usuario
        fetch(`/usuario/${currentHistorias[0].usuarios_publicate_id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(usuario => {
                console.log('Datos del usuario recibidos:', usuario);

                // Manejar la foto del usuario
                if (profileImage) {
                    if (usuario.foto) {
                        const fotoUrl = `/storage/${usuario.foto}`;
                        profileImage.src = fotoUrl;
                        profileImage.onerror = () => {
                            console.log('Error al cargar la imagen, usando imagen por defecto');
                            profileImage.src = '/img/default-avatar.png';
                        };
                    } else {
                        profileImage.src = '/img/default-avatar.png';
                    }
                }

                // Actualizar nombre y hacerlo clickeable
                if (usuarioNombre) {
                    usuarioNombre.textContent = usuario.fantasia || usuario.name || 'Usuario';
                    usuarioNombre.href = `/escorts/${currentHistorias[0].usuarios_publicate_id}`;
                    usuarioNombre.style.color = 'white';
                    usuarioNombre.style.textDecoration = 'none';
                    usuarioNombre.style.fontWeight = 'bold';

                    usuarioNombre.onmouseover = function() {
                        this.style.textDecoration = 'underline';
                    };
                    usuarioNombre.onmouseout = function() {
                        this.style.textDecoration = 'none';
                    };
                }
            })
            .catch(error => {
                console.error('Error al obtener datos del usuario:', error);
                if (profileImage) profileImage.src = '/img/default-avatar.png';
                if (usuarioNombre) {
                    usuarioNombre.textContent = 'Usuario';
                    usuarioNombre.href = '#';
                }
            });

        // Crear indicadores
        const indicatorsContainer = document.querySelector('.historia-indicators');
        if (indicatorsContainer) {
            indicatorsContainer.innerHTML = currentHistorias.map((_, i) =>
                `<div class="indicator ${i === 0 ? 'active' : ''}" data-index="${i}"></div>`
            ).join('');
        }

        mostrarHistoriaActual();

        // Marcar como visto el estado actual
        if (currentHistorias[currentIndex] && currentHistorias[currentIndex].id) {
            marcarComoVisto(currentHistorias[currentIndex].id);
        }
    }

    function mostrarHistoriaActual() {
        clearTimeout(timer);
        const contenido = document.getElementById('historia-contenido');
        const historiaTiempo = document.getElementById('modal-historia-tiempo');

        if (!contenido || !currentHistorias[currentIndex]) {
            console.error('Contenido no encontrado o historia no válida');
            return;
        }

        const estado = currentHistorias[currentIndex];

        // Actualizar el tiempo de la historia actual
        if (historiaTiempo && typeof moment !== 'undefined') {
            console.log('Actualizando tiempo para historia:', {
                index: currentIndex,
                created_at: estado.created_at
            });
            historiaTiempo.textContent = `hace ${moment(estado.created_at).fromNow(true)}`;
        } else if (historiaTiempo) {
            historiaTiempo.textContent = 'hace un momento';
        }

        try {
            const mediaFiles = typeof estado.fotos === 'string' ?
                JSON.parse(estado.fotos) : estado.fotos;

            // Actualizar indicadores
            document.querySelectorAll('.indicator').forEach((ind, i) => {
                if (i < currentIndex) ind.classList.add('viewed');
                else if (i === currentIndex) {
                    ind.classList.add('active');
                    ind.classList.remove('viewed');
                } else {
                    ind.classList.remove('active', 'viewed');
                }
            });

            contenido.innerHTML = '';

            if (mediaFiles.imagenes && mediaFiles.imagenes.length > 0) {
                mostrarImagen(contenido, mediaFiles.imagenes[0], estado);
            } else if (mediaFiles.videos && mediaFiles.videos.length > 0) {
                mostrarVideo(contenido, mediaFiles.videos[0], estado);
            }
        } catch (error) {
            console.error('Error al procesar los media files:', error);
            contenido.innerHTML = '<p style="color: white;">Error al cargar el contenido</p>';
        }
    }

    function mostrarImagen(contenido, imagePath, estado) {
        const img = document.createElement('img');
        imagePath = imagePath.replace(/\\/g, '/');
        img.src = `/storage/${imagePath}`;
        img.className = 'historia-media';
        img.style.maxWidth = '100%';
        img.style.maxHeight = '80vh';
        img.style.objectFit = 'contain';
        contenido.appendChild(img);

        img.onload = () => {
            timer = setTimeout(() => {
                if (estado.id) {
                    marcarComoVisto(estado.id);
                }
                nextHistoria();
            }, 5000);
        };

        img.onerror = () => {
            console.error('Error al cargar la imagen:', imagePath);
            contenido.innerHTML = '<p style="color: white;">Error al cargar la imagen</p>';
        };
    }

    function mostrarVideo(contenido, videoPath, estado) {
        // Detener video anterior si existe
        if (currentVideo) {
            currentVideo.pause();
            currentVideo.currentTime = 0;
        }

        const video = document.createElement('video');
        video.className = 'historia-media';
        video.controls = true;
        video.autoplay = true;
        video.style.maxWidth = '100%';
        video.style.maxHeight = '80vh';
        video.style.objectFit = 'contain';

        videoPath = videoPath.replace(/\\/g, '/');
        const source = document.createElement('source');
        source.src = `/storage/${videoPath}`;
        source.type = `video/${videoPath.split('.').pop()}`;
        video.appendChild(source);
        contenido.appendChild(video);

        // Asignar como video actual
        currentVideo = video;

        video.onended = () => {
            if (estado.id) {
                marcarComoVisto(estado.id);
            }
            nextHistoria();
        };

        video.onerror = () => {
            console.error('Error al cargar el video:', videoPath);
            contenido.innerHTML = '<p style="color: white;">Error al cargar el video</p>';
        };
    }

    function nextHistoria() {
        if (currentVideo) {
            currentVideo.pause();
            currentVideo.currentTime = 0;
        }
        if (currentIndex < currentHistorias.length - 1) {
            currentIndex++;
            mostrarHistoriaActual();
            if (currentHistorias[currentIndex].id) {
                marcarComoVisto(currentHistorias[currentIndex].id);
            }
        } else {
            cerrarHistoria();
        }
    }

    function previousHistoria() {
        if (currentVideo) {
            currentVideo.pause();
            currentVideo.currentTime = 0;
        }
        if (currentIndex > 0) {
            currentIndex--;
            mostrarHistoriaActual();
        }
    }

    function cerrarHistoria() {
        const modal = document.getElementById('historiaModal');
        const final = document.getElementById('historiaFinal');

        stopAllVideos();

        if (modal) modal.style.display = 'none';
        if (final) {
            final.style.display = 'flex';
            setTimeout(() => {
                final.style.display = 'none';
            }, 3000);
        }

        // Actualizar el estilo del círculo del usuario actual
        if (currentHistorias[0] && currentHistorias[0].usuarios_publicate_id) {
            actualizarEstiloHistoriaCompleta(currentHistorias[0].usuarios_publicate_id);
        }
    }

    function actualizarEstiloHistoriaCompleta(usuarioPublicateId) {
        document.querySelectorAll('.historia-item').forEach(item => {
            if (item.dataset.usuarioId == usuarioPublicateId) {
                const circulo = item.querySelector('.historia-circle');
                if (circulo) {
                    circulo.classList.add('historia-vista');
                    const todasVistas = currentHistorias.every(historia => historia.visto);
                    if (todasVistas) {
                        circulo.style.background = '#808080';
                    }
                }
            }
        });
    }

    function marcarComoVisto(estadoId) {
        fetch('/estados/marcar-visto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    estado_id: estadoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const historia = currentHistorias.find(h => h.id === estadoId);
                    if (historia) {
                        historia.visto = true;
                    }
                } else {
                    console.error('Error al marcar como visto:', data.error);
                }
            })
            .catch(error => {
                console.error('Error al marcar como visto:', error);
            });
    }

    // Función para manejar gestos táctiles
    function handleGesture() {
        if (touchendX < touchstartX) nextHistoria();
        if (touchendX > touchstartX) previousHistoria();
    }

    // Inicializar todos los event listeners
    initializeEventListeners();
});
    </script>
   @if(Route::currentRouteName() !== 'inicio' && Route::currentRouteName() !== 'favoritos.show')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener la ubicación del modelo
    const ubicacionData = {
        direccion: "{{ $usuarioPublicate->location->direccion ?? $usuarioPublicate->ubicacion }}",
        lat: {{ $usuarioPublicate->location->latitud ?? -33.4489 }},
        lng: {{ $usuarioPublicate->location->longitud ?? -70.6693 }}
    };

    function initMap() {
        // Crear el mapa con las coordenadas
        const map = new google.maps.Map(document.getElementById('escort-map'), {
            zoom: 15,
            center: { lat: ubicacionData.lat, lng: ubicacionData.lng },
            mapTypeControl: false,
            streetViewControl: false,
            styles: [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ]
        });

        // Crear el marcador
        const marker = new google.maps.Marker({
            map: map,
            position: { lat: ubicacionData.lat, lng: ubicacionData.lng }
        });

        // Agregar círculo para área aproximada
        const circle = new google.maps.Circle({
            map: map,
            center: { lat: ubicacionData.lat, lng: ubicacionData.lng },
            radius: 500,  // Radio en metros
            fillColor: '#FF0000',
            fillOpacity: 0.1,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 1
        });

        // Si no tenemos coordenadas guardadas, geocodificar la dirección
        if (ubicacionData.lat === -33.4489 && ubicacionData.lng === -70.6693) {
            const geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({ 
                address: ubicacionData.direccion + ', Chile' 
            }, function(results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    
                    // Actualizar el mapa
                    map.setCenter(location);
                    marker.setPosition(location);
                    circle.setCenter(location);
                }
            });
        }
    }

    // Cargar el mapa
    initMap();
});
</script>
@endif

    <script>
// Primero definimos los estilos de la animación
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .login-alert {
        animation: slideIn 0.3s ease forwards;
    }

    .login-alert.hiding {
        animation: slideOut 0.3s ease forwards;
    }
`;
document.head.appendChild(styleSheet);

function showLoginAlert() {
    // Eliminar alerta existente si hay alguna
    const existingAlert = document.querySelector('.login-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Crear la alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = 'login-alert';
    alertDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.7);
    `;
    
    // Contenedor interno para el mensaje
    const messageContainer = document.createElement('div');
    messageContainer.style.cssText = `
        background: rgb(42, 42, 42);
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    `;
    
    messageContainer.innerHTML = `
        <span style="
            color: white; 
            font-size: 18px; 
            text-align: center;
            display: block;
            width: 100%;
            line-height: 1.5;
            font-weight: 500;
        ">
            Debes tener una cuenta para tener favoritos
        </span>
        <a href="/login" 
           style="
               background: #e00037; 
               color: white; 
               padding: 14px 28px; 
               text-decoration: none; 
               text-transform: uppercase; 
               font-weight: bold; 
               font-size: 15px;
               border-radius: 4px;
               transition: all 0.3s ease;
               display: inline-block;
               margin: 0 auto;
               border: none;
               cursor: pointer;
           ">
            ACCEDER
        </a>
    `;

    alertDiv.appendChild(messageContainer);
    document.body.appendChild(alertDiv);

    // Cerrar al hacer click en cualquier parte
    alertDiv.addEventListener('click', (e) => {
        if (e.target === alertDiv) {
            alertDiv.classList.add('hiding');
            setTimeout(() => {
                alertDiv.remove();
            }, 300);
        }
    });

    // Remover automáticamente después de 2.7 segundos
    setTimeout(() => {
        alertDiv.classList.add('hiding');
        setTimeout(() => {
            alertDiv.remove();
        }, 300); // Duración de la animación
    }, 2700);
}

// Agregar event listeners a las imágenes bloqueadas
document.addEventListener('DOMContentLoaded', function() {
    const blockedImages = document.querySelectorAll('.blocked-image');
    blockedImages.forEach((img) => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            showLoginAlert();
        });
    });
});

// Exponer la función globalmente
window.showLoginAlert = showLoginAlert;



// Función para manejar el estado del botón
function setButtonState(button, isFavorited) {
    const textSpan = button.querySelector('span');
    const heartIcon = button.querySelector('i');
    
    if (isFavorited) {
        button.classList.add('active');
        textSpan.innerHTML = 'ELIMINAR DE<br>FAVORITOS';
        heartIcon.classList.remove('far');
        heartIcon.classList.add('fas');
        heartIcon.style.color = '#e00037';
    } else {
        button.classList.remove('active');
        textSpan.innerHTML = 'FAVORITOS';
        heartIcon.classList.remove('fas');
        heartIcon.classList.add('far');
        heartIcon.style.color = 'white';
    }
}

// Inicializar el comportamiento del botón
document.querySelectorAll('.favorite-button').forEach(button => {
    const isInitiallyFavorited = button.classList.contains('active');
    setButtonState(button, isInitiallyFavorited);

    button.addEventListener('click', function(e) {
        const id = this.dataset.id;
        const currentState = this.classList.contains('active');
        
        fetch(`/favorite/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 401) {
                // Usuario no autenticado
                showLoginAlert();
                throw new Error('User not authenticated');
            }
            return response.json();
        })
        .then(data => {
            setButtonState(this, data.status === 'added');
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.message !== 'User not authenticated') {
                setButtonState(this, currentState);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiperConfig = {
        slidesPerView: 2,
        spaceBetween: 5,
        grabCursor: true,
        loop: true,
        speed: 600,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        }
    };

    // Inicializar un único Swiper para ambas secciones
    const combinedSwiper = new Swiper('.sections-container .swiper-container2', swiperConfig);
});
</script>  
<script>
function openShareModal() {
    const modal = document.getElementById('shareModal');
    modal.style.display = 'block';
    document.getElementById('shareUrl').value = window.location.href;
    // Agregar clase active después de un pequeño delay para activar la animación
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    modal.classList.remove('active');
    // Esperar a que termine la animación antes de ocultar el modal
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function copyUrl() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    document.execCommand('copy');
    
    // Cambiar el texto del botón temporalmente para dar feedback
    const copyButton = document.querySelector('.copy-button');
    const originalText = copyButton.textContent;
    copyButton.textContent = '¡COPIADO!';
    setTimeout(() => {
        copyButton.textContent = originalText;
    }, 2000);
}

function shareOnFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareOnX() {
    window.open(`https://x.com/intent/tweet?url=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareOnTelegram() {
    window.open(`https://t.me/share/url?url=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareOnWhatsapp() {
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(window.location.href)}`, '_blank');
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        closeShareModal();
    }
}

function openEscortModal(imageSrc) {
    const modal = document.getElementById('escortperfilImageModal');
    const modalImage = document.getElementById('escortperfilModalImage');
    const backdrop = document.getElementById('escortperfilModalBackdrop');
    
    modalImage.src = imageSrc;
    
    modalImage.onload = function() {
        const windowHeight = window.innerHeight - 40; // Reducimos el margen a 20px por lado
        const windowWidth = window.innerWidth - 40;  // Reducimos el margen a 20px por lado
        
        const imgWidth = this.naturalWidth;
        const imgHeight = this.naturalHeight;
        
        let newWidth = imgWidth;
        let newHeight = imgHeight;
        
        // Calculamos las dimensiones para que ocupen más espacio
        if (imgWidth > windowWidth) {
            newWidth = windowWidth;
            newHeight = (imgHeight * windowWidth) / imgWidth;
        }
        
        if (newHeight > windowHeight) {
            newHeight = windowHeight;
            newWidth = (imgWidth * windowHeight) / imgHeight;
        }
        
        // Aumentamos el tamaño un 20% si hay espacio
        if (newWidth * 1.2 <= windowWidth && newHeight * 1.2 <= windowHeight) {
            newWidth *= 1.2;
            newHeight *= 1.2;
        }
        
        modal.style.width = `${newWidth}px`;
        modal.style.height = `${newHeight}px`;
        
        modal.style.display = 'block';
        backdrop.style.display = 'block';
    };
}

function closeEscortModal() {
    document.getElementById('escortperfilImageModal').style.display = 'none';
    document.getElementById('escortperfilModalBackdrop').style.display = 'none';
}

// Cerrar el modal con la tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEscortModal();
    }
});


</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const swiperContainers = document.querySelectorAll('.swiper-container2');
    
    swiperContainers.forEach(container => {
        const wrapper = container.querySelector('.swiper-wrapper');
        const pagination = container.querySelector('.swiper-pagination2');
        const slides = wrapper.querySelectorAll('.swiper-slide2');
        
        // Crear puntos de paginación
        slides.forEach((_, index) => {
            const bullet = document.createElement('span');
            bullet.className = 'swiper-pagination-bullet' + (index === 0 ? ' swiper-pagination-bullet-active' : '');
            pagination.appendChild(bullet);
        });
        
        // Actualizar paginación al hacer scroll
        wrapper.addEventListener('scroll', () => {
            const scrollLeft = wrapper.scrollLeft;
            const slideWidth = slides[0].offsetWidth;
            const activeIndex = Math.round(scrollLeft / slideWidth);
            
            pagination.querySelectorAll('.swiper-pagination-bullet').forEach((bullet, index) => {
                bullet.classList.toggle('swiper-pagination-bullet-active', index === activeIndex);
            });
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Variable global para control de filtros
    let hasAddedUrlFilter = false;

    // Elementos del DOM
    const modal = new bootstrap.Modal(document.getElementById('filterModal'));
    const form = document.getElementById('filterForm');
    const ciudadSelect = document.getElementById('ciudadSelect');
    const barrioContainer = document.getElementById('barrioContainer');
    const barrioSelect = document.getElementById('barrioSelect');
    const nacionalidadSelect = document.getElementById('nacionalidadSelect');
    const disponibleCheck = document.getElementById('disponibleCheck');
    const resenaCheck = document.getElementById('resenaCheck');

    // Función para manejar el cierre del modal
    const handleModalClose = () => {
        form.reset();
        window.history.replaceState({}, document.title, window.location.href);
    };

    modal._element.addEventListener('hidden.bs.modal', handleModalClose);
    document.querySelector('.btn-close')?.addEventListener('click', () => modal.hide());

    // Funciones de normalización
    const normalizeText = (text) => {
        if (!text) return '';
        return text.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "") // Remover acentos
            .replace(/\s+/g, '-') // Espacios a guiones
            .replace(/[^a-z0-9-]/g, '-') // Caracteres especiales a guiones
            .replace(/-+/g, '-') // Evitar múltiples guiones seguidos
            .replace(/^-|-$/g, ''); // Remover guiones al inicio y final
    };

    const normalizeString = (text) => {
        if (!text) return '';
        return text.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    };

    // Función para mostrar alertas
    function showFiltroAlert(message) {
        const alertContainer = document.querySelector('.filtro-alert-container');
        if (!alertContainer) return;

        const alertElement = document.createElement('div');
        alertElement.className = 'filtro-custom-alert';
        alertElement.innerHTML = `
            <span class="filtro-alert-message">${message}</span>
            <button class="filtro-alert-close" onclick="this.parentElement.remove()">&times;</button>
        `;
        alertContainer.appendChild(alertElement);
        setTimeout(() => {
            if (alertElement.parentElement) {
                alertElement.classList.add('filtro-fade-out');
                setTimeout(() => alertElement.remove(), 300);
            }
        }, 5000);
    }

    // Función para contar filtros actuales
    const countUrlFilters = () => {
        let filterCount = 0;
        if (nacionalidadSelect.value) filterCount++;
        const [edadMin, edadMax] = edadRange.noUiSlider.get().map(Number);
        if (edadMin !== 18 || edadMax !== 50) filterCount++;
        const selectedCategory = document.querySelector('.price-category.active');
        if (selectedCategory || (precioRange.noUiSlider.get()[0] !== 0 || precioRange.noUiSlider.get()[1] !== 300000)) {
            filterCount++;
        }
        if (disponibleCheck.classList.contains('selected')) filterCount++;
        const checkedAtributos = document.querySelectorAll('input[name="atributos[]"]:checked');
        if (checkedAtributos.length > 0) filterCount++;
        const checkedServicios = document.querySelectorAll('input[name="servicios[]"]:checked');
        if (checkedServicios.length > 0) filterCount++;
        if (resenaCheck.classList.contains('selected')) filterCount++;
        return filterCount;
    };

    // Función para determinar si un filtro debe ir en la URL
    const shouldAddToUrl = () => {
        if (hasAddedUrlFilter) return false;
        const isSantiago = ciudadSelect.value.toLowerCase() === 'santiago';
        if (isSantiago && barrioSelect.value) return true;
        return true;
    };

    // Inicialización de rangos
    const edadRange = document.getElementById('edadRange');
    noUiSlider.create(edadRange, {
        start: [18, 50],
        connect: true,
        step: 1,
        range: { 'min': 18, 'max': 50 }
    });

    const precioRange = document.getElementById('precioRange');
    noUiSlider.create(precioRange, {
        start: [0, 300000],
        connect: true,
        step: 1000,
        range: { 'min': 0, 'max': 300000 }
    });

    // Configuración de tooltips y actualizaciones de rangos
    const setupRangeTooltips = (range) => {
        const handles = range.querySelectorAll('.noUi-handle');
        handles.forEach(handle => {
            const tooltip = document.createElement('div');
            tooltip.className = 'slider-tooltip';
            handle.appendChild(tooltip);
        });
    };

    const updateRangeValues = (range, values, prefix = '', suffix = '') => {
        const [min, max] = values.map(x => Math.round(Number(x)));
        const tooltips = range.querySelectorAll('.slider-tooltip');
        const minElement = range.parentElement.querySelector('.range-values span:first-child');
        const maxElement = range.parentElement.querySelector('.range-values span:last-child');
        
        if (tooltips[0]) tooltips[0].textContent = `${prefix}${min.toLocaleString()}${suffix}`;
        if (tooltips[1]) tooltips[1].textContent = `${prefix}${max.toLocaleString()}${suffix}`;
        if (minElement) minElement.textContent = `${prefix}${min.toLocaleString()}${suffix}`;
        if (maxElement) maxElement.textContent = `${prefix}${max.toLocaleString()}${suffix}`;
        
        return [min, max];
    };

    setupRangeTooltips(edadRange);
    setupRangeTooltips(precioRange);

    edadRange.noUiSlider.on('update', (values) => {
        const [min, max] = updateRangeValues(edadRange, values, '', ' años');
        document.getElementById('edadMin').value = min;
        document.getElementById('edadMax').value = max;
    });

    precioRange.noUiSlider.on('update', (values) => {
        const [min, max] = updateRangeValues(precioRange, values, '$');
        document.getElementById('precioMin').value = min;
        document.getElementById('precioMax').value = max;
    });

    // Gestión de categorías de precio
    const handlePriceCategories = () => {
        const priceCategories = document.querySelectorAll('.price-category');
        const isSantiago = ciudadSelect.value.toLowerCase() === 'santiago';
        
        priceCategories.forEach(category => {
            if (!category.style.visibility) { // Solo modificar categorías visibles
                category.style.display = isSantiago ? 'block' : 'none';
            }
        });

        if (!isSantiago) {
            precioRange.noUiSlider.set([0, 300000]);
            document.getElementById('categoriaFilter').value = '';
            priceCategories.forEach(category => category.classList.remove('active'));
        }
    };

    ciudadSelect.addEventListener('change', handlePriceCategories);

    // Event listeners para categorías de precio
    document.querySelectorAll('.price-category').forEach(category => {
        if (!category.style.visibility) { // Solo agregar listeners a categorías visibles
            category.addEventListener('click', () => {
                const min = parseInt(category.dataset.min);
                const max = parseInt(category.dataset.max);
                const categoriaValor = category.dataset.categoria;

                if (category.classList.contains('active')) {
                    category.classList.remove('active');
                    precioRange.noUiSlider.set([0, 300000]);
                    document.getElementById('categoriaFilter').value = '';
                } else {
                    document.querySelectorAll('.price-category').forEach(cat => cat.classList.remove('active'));
                    category.classList.add('active');
                    precioRange.noUiSlider.set([min, max]);
                    document.getElementById('categoriaFilter').value = categoriaValor;
                }
            });
        }
    });

    // Función para crear checkboxes
    const createCheckboxes = (items, container, name) => {
    console.log(`Creando checkboxes para ${name}:`, items);
    items.forEach((item, index) => {
        const label = document.createElement('label');
        label.className = 'checkbox-label';
        if (index >= 8) label.style.display = 'none';
        label.innerHTML = `
            <input type="checkbox" name="${name}[]" value="${item.url}"> <!-- Usar item.url -->
            <span class="checkbox-text">${item.nombre || item}</span>
        `;
        container.appendChild(label);
    });
};

fetch('/get-filter-data')
    .then(response => {
        if (!response.ok) throw new Error(`Error en la solicitud: ${response.statusText}`);
        return response.json();
    })
    .then(data => {
        // Verificar y manejar los datos de nacionalidades
        if (data.nacionalidades) {
    nacionalidadSelect.innerHTML = '<option value="">Seleccionar nacionalidad</option>';
    data.nacionalidades.forEach(nacionalidad => {
        const option = document.createElement('option');
        option.value = nacionalidad.url;  // Asegurarte de que aquí se asigna el valor correcto
        option.textContent = nacionalidad.nombre;
        nacionalidadSelect.appendChild(option);
    });

        } else {
            console.warn('No se encontraron datos de nacionalidades.');
        }

        // Crear checkboxes para servicios y atributos
        createCheckboxes(data.servicios, document.getElementById('serviciosContainer'), 'servicios');
        createCheckboxes(data.atributos, document.getElementById('atributosContainer'), 'atributos');
    })
    .catch(error => {
        console.error('Error al obtener los datos:', error);
    });



    // Gestión de barrios
    const toggleBarrioContainer = () => {
        const selectedCity = ciudadSelect.options[ciudadSelect.selectedIndex]?.text;
        const isSantiago = selectedCity?.toLowerCase().includes('santiago');
        
        barrioContainer.style.display = isSantiago ? 'block' : 'none';
        
        if (isSantiago && barrioSelect.options.length <= 1 && window.barriosSantiago?.length) {
            barrioSelect.innerHTML = '<option value="">Seleccionar barrio</option>';
            window.barriosSantiago.forEach(barrio => {
                const option = document.createElement('option');
                option.value = barrio;
                option.textContent = barrio;
                barrioSelect.appendChild(option);
            });
        } else if (!isSantiago) {
            barrioSelect.value = '';
        }
    };

    ciudadSelect.addEventListener('change', toggleBarrioContainer);
    toggleBarrioContainer();

    // Event listeners para botones
    resenaCheck.addEventListener('click', function() {
        this.classList.toggle('selected');
    });

    disponibleCheck.addEventListener('click', function() {
        this.classList.toggle('selected');
    });

    // Manejo del envío del formulario
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        hasAddedUrlFilter = false;

        if (!ciudadSelect.value) {
            showFiltroAlert('Por favor seleccione una ciudad');
            return;
        }

        let url = `/escorts-${ciudadSelect.value}`;
        const params = new URLSearchParams();
        const isSantiago = ciudadSelect.options[ciudadSelect.selectedIndex].text.toLowerCase().includes('santiago');

        // Agregar sector/comuna para Santiago
        if (isSantiago && barrioSelect.value) {
            const normalizedBarrio = normalizeText(barrioSelect.value);
            url += `/${normalizedBarrio}`;
        }

        // Procesar nacionalidad
        if (nacionalidadSelect.value) {
    console.log('Valor del select:', nacionalidadSelect.value);
    if (shouldAddToUrl()) {
        url += `/${nacionalidadSelect.value}`;
        console.log('URL actualizada:', url);
        hasAddedUrlFilter = true;
    } else {
        params.append('n', nacionalidadSelect.value);
        console.log('Parámetros agregados:', params.toString());
    }
}


        // Procesar edad
        const [edadMin, edadMax] = edadRange.noUiSlider.get().map(Number);
        if (edadMin !== 18 || edadMax !== 50) {
            if (shouldAddToUrl()) {
                url += `/edad-${edadMin}-${edadMax}`;
                hasAddedUrlFilter = true;
            } else {
                params.append('e', `${edadMin}-${edadMax}`);
            }
        }

        // Procesar categoría de precio
const selectedCategory = document.querySelector('.price-category.active');
if (selectedCategory) {
    const categoria = selectedCategory.dataset.categorias; // Cambiamos de .dataset.categoria a .dataset.categorias
    if (categoria && categoria !== 'undefined') {
        if (shouldAddToUrl()) {
            url += `/${categoria.toLowerCase()}`; // Añadimos .toLowerCase() para asegurar consistencia
            hasAddedUrlFilter = true;
        } else {
            params.append('categoria', categoria.toLowerCase());
        }
    }
} else {
    const [precioMin, precioMax] = precioRange.noUiSlider.get().map(Number);
    if (precioMin !== 0 || precioMax !== 300000) {
        if (shouldAddToUrl()) {
            url += `/precio-${precioMin}-${precioMax}`;
            hasAddedUrlFilter = true;
        } else {
            params.append('p', `${precioMin}-${precioMax}`);
        }
    }
}

        // Procesar disponibilidad
        if (disponibleCheck.classList.contains('selected')) {
            if (shouldAddToUrl()) {
                url += '/disponible';
                hasAddedUrlFilter = true;
            } else {
                params.append('disponible', '1');
            }
        }

        // Procesar atributos
        const checkedAtributos = Array.from(document.querySelectorAll('input[name="atributos[]"]:checked')).map(cb => cb.value);
        if (checkedAtributos.length > 0) {
            if (shouldAddToUrl()) {
                url += `/${checkedAtributos[0]}`;
                hasAddedUrlFilter = true;
                if (checkedAtributos.length > 1) {
                    params.append('a', checkedAtributos.slice(1).join(','));
                }
            } else {
                params.append('a', checkedAtributos.join(','));
            }
        }

        // Procesar servicios
        const checkedServicios = Array.from(document.querySelectorAll('input[name="servicios[]"]:checked')).map(cb => cb.value);
        if (checkedServicios.length > 0) {
            if (shouldAddToUrl()) {
                url += `/${checkedServicios[0]}`;
                hasAddedUrlFilter = true;
                if (checkedServicios.length > 1) {
                    params.append('s', checkedServicios.slice(1).join(','));
                }
            } else {
                params.append('s', checkedServicios.join(','));
            }
        }

        // Procesar reseñas verificadas
        if (resenaCheck.classList.contains('selected')) {
            if (shouldAddToUrl()) {
                url += '/resena-verificada';
                hasAddedUrlFilter = true;
            } else {
                params.append('resena', '1');
            }
        }

        // Construir URL final
        const queryString = params.toString();
        if (queryString) {
            url += `?${queryString}`;
        }
        
        window.location.href = url;
    });

    // Reset de filtros
    document.getElementById('resetFilters').addEventListener('click', () => {
        form.reset();
        edadRange.noUiSlider.reset();
        precioRange.noUiSlider.reset();
        nacionalidadSelect.value = '';
        barrioSelect.value = '';
        barrioContainer.style.display = ciudadSelect.value.toLowerCase().includes('santiago') ? 'block' : 'none';
        disponibleCheck.classList.remove('selected');
        resenaCheck.classList.remove('selected');
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.querySelectorAll('.price-category').forEach(category => {
            category.classList.remove('active');
        });
        precioRange.noUiSlider.set([0, 300000]);
        document.getElementById('categoriaFilter').value = '';

        // Reset "mostrar más/menos" buttons
        ['showMoreServices', 'showMoreAttributes'].forEach(id => {
            const button = document.getElementById(id);
            if (button && button.classList.contains('selected')) {
                button.click(); // Esto volverá a ocultar los elementos extras
            }
        });
    });

    // Mostrar modal
    document.querySelector('.btn-filters').addEventListener('click', () => modal.show());

    // Inicializar handlers de "mostrar más/menos" si existen
    ['showMoreServices', 'showMoreAttributes'].forEach(id => {
        const button = document.getElementById(id);
        if (button) {
            button.addEventListener('click', function() {
                const container = document.getElementById(id === 'showMoreServices' ? 'serviciosContainer' : 'atributosContainer');
                const labels = container.querySelectorAll('.checkbox-label');
                const isExpanded = this.classList.contains('selected');
                
                labels.forEach((label, index) => {
                    if (index >= 8) {
                        label.style.display = isExpanded ? 'none' : 'block';
                    }
                });
                
                this.classList.toggle('selected');
                this.querySelector('.review-text').textContent = isExpanded ? 'Mostrar más' : 'Mostrar menos';
            });
        }
    });
});
</script>

<script>
const initBlogSwiper = () => {
    if (window.innerWidth < 768) {
        const swiper = new Swiper('.swiper-blog', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            effect: 'slide',
            speed: 400,
            autoHeight: true,
            grabCursor: true,
            touchRatio: 1,
            touchAngle: 45,
            resistance: false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });
    }
};

// Asegurarnos de que el Swiper se inicialice correctamente
document.addEventListener('DOMContentLoaded', initBlogSwiper);
</script>
<script>
 document.addEventListener('DOMContentLoaded', function() {
    const bullets = document.querySelectorAll('.swiper-pagination-bullet');
    const video = document.querySelector('.escortperfil-video');
    
    bullets.forEach((bullet) => {
        bullet.addEventListener('click', function() {
            const videoUrl = this.dataset.video;
            
            // Actualizar video solo si cambia la URL
            if(video.src !== videoUrl) {
                video.src = videoUrl;
                video.load(); // Recargar el video
                video.play(); // Opcional: reproducir automáticamente al cambiar
            }
            
            // Actualizar bullet activo
            bullets.forEach(b => b.classList.remove('swiper-pagination-bullet-active'));
            this.classList.add('swiper-pagination-bullet-active');
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.querySelector('.dropdown-button');
    const dropdownContent = document.querySelector('.dropdown-content');
    const dropdownLinks = document.querySelectorAll('.dropdown-content a');
    const dropdown = document.querySelector('.dropdown');
    const defaultText = 'CIUDADES';

    // Asegurar que el texto inicial sea "CIUDADES"
    dropdownButton.textContent = defaultText;

    // Deshabilitar el comportamiento por defecto del botón
    dropdownButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // Manejar hover en dispositivos no móviles
    if (!('ontouchstart' in window)) {
        dropdown.addEventListener('mouseenter', function() {
            dropdownContent.style.display = 'flex';
        });

        dropdown.addEventListener('mouseleave', function() {
            dropdownContent.style.display = 'none';
            dropdownButton.textContent = defaultText; // Restaurar texto al salir
        });
    }

    // Cerrar el menú al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdownContent.style.display = 'none';
            dropdownButton.textContent = defaultText;
        }
    });

    // Manejar la selección de ciudad sin cambiar el texto del botón
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mantener el texto como "CIUDADES"
            dropdownButton.textContent = defaultText;
            
            // Obtener y navegar a la URL del enlace
            const href = this.getAttribute('href');
            window.location.href = href;
        });
    });

    // Soporte específico para dispositivos móviles
    if ('ontouchstart' in window) {
        dropdownButton.addEventListener('touchstart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownContent.style.display = 
                dropdownContent.style.display === 'flex' ? 'none' : 'flex';
            dropdownButton.textContent = defaultText; // Mantener texto en móviles
        });

        // Cerrar al tocar fuera en móviles
        document.addEventListener('touchstart', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdownContent.style.display = 'none';
                dropdownButton.textContent = defaultText;
            }
        });
    }

    // Asegurar que el texto siempre sea "CIUDADES"
    dropdownButton.textContent = defaultText;

    // Prevenir que los clicks dentro del dropdown cierren el menú
    dropdownContent.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

function toggleText() {
    const textoCorto = document.getElementById('texto-corto');
    const textoCompleto = document.getElementById('texto-completo');
    const btnVerMas = document.getElementById('btn-ver-mas');

    if (textoCompleto.style.display === 'none') {
        textoCorto.style.display = 'none';
        textoCompleto.style.display = 'inline';
        btnVerMas.textContent = 'ver menos';
    } else {
        textoCorto.style.display = 'inline';
        textoCompleto.style.display = 'none';
        btnVerMas.textContent = 'ver más';
    }
}

</script>

<script>
console.log('Script iniciado');

function showLoginImageAlert() {
    console.log('Función showLoginImageAlert llamada');
    
    // Eliminar alerta existente si hay alguna
    const existingAlert = document.querySelector('.login-alert');
    if (existingAlert) {
        console.log('Alerta existente encontrada y removida');
        existingAlert.remove();
    }

    console.log('Creando nueva alerta');
    // Crear la alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = 'login-alert';
    alertDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.7);
    `;
    
    // Contenedor interno para el mensaje
    const messageContainer = document.createElement('div');
    messageContainer.style.cssText = `
        background: rgb(42, 42, 42);
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    `;
    
    messageContainer.innerHTML = `
        <span style="
            color: white; 
            font-size: 18px; 
            text-align: center;
            display: block;
            width: 100%;
            line-height: 1.5;
            font-weight: 500;
        ">
            Debes tener una cuenta para ver este contenido
        </span>
        <a href="/login" 
           style="
               background: #e00037; 
               color: white; 
               padding: 14px 28px; 
               text-decoration: none; 
               text-transform: uppercase; 
               font-weight: bold; 
               font-size: 15px;
               border-radius: 4px;
               transition: all 0.3s ease;
               display: inline-block;
               margin: 0 auto;
               border: none;
               cursor: pointer;
           ">
            ACCEDER
        </a>
    `;

    alertDiv.appendChild(messageContainer);
    document.body.appendChild(alertDiv);
    console.log('Alerta añadida al DOM');

    // Cerrar al hacer click en cualquier parte
    alertDiv.addEventListener('click', (e) => {
        if (e.target === alertDiv) {
            console.log('Click en overlay detectado');
            alertDiv.classList.add('hiding');
            alertDiv.style.opacity = '0';
            setTimeout(() => {
                alertDiv.remove();
                console.log('Alerta removida por click');
            }, 300);
        }
    });

    // Remover automáticamente después de 2.7 segundos
    setTimeout(() => {
        console.log('Iniciando eliminación automática');
        alertDiv.classList.add('hiding');
        alertDiv.style.opacity = '0';
        setTimeout(() => {
            alertDiv.remove();
            console.log('Alerta removida automáticamente');
        }, 300);
    }, 2700);
}

// Agregar event listeners a las imágenes bloqueadas
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded disparado');
    const blockedImages = document.querySelectorAll('.blocked-image');
    console.log('Imágenes bloqueadas encontradas:', blockedImages.length);
    
    blockedImages.forEach((img, index) => {
        console.log(`Agregando event listener a imagen bloqueada ${index + 1}`);
        img.addEventListener('click', function(e) {
            console.log(`Imagen bloqueada ${index + 1} clickeada`);
            e.preventDefault();
            showLoginImageAlert();
        });
    });
});

// Exponer la función globalmente para debugging
window.showLoginImageAlert = showLoginImageAlert;
console.log('Script completamente cargado');
</script>
<script>
const whatsappStyleSheet = document.createElement('style');
whatsappStyleSheet.textContent = `
    @media (max-width: 767px) {
        body .escortperfil-actions {
            position: fixed !important;
            top: 800px !important;
            bottom: 90px !important;
            padding: 3px !important;
            z-index: 1001 !important;
            display: flex !important;
            justify-content: space-around !important;
            transition: transform 0.3s ease !important;
        }

        body .escortperfil-actions.hidden {
            transform: translateY(100%) !important;
        }

        body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 430px) {
        body .escortperfil-actions {
            top: 825px !important;
            bottom: 90px !important;
        }

        body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 415px) {
        body .escortperfil-actions {
            top: 825px !important;
            bottom: 90px !important;
        }

                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    
    @media screen and (max-width: 414px) {
        body .escortperfil-actions {
            top: 825px !important;
            bottom: 90px !important;
        }
    }

    @media screen and (max-width: 414px) and (max-height: 736px) {
                body .escortperfil-btn {
            margin: -178px 0px !important;
            flex: 1 !important;
        }
}

@media screen and (max-width: 414px) and (min-height: 737px) {
                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
}

    @media screen and (max-width: 413px) {
        body .escortperfil-actions {
            top: 840px !important;
            bottom: 90px !important;
        }

        body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 411px) {
        body .escortperfil-actions {
            top: 772px !important;
            bottom: 90px !important;
        }

                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

        @media screen and (max-width: 411px) and (min-height: 731px) {
        body .escortperfil-actions {
            top: 658px !important;
            bottom: 90px !important;
        }
}

    @media screen and (max-width: 411px) and (min-height: 823px) {
        body .escortperfil-actions {
            top: 754px !important;
            bottom: 90px !important;
        }
}

    @media screen and (max-width: 393px) {
        body .escortperfil-actions {
            top: 772px !important;
            bottom: 90px !important;
        }

                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 391px) {
        body .escortperfil-actions {
            top: 762px !important;
            bottom: 90px !important;
        }

                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 389px) {
        body .escortperfil-actions {
            top: 782px !important;
            bottom: 90px !important;
        }
            
                body .escortperfil-btn {
            margin: -149px -10px !important;
            flex: 1 !important;
        }
    }

    @media screen and (max-width: 375px) {
        body .escortperfil-actions {
                        top: 582px !important;
            bottom: 90px !important;
        }

                body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

        @media screen and (max-width: 375px) and (min-height: 812px) {
        body .escortperfil-actions {
            top: 740px !important;
            bottom: 90px !important;
        }
}

    @media screen and (max-width: 361px) {
        body .escortperfil-actions {
                        top: 664px !important;
            bottom: 90px !important;
        }
                    body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }

        @media screen and (max-width: 344px) {
        body .escortperfil-actions {
            top: 620px !important;
            bottom: 90px !important;
        }
            body .escortperfil-btn {
            margin: -16px 0px !important;
            flex: 1 !important;
        }
    }
        

    @media screen and (max-width: 361px) and (min-height: 800px) {
        body .escortperfil-actions {
            top: 720px !important;
            bottom: 90px !important;
        }
}
    @media screen and (max-width: 344px) and (min-height: 882px) {
        body .escortperfil-actions {
            top: 800px !important;
            bottom: 90px !important;
        }

        body .escortperfil-btn {
            margin: -16px -10px !important;
            flex: 1 !important;
        }
}
`;
document.head.appendChild(whatsappStyleSheet);

// Script para manejar el scroll
document.addEventListener('DOMContentLoaded', function() {
    const actionsBar = document.querySelector('.escortperfil-actions');
    let lastScrollY = window.scrollY;
    let ticking = false;

    if (!actionsBar) return;

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const currentScrollY = window.scrollY;
                
                // Mostrar/ocultar barra de acciones basado en el scroll
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    actionsBar.classList.add('hidden');
                } else {
                    actionsBar.classList.remove('hidden');
                }

                lastScrollY = currentScrollY;
                ticking = false;
            });

            ticking = true;
        }
    });
});
    </script>
</body>

</html>