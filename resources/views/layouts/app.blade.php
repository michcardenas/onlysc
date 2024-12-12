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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.3.2/swiper-bundle.min.js"></script>

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
                        <option value="" disabled selected>Seleccionar ciudad</option>
                        @foreach($ciudades as $ciudad)
                        <option value="{{ strtolower($ciudad->nombre) }}">{{ ucfirst($ciudad->nombre) }}</option>
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

    <main>
        @yield('content')
    </main>

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
</body>

</html>