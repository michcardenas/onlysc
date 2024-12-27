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
                <option value="{{ strtolower($ciudad->nombre) }}" 
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

                <div class="search-bar">
                    <input type="text" placeholder="Buscar por nombre, servicio o atributo...">
                    <button type="submit" class="btn-search">
                        <img src="{{ asset('images/search.svg') }}" alt="search icon" class="search-icon">
                    </button>
                </div>

                <button class="btn-filters">
                    <img src="{{ asset('images/filtro.svg') }}" alt="Filtros" class="icon-filter"> Filtros
                </button>
            </div>

            <div class="navbar-right">
                <a href="/" class="nav-link">INICIO</a>

                <select name="categorias" id="categorias" class="nav-link">
                    <option value="" disabled selected>CATEGORÍAS</option>
                    <option value="DELUXE">DELUXE</option>
                    <option value="VIP">VIP</option>
                    <option value="PREMIUM">PREMIUM</option>
                    <option value="MASAJES">MASAJES</option>
                </select>

                <a href="/mis-favoritos/" class="nav-link">FAVORITOS</a>
                <a href="/blog/" class="nav-link">BLOG</a>
                <a href="/foro/" class="nav-link">FORO</a>
            </div>
        </div>
    </header>

<!-- Modal -->
<div class="modal fade filtro-modal" id="filterModal" tabindex="-1">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title">Filtros</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
           </div>

<!-- Añade un div contenedor -->
<div class="filters-container">
    <div class="filter-ciudad">
        <div class="filter-section">
            <h6 class="range-title">Ciudad</h6>
            <select id="ciudadSelect" class="form-select">
                <option value="">Seleccionar ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->url }}" {{ isset($ciudadSeleccionada) && $ciudadSeleccionada->url == $ciudad->url ? 'selected' : '' }}>
                        {{ $ciudad->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="filtro-nac">
        <div class="filter-section">
            <h6 class="range-title">Nacionalidad</h6>
            <select name="nacionalidad" id="nacionalidadSelect" class="form-select">
                <option value="">Todas las nacionalidades</option>
                <option value="argentine">Argentina</option>
                <option value="brazilian">Brasileña</option>
                <option value="chilean">Chilena</option>
                <option value="colombian">Colombiana</option>
                <option value="ecuadorian">Ecuatoriana</option>
                <option value="uruguayan">Uruguaya</option>
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
                       <div class="range-container">
                           <div id="precioRange"></div>
                           <div class="range-values">
                               <span>$50.000</span>
                               <span>$300.000</span>
                           </div>
                       </div>
                       <input type="hidden" name="precioMin" id="precioMin">
                       <input type="hidden" name="precioMax" id="precioMax">
                   </div>

                   <!-- Contenedores para checkboxes -->
                   <!-- Removida la estructura de columnas -->
                   <div class="filter-section1">
                       <h6 class="range-title1">Servicios</h6>
                       <div id="serviciosContainer" class="servicios-grid"></div>
                   </div>
                   <div class="filter-section1">
                       <h6 class="range-title1">Atributos</h6>
                       <div id="atributosContainer" class="servicios-grid"></div>
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
                            <a href="/foro/">FORO</a>
                            <a href="/blog/">BLOG</a>
                        </div>
                        <div class="custom-link-row">
                            <a href="#">CONTACTO</a>
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
                    container.style.marginBottom = '500px';
                }
            }
        });
    </script>
    <script>
// Variables globales
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

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
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
        historiaModal.onclick = function(e) {
            if (e.target === this) {
                stopAllVideos();
                this.style.display = 'none';
                clearTimeout(timer);
            }
        }
    }

    // Configurar el botón de cerrar
    const cerrarModal = document.querySelector('.cerrar-modal');
    if (cerrarModal) {
        cerrarModal.onclick = function() {
            const modal = document.getElementById('historiaModal');
            if (modal) {
                stopAllVideos();
                modal.style.display = 'none';
                clearTimeout(timer);
            }
        }
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

    // Configurar navbar móvil
    const navbar = document.querySelector('.navbar-bottom');
    if (navbar) {
        const navbarPosition = navbar.offsetTop;
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            if (window.innerWidth <= 768) {
                if (scrollPosition > navbarPosition) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });
    }
});

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

function handleGesture() {
    if (touchendX < touchstartX) nextHistoria();
    if (touchendX > touchstartX) previousHistoria();
}
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
        top: 895px;
        right: 688px;
        background: rgb(42, 42, 42);
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        z-index: 1000;
        white-space: nowrap;
    `;
    
    alertDiv.innerHTML = `
        <span style="color: white; font-size: 14px;">Para agregar a favoritos debes tener una cuenta</span>
        <a href="/login" 
           style="background: #e00037; 
                  color: white; 
                  padding: 8px 16px; 
                  text-decoration: none; 
                  text-transform: uppercase; 
                  font-weight: bold; 
                  font-size: 14px;">
            ACCEDER
        </a>
    `;
    
    // Encontrar el botón de favoritos y añadir la alerta después de él
    const favoriteButton = document.querySelector('.favorite-button');
    if (favoriteButton && favoriteButton.parentNode) {
        favoriteButton.parentNode.insertBefore(alertDiv, favoriteButton.nextSibling);
    } else {
        document.body.appendChild(alertDiv);
    }

    // Remover con animación después de 2.7 segundos
    setTimeout(() => {
        alertDiv.classList.add('hiding');
        setTimeout(() => {
            alertDiv.remove();
        }, 300); // Duración de la animación
    }, 2700);
}

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
        textSpan.innerHTML = 'AÑADIR A<br>FAVORITOS';
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
    document.getElementById('escortperfilModalImage').src = imageSrc;
    document.getElementById('escortperfilImageModal').style.display = 'block';
    document.getElementById('escortperfilModalBackdrop').style.display = 'block';
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
    const modal = new bootstrap.Modal(document.getElementById('filterModal'));
    const form = document.getElementById('filterForm');
    
    // Inicialización de rangos
    const edadRange = document.getElementById('edadRange');
    noUiSlider.create(edadRange, {
        start: [18, 50],
        connect: true,
        step: 1,
        range: {
            'min': 18,
            'max': 50
        }
    });

    const precioRange = document.getElementById('precioRange');
    noUiSlider.create(precioRange, {
        start: [50000, 300000],
        connect: true,
        step: 1000,
        range: {
            'min': 50000,
            'max': 300000
        }
    });

    // Crear tooltips para edad
    const edadHandles = edadRange.querySelectorAll('.noUi-handle');
    edadHandles.forEach(handle => {
        const tooltip = document.createElement('div');
        tooltip.className = 'slider-tooltip';
        handle.appendChild(tooltip);
    });

    // Actualizar valores de los rangos y tooltips de edad
    edadRange.noUiSlider.on('update', (values, handle) => {
        const [min, max] = values.map(x => Math.round(Number(x)));
        const tooltips = edadRange.querySelectorAll('.slider-tooltip');
        
        tooltips[0].textContent = `${min}`;
        tooltips[1].textContent = `${max}`;
        
        const minElement = edadRange.parentElement.querySelector('.range-values span:first-child');
        const maxElement = edadRange.parentElement.querySelector('.range-values span:last-child');
        minElement.textContent = `${min} años`;
        maxElement.textContent = `${max} años`;
        document.getElementById('edadMin').value = min;
        document.getElementById('edadMax').value = max;
    });

    // Crear tooltips para precio
    const precioHandles = precioRange.querySelectorAll('.noUi-handle');
    precioHandles.forEach(handle => {
        const tooltip = document.createElement('div');
        tooltip.className = 'slider-tooltip';
        handle.appendChild(tooltip);
    });

    // Actualizar valores de los rangos y tooltips de precio
    precioRange.noUiSlider.on('update', (values, handle) => {
        const [min, max] = values.map(x => Math.round(Number(x)));
        const tooltips = precioRange.querySelectorAll('.slider-tooltip');
        
        tooltips[0].textContent = `$${min}`;
        tooltips[1].textContent = `$${max}`;
        
        const minElement = precioRange.parentElement.querySelector('.range-values span:first-child');
        const maxElement = precioRange.parentElement.querySelector('.range-values span:last-child');
        minElement.textContent = `$${min.toLocaleString()}`;
        maxElement.textContent = `$${max.toLocaleString()}`;
        document.getElementById('precioMin').value = min;
        document.getElementById('precioMax').value = max;
    });

    // Arrays de atributos y servicios
    const atributos = ["Busto Grande","Busto Mediano","Busto Pequeño","Cara Visible","Cola Grande","Cola Mediana","Cola Pequeña","Con Video","Contextura Delgada","Contextura Grande","Contextura Mediana","Depilación Full","Depto Propio","En Promoción","English","Escort Independiente","Español","Estatura Alta","Estatura Mediana","Estatura Pequeña","Hentai","Morena","Mulata","No fuma","Ojos Claros","Ojos Oscuros","Peliroja","Portugues","Relato Erotico","Rubia","Tatuajes","Trigueña"];
    const servicios = ["Anal","Atención a domicilio","Atención en hoteles","Baile Erotico","Besos","Cambio de rol","Departamento Propio","Disfraces","Ducha Erotica","Eventos y Cenas","Eyaculación Cuerpo","Eyaculación Facial","Hetero","Juguetes","Lesbico","Lluvia dorada","Masaje Erotico","Masaje prostatico","Masaje Tantrico","Masaje Thai","Masajes con final feliz","Masajes desnudos","Masajes Eroticos","Masajes para hombres","Masajes sensitivos","Masajes sexuales","Masturbación Rusa","Oral Americana","Oral con preservativo","Oral sin preservativo","Orgias","Parejas","Trio"];

    // Función para crear checkboxes
    const createCheckboxes = (items, containerId, name) => {
        const container = document.getElementById(containerId);
        items.forEach(item => {
            const label = document.createElement('label');
            label.innerHTML = `
                <input type="checkbox" name="${name}[]" value="${item}">
                ${item}
            `;
            container.appendChild(label);
        });
    };

    createCheckboxes(atributos, 'atributosContainer', 'atributos');
    createCheckboxes(servicios, 'serviciosContainer', 'servicios');

    // Manejo del formulario
    form.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const selectedCity = document.getElementById('ciudadSelect').value;
    const selectedNacionalidad = document.getElementById('nacionalidadSelect').value;
    const [edadMin, edadMax] = edadRange.noUiSlider.get().map(Number);
    const [precioMin, precioMax] = precioRange.noUiSlider.get().map(Number);
    const selectedAtributos = Array.from(document.querySelectorAll('[name="atributos[]"]:checked')).map(cb => cb.value);
    const selectedServicios = Array.from(document.querySelectorAll('[name="servicios[]"]:checked')).map(cb => cb.value);
    
    let url = `/escorts-${selectedCity}`;

    // Para filtros simples (mantener la lógica existente)
    if (selectedAtributos.length === 1 && selectedServicios.length === 0 && 
        edadMin === 18 && edadMax === 50 && 
        precioMin === 50000 && precioMax === 300000 &&
        !selectedNacionalidad) {
        url += `/${selectedAtributos[0].toLowerCase().replace(/\s+/g, '-')}`;
        window.location.href = url;
        return;
    } 
    else if (selectedServicios.length === 1 && selectedAtributos.length === 0 && 
             edadMin === 18 && edadMax === 50 && 
             precioMin === 50000 && precioMax === 300000 &&
             !selectedNacionalidad) {
        url += `/${selectedServicios[0].toLowerCase().replace(/\s+/g, '-')}`;
        window.location.href = url;
        return;
    }
    
    // Para múltiples filtros
    const params = new URLSearchParams();
    
    if (edadMin !== 18 || edadMax !== 50) {
        params.append('e', `${edadMin}-${edadMax}`);
    }
    
    if (precioMin !== 50000 || precioMax !== 300000) {
        params.append('p', `${precioMin}-${precioMax}`);
    }
    
    if (selectedAtributos.length > 0) {
        params.append('a', selectedAtributos.join(','));
    }
    
    if (selectedServicios.length > 0) {
        params.append('s', selectedServicios.join(','));
    }

    if (selectedNacionalidad) {
        params.append('n', selectedNacionalidad);
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
    document.getElementById('nacionalidadSelect').value = ''; // Agregar esta línea
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
});

    // Mostrar modal
    document.querySelector('.btn-filters').addEventListener('click', () => modal.show());
});
</script>


</body>

</html>