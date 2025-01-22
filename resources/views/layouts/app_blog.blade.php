<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>{{ $meta->meta_title ?? 'Blog - Escorts' }}</title>
<meta name="description" content="{{ $meta->meta_description ?? 'Descripción predeterminada' }}">
<meta name="keywords" content="{{ $meta->meta_keywords ?? 'Palabras clave predeterminadas' }}">
<meta name="robots" content="{{ $meta->meta_robots ?? 'index, follow' }}">
@if($meta->canonical_url)
    <link rel="canonical" href="{{ $meta->canonical_url }}">
@endif

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
        @endphp

        @foreach($ciudadesPorZona as $zona => $ciudadesZona)
            <div class="dropdown-column">
                <h3>{{ $zona }}</h3>
                @foreach($ciudadesZona as $ciudad)
                    <a href="/escorts-{{ $ciudad->url }}" 
                       class="ciudad-link">
                        {{ strtoupper($ciudad->nombre) }}
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

                <a href="/mis-favoritos/" class="nav-link">FAVORITOS</a>
                <a href="/blog/" class="nav-link">BLOG</a>
                <a href="/foro/" class="nav-link">FORO</a>
            </div>
        </div>
    </header>

<!-- Modal -->
<div class="modal fade filtro-modal" id="filterModal" tabindex="-1">
<div class="filtro-alert-container"></div>
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
            <select id="ciudadSelect" class="form-select" required>
                <option value="">Seleccionar ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->url }}" {{ isset($ciudadSeleccionada) && $ciudadSeleccionada->url == $ciudad->url ? 'selected' : '' }}>
                        {{ $ciudad->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="filtro-nac" id="barrioContainer" style="display: none;">
    <h6 class="range-title">Sector</h6>
    <select id="barrioSelect" class="form-select">
        <option value="">Seleccionar sector</option>
        @foreach($barriosSantiago ?? [] as $barrio)
            <option value="{{ $barrio }}" {{ isset($sectorSeleccionado) && $sectorSeleccionado == $barrio ? 'selected' : '' }}>
                {{ $barrio }}
            </option>
        @endforeach
    </select>
</div>

    <div class="filtro-nac">
        <div class="filter-section">
            <h6 class="range-title">Nacionalidad</h6>
            <select name="nacionalidad" id="nacionalidadSelect" class="form-select">
                <option value="">Todas las nacionalidades</option>
                <option value="argentina">Argentina</option>
                <option value="brasil">Brasileña</option>
                <option value="chile">Chilena</option>
                <option value="colombia">Colombiana</option>
                <option value="ecuador">Ecuatoriana</option>
                <option value="uruguay">Uruguaya</option>
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
                   <!-- Contenedores para checkboxes -->
                   <!-- Removida la estructura de columnas -->
                   <div class="filter-section1">
   <h6 class="range-title1">Servicios</h6>
   <div id="serviciosContainer" class="servicios-grid"></div>
   <div class="review-container" id="showMoreServices">
       <span class="review-text">Mostrar más</span>
   </div>
</div>
<div class="filter-section1">
   <h6 class="range-title1">Atributos</h6>
   <div id="atributosContainer" class="servicios-grid"></div>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/z94ao1xzansr93pi0qe5kfxgddo1f4ltb8q7qa8pw9g52txs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        const categorias = @json($categorias);
        const articulos = @json($articulos);

        document.addEventListener('DOMContentLoaded', function() {
            categorias.forEach(categoria => {
                const articulosCount = articulos.filter(articulo =>
                    articulo.categories.some(cat => cat.id === categoria.id)
                ).length;

                if (articulosCount >= 1) {
                    new Swiper(`.blog-carousel-${categoria.id}`, {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        grabCursor: true,
                        breakpoints: {
                            640: {
                                slidesPerView: 'auto',
                                spaceBetween: 20
                            },
                            1024: {
                                slidesPerView: 'auto',
                                spaceBetween: 30
                            }
                        }
                    });
                }
            });
        });
    </script>

    <script>
       tinymce.init({
    selector: '#comentario',
    plugins: 'emoticons',
    toolbar: 'undo redo | bold italic underline | emoticons',
    menubar: false,
    height: 200,
    forced_root_block: false,
    verify_html: true,
    cleanup: true,
    paste_as_text: true,

    setup: function(editor) {
        // Validar el contenido mientras se escribe
        editor.on('keyup', function(e) {
            let contenido = editor.getContent();
            let contenidoLimpio = contenido.replace(/(https?:\/\/[^\s]+)|(www\.[^\s]+)/gi, '');
            if (contenido !== contenidoLimpio) {
                editor.setContent(contenidoLimpio);
                editor.selection.select(editor.getBody(), true);
                editor.selection.collapse(false);
            }
        });

        // Validar al pegar contenido
        editor.on('paste', function(e) {
            let contenido = e.clipboardData.getData('text');
            let contenidoLimpio = contenido.replace(/(https?:\/\/[^\s]+)|(www\.[^\s]+)/gi, '');
            if (contenido !== contenidoLimpio) {
                e.preventDefault();
                editor.insertContent(contenidoLimpio);
            }
        });

        editor.on('change', function() {
            let contenido = editor.getBody().innerText;
            let contenidoLimpio = contenido.replace(/(https?:\/\/[^\s]+)|(www\.[^\s]+)/gi, '');
            editor.targetElm.value = contenidoLimpio;
        });

        editor.getElement().form.addEventListener('submit', function(e) {
            let contenido = editor.getBody().innerText;
            let contenidoLimpio = contenido.replace(/(https?:\/\/[^\s]+)|(www\.[^\s]+)/gi, '');
            editor.setContent(contenidoLimpio);
        });
    },

    // Validar antes de insertar contenido
    valid_elements: '-p,-span,-b,-i,-u,br,em,strong',
    invalid_elements: 'a,script,iframe,link,img',

    skin: 'oxide',
    content_css: false,
    content_style: `
        body {
            background-color: #fffff;
            color: #000;
            font-family: 'Montserrat', sans-serif;
            padding: 10px;
            border-radius: 4px;
        }
    `,

    init_instance_callback: function(editor) {
        let elementsToUpdate = editor.getContainer().querySelectorAll(
            '.tox-tinymce, .tox-editor-header, .tox-toolbar, .tox-toolbar__primary, ' +
            '.tox-toolbar__group, .tox-button, .tox-statusbar, .tox-editor-container, ' +
            '.tox-edit-area'
        );

        elementsToUpdate.forEach(function(element) {
            element.style.backgroundColor = '#fffff';
            element.style.border = 'none';
            element.style.boxShadow = 'none';
        });

        let mainContainer = editor.getContainer();
        mainContainer.style.border = '1px solid #fffff';
        mainContainer.style.boxShadow = 'none';

        editor.on('blur', function() {
            if (editor.getContent().trim().length === 0) {
                editor.targetElm.classList.add('is-invalid');
            } else {
                editor.targetElm.classList.remove('is-invalid');
            }
        });
    }
});
    </script>

    @stack('scripts')
</body>

</html>