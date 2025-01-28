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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/foro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    @if(isset($metaTags))
    {{-- Meta título --}}
    @if(isset($metaTags->meta_title))
        <title>{{ $metaTags->meta_title }}</title>
    @endif
    
    {{-- Meta tags principales --}}
    @if(isset($metaTags->meta_description))
        <meta name="description" content="{{ $metaTags->meta_description }}">
    @endif
    @if(isset($metaTags->meta_keywords))
        <meta name="keywords" content="{{ $metaTags->meta_keywords }}">
    @endif
    
    {{-- Meta tags adicionales --}}
    @if(isset($metaTags->meta_author))
        <meta name="author" content="{{ $metaTags->meta_author }}">
    @endif
    @if(isset($metaTags->meta_robots))
        <meta name="robots" content="{{ $metaTags->meta_robots }}">
    @endif
    
    {{-- URL Canónica --}}
    @if(isset($metaTags->canonical_url))
        <link rel="canonical" href="{{ $metaTags->canonical_url }}" />
    @endif
    
    {{-- Open Graph tags para redes sociales --}}
    @if(isset($metaTags->meta_title))
        <meta property="og:title" content="{{ $metaTags->meta_title }}">
    @endif
    @if(isset($metaTags->meta_description))
        <meta property="og:description" content="{{ $metaTags->meta_description }}">
    @endif
    <meta property="og:type" content="website">
    @if(isset($metaTags->canonical_url))
        <meta property="og:url" content="{{ $metaTags->canonical_url }}">
    @endif
@endif
    <!-- Scripts -->
    <script src="https://cdn.tiny.cloud/1/grz9xs9xkslcyiomigsaezo439rjdxjxzbl483z5a17k9z70/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<style>
/* Layout principal con flexbox */
.foro-layout {
    display: flex;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

/* Columna principal */
.foro-main-column {
    flex: 1;
    min-width: 0;
}

/* Grid de tarjetas de foro */
.foro-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Estilos de las tarjetas */
.foro-card {
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    text-decoration: none;
    color: inherit;
    height: 450px; 
}

.foro-card:hover {
    transform: translateY(-3px);
}

.foro-card-content {
    padding: 1.5rem;
    height: 30%; /* 30% de la tarjeta para el contenido */
    overflow: hidden;
}

.foro-card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.foro-card-description {
    color: #666;
    line-height: 1.5;
}
.foro-card-image {
    flex: 1; /* Toma el espacio restante */
    position: relative;
    overflow: hidden;
    height: 55%;
}

.foro-card-image img {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
}}

.foro-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #666;
    height: 15%; 
}

.foro-card-footer svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

/* Barra lateral */
.foro-sidebar {
    width: 300px;
    flex-shrink: 0;
}

/* Estilos de comentarios */
.foro-comments {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.foro-comment-link {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.foro-comment-link:hover {
    background-color: #f8f9fa;
}

.foro-comment {
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.foro-comment:last-child {
    border-bottom: none;
}

/* Paginación */
.foro-pagination {
    margin-top: 2rem;
}

.foro-pagination nav {
    display: flex;
    justify-content: center;
    font-size: 0.9rem;
}

.foro-pagination .page-item {
    display: inline-block;
    margin: 0 3px;
}

.foro-pagination .page-link {
    display: block;
    padding: 6px 10px;
    font-size: 0.85rem;
    color: #007bff;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #fff;
    transition: background-color 0.3s, color 0.3s;
}

.foro-pagination .page-link:hover {
    background-color: #007bff;
    color: #fff;
}

.foro-pagination .page-item.active .page-link {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    pointer-events: none;
}

.foro-pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    pointer-events: none;
}
.foro-banner-content {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }

    .foro-texto_banner {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 1rem; /* Espacio entre los elementos */
    }

    .foro-texto_banner h1 {
        margin: 0; /* Elimina márgenes por defecto */
    }

    .foro-texto_banner h2 {
        margin: 0; /* Elimina márgenes por defecto */
        font-size: 1.5rem;
        color: #ffffff;
    }
/* Media queries para responsividad */
@media (max-width: 1024px) {
    .foro-layout {
        flex-direction: column;
    }

    .foro-sidebar {
        width: 100%;
    }

    .foro-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .foro-search-container {
   
    margin: 2rem 0px 1rem 1rem;
 
}
    
    .foro-card-image img {
  
    object-fit: fill;
}
    .foro-card {
        max-width: 500px;
        margin: 0 auto;
    }

    .foro-card-image {
        height: 180px;
    }

    .foro-card-content {
        padding: 1rem;
    }

    .foro-card-footer {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 480px) {
    .foro-layout {
        padding: 0.5rem;
    }

    .foro-card {
        max-width: 100%;
    }

    .foro-card-image {
        height: 160px;
    }

    .foro-card-title {
        font-size: 1.1rem;
    }

    .foro-card-description {
        font-size: 0.9rem;
    }

    .foro-card-footer {
        font-size: 0.8rem;
    }
}

/* Mantén los estilos existentes para foro-card, foro-comments, etc. */
</style>
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

document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('foro-search-btn');
    const searchInput = document.getElementById('foro-search-input');

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function () {
            const query = searchInput.value;

            if (query.trim() !== '') {
    fetch(`/buscar?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(foros => {
            const resultsContainer = document.querySelector('.foro-grid'); // Seleccionamos el contenedor de tarjetas
            resultsContainer.innerHTML = ''; // Limpiamos las tarjetas existentes

            if (foros.length > 0) {
                foros.forEach(foro => {
                    // Crear la tarjeta con la misma estructura existente
                    const foroElement = document.createElement('a');
                    foroElement.href = `/foro/${foro.id}`;
                    foroElement.classList.add('foro-card');
                    foroElement.innerHTML = `
                        <div class="foro-card-content">
                            <h3 class="foro-card-title">${foro.titulo}</h3>
                            <p class="foro-card-description">${foro.subtitulo}</p>
                        </div>
                        <div class="foro-card-image">
                            ${
                                foro.foto
                                    ? `<img src="/storage/${foro.foto}" alt="${foro.titulo}">`
                                    : `<img src="/images/default-foro.jpg" alt="Imagen por defecto">`
                            }
                        </div>
                        <div class="foro-card-footer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                            </svg>
                            <span>Por: ${foro.nombre_usuario}</span>
                            <span class="ml-2">${foro.fecha}</span>
                        </div>
                    `;
                    // Añadimos la tarjeta al contenedor
                    resultsContainer.appendChild(foroElement);
                });
            } else {
                // Mostrar un mensaje si no hay resultados
                resultsContainer.innerHTML = '<p>No se encontraron resultados.</p>';
            }
        })
        .catch(error => console.error('Error al buscar:', error));
}

        });
    } else {
        console.error('No se encontraron los elementos necesarios');
    }




    // Primero verificamos que existan los elementos necesarios
    const contenido = document.querySelector('.blog-text');
    const contenidosDinamicos = document.getElementById('contenidos-dinamicos');

    // Verificar si los elementos existen antes de continuar
    if (!contenido || !contenidosDinamicos) {
        console.log('No se encontraron los elementos necesarios');
        return;
    }

    // Función para generar un ID único para cada encabezado
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    // Obtener todos los encabezados h1 y h2 del contenido
    const headings = contenido.querySelectorAll('h1, h2');

    // Verificar si hay encabezados antes de crear la lista
    if (headings.length === 0) {
        contenidosDinamicos.innerHTML = '<p>No hay secciones disponibles</p>';
        return;
    }

    // Generar la tabla de contenidos
    const ul = document.createElement('ul');
    ul.className = 'contents-list';
    
    headings.forEach((heading, index) => {
        // Generar ID único para el encabezado si no tiene uno
        if (!heading.id) {
            heading.id = `heading-${slugify(heading.textContent)}-${index}`;
        }

        // Crear elemento de la lista
        const li = document.createElement('li');
        const a = document.createElement('a');
        
        // Estilizar según nivel de encabezado
        if (heading.tagName === 'H2') {
            li.style.paddingLeft = '1rem';
        }

        a.href = `#${heading.id}`;
        a.textContent = heading.textContent;
        a.className = 'table-link';
        
        li.appendChild(a);
        ul.appendChild(li);
    });

    // Limpiar el contenido existente y agregar la nueva lista
    contenidosDinamicos.innerHTML = '';
    contenidosDinamicos.appendChild(ul);

    // Agregar comportamiento de scroll suave
    document.querySelectorAll('.table-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

document.getElementById('foro-search-btn').addEventListener('click', function() {
    const query = document.getElementById('foro-search-input').value;

    if (query.trim() !== '') {
        fetch(`/buscar?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(foros => {
                const resultsContainer = document.getElementById('foro-results');
                resultsContainer.innerHTML = ''; // Limpia los resultados previos

                if (foros.length > 0) {
                    foros.forEach(foro => {
                        const foroElement = document.createElement('div');
                        foroElement.classList.add('foro-card');
                        foroElement.innerHTML = `
                            <h3>${foro.titulo}</h3>
                            <p>${foro.subtitulo}</p>
                            <p>${foro.contenido}</p>
                        `;
                        resultsContainer.appendChild(foroElement);
                    });
                } else {
                    resultsContainer.innerHTML = '<p>No se encontraron resultados.</p>';
                }
            })
            .catch(error => console.error('Error al buscar:', error));
    }
});

</script>




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
</body>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
@stack('scripts')
    
    <!-- Script específico del blog -->
    @if(request()->is('blog*'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($categorias))
            @foreach($categorias as $categoria)
                @php
                    $articulos_count = $categoria->articles()->where('estado', 'publicado')->count();
                @endphp
                
                @if($articulos_count > 3)
                    new Swiper('.blog-carousel-{{ $categoria->id }}', {
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
                                spaceBetween: 20,
                            },
                            1024: {
                                slidesPerView: 'auto',
                                spaceBetween: 30,
                            }
                        }
                    });
                @endif
            @endforeach
        @endif
    });
    </script>
    @endif

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

        // Agregar esta función
        const handleModalClose = () => {
            // Prevenir la recarga del formulario
            form.reset();
            window.history.replaceState({}, document.title, window.location.href);
        };

        modal._element.addEventListener('hidden.bs.modal', handleModalClose);

        document.querySelector('.btn-close')?.addEventListener('click', () => {
            modal.hide();
        });

        // Funciones de normalización
        const normalizeText = (text) => {
            return text.toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "") // Remover acentos
                .replace(/\s+/g, '-') // Espacios a guiones
                .replace(/[^a-z0-9-]/g, '-') // Caracteres especiales a guiones
                .replace(/-+/g, '-') // Evitar múltiples guiones seguidos
                .replace(/^-|-$/g, ''); // Remover guiones al inicio y final
        };

        const normalizeString = (text) => {
            return text.toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .trim();
        };

        function showFiltroAlert(message) {
            const alertContainer = document.querySelector('.filtro-alert-container');
            const alertElement = document.createElement('div');
            alertElement.className = 'filtro-custom-alert';

            alertElement.innerHTML = `
        <span class="filtro-alert-message">${message}</span>
        <button class="filtro-alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;

            alertContainer.appendChild(alertElement);

            // Auto cerrar después de 5 segundos
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

            // Si hay nacionalidad seleccionada
            if (nacionalidadSelect.value) filterCount++;

            // Si hay edad diferente del default
            const [edadMin, edadMax] = edadRange.noUiSlider.get().map(Number);
            if (edadMin !== 18 || edadMax !== 50) filterCount++;

            // Si hay precio/categoría seleccionada
            const selectedCategory = document.querySelector('.price-category.active');
            if (selectedCategory ||
                (precioRange.noUiSlider.get()[0] !== 0 ||
                    precioRange.noUiSlider.get()[1] !== 300000)) {
                filterCount++;
            }

            // Si está disponible
            if (disponibleCheck.classList.contains('selected')) filterCount++;

            // Si hay atributos seleccionados
            const checkedAtributos = document.querySelectorAll('input[name="atributos[]"]:checked');
            if (checkedAtributos.length > 0) filterCount++;

            // Si hay servicios seleccionados
            const checkedServicios = document.querySelectorAll('input[name="servicios[]"]:checked');
            if (checkedServicios.length > 0) filterCount++;

            // Si tiene reseña verificada
            if (resenaCheck.classList.contains('selected')) filterCount++;

            return filterCount;
        };

        // Función para determinar si un filtro debe ir en la URL
        const shouldAddToUrl = () => {
            if (hasAddedUrlFilter) return false;

            const isSantiago = ciudadSelect.value.toLowerCase() === 'santiago';
            const currentFilters = countUrlFilters();

            // Si es Santiago y tiene barrio seleccionado, permitimos una variable adicional
            if (isSantiago && barrioSelect.value) {
                return true; // Siempre permitimos el primer filtro después del barrio
            }

            // Para otros casos, solo permitimos un filtro
            return true;
        };

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
            start: [0, 300000],
            connect: true,
            step: 1000,
            range: {
                'min': 0,
                'max': 300000
            }
        });

        // Gestión de categorías de precio
        const handlePriceCategories = () => {
            const priceCategories = document.querySelectorAll('.price-category');
            const isSantiago = ciudadSelect.value.toLowerCase() === 'santiago';

            priceCategories.forEach(category => {
                category.style.display = isSantiago ? 'block' : 'none';
            });

            if (!isSantiago) {
                precioRange.noUiSlider.set([0, 300000]);
                document.getElementById('categoriaFilter').value = '';
                priceCategories.forEach(category => {
                    category.classList.remove('active');
                });
            }
        };

        ciudadSelect.addEventListener('change', handlePriceCategories);

        // Event listeners para categorías de precio
        document.querySelectorAll('.price-category').forEach(category => {
            category.addEventListener('click', () => {
                const min = parseInt(category.dataset.min);
                const max = parseInt(category.dataset.max);
                const categoriaValor = category.dataset.categoria;

                if (category.classList.contains('active')) {
                    category.classList.remove('active');
                    precioRange.noUiSlider.set([0, 300000]);
                    document.getElementById('categoriaFilter').value = '';
                } else {
                    document.querySelectorAll('.price-category').forEach(cat => {
                        cat.classList.remove('active');
                    });
                    category.classList.add('active');
                    precioRange.noUiSlider.set([min, max]);
                    document.getElementById('categoriaFilter').value = categoriaValor;
                }
            });
        });

        // Configuración de tooltips y actualizaciones de rangos
        const setupRangeTooltips = (range, suffix = '') => {
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

            tooltips[0].textContent = `${prefix}${min.toLocaleString()}${suffix}`;
            tooltips[1].textContent = `${prefix}${max.toLocaleString()}${suffix}`;
            minElement.textContent = `${prefix}${min.toLocaleString()}${suffix}`;
            maxElement.textContent = `${prefix}${max.toLocaleString()}${suffix}`;

            return [min, max];
        };

        // Configurar tooltips
        setupRangeTooltips(edadRange);
        setupRangeTooltips(precioRange);

        // Event listeners para rangos
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

        // Arrays de atributos y servicios
        const atributos = [
            "Busto grande", "Busto mediano", "Busto pequeño", "Cara visible",
            "Cola grande", "Cola mediana", "Cola pequeña", "Con video",
            "Contextura delgada", "Contextura grande", "Contextura mediana",
            "Depilacion full", "Depto propio", "En promocion", "English",
            "Escort independiente", "Español", "Estatura alta", "Estatura mediana",
            "Estatura pequeña", "Hentai", "Morena", "Mulata", "No fuma",
            "Ojos claros", "Ojos oscuros", "Peliroja", "Portugues",
            "Relato erotico", "Rubia", "Tatuajes", "Trigueña"
        ];

        const servicios = [
            "Anal", "Atencion a domicilio", "Atencion en hoteles", "Baile erotico",
            "Besos", "Cambio de rol", "Departamento propio", "Disfraces",
            "Ducha erotica", "Eventos y cenas", "Eyaculacion cuerpo",
            "Eyaculacion facial", "Hetero", "Juguetes", "Lesbico",
            "Lluvia dorada", "Masaje erotico", "Masaje prostatico",
            "Masaje tantrico", "Masaje thai", "Masajes con final feliz",
            "Masajes desnudos", "Masajes eroticos", "Masajes para hombres",
            "Masajes sensitivos", "Masajes sexuales", "Masturbacion rusa",
            "Oral americana", "Oral con preservativo", "Oral sin preservativo",
            "Orgias", "Parejas", "Trio"
        ];

        // Crear checkboxes con valores originales
        const createCheckboxes = (items, containerId, name) => {
            const container = document.getElementById(containerId);
            const showCount = 8;

            items.forEach((item, index) => {
                const label = document.createElement('label');
                label.className = 'checkbox-label';
                if (index >= showCount) label.style.display = 'none';

                label.innerHTML = `
                <input type="checkbox" name="${name}[]" value="${item}">
                <span class="checkbox-text">${item}</span>
            `;
                container.appendChild(label);
            });
        };

        // Gestión de mostrar más/menos
        ['showMoreServices', 'showMoreAttributes'].forEach(id => {
            document.getElementById(id).addEventListener('click', function() {
                const container = document.getElementById(id === 'showMoreServices' ? 'serviciosContainer' : 'atributosContainer');
                const labels = container.querySelectorAll('.checkbox-label');
                const isExpanded = this.classList.contains('selected');

                labels.forEach((label, index) => {
                    if (index >= 8) label.style.display = isExpanded ? 'none' : 'block';
                });

                this.classList.toggle('selected');
                this.querySelector('.review-text').textContent = isExpanded ? 'Mostrar más' : 'Mostrar menos';
            });
        });

        // Crear los checkboxes
        createCheckboxes(atributos, 'atributosContainer', 'atributos');
        createCheckboxes(servicios, 'serviciosContainer', 'servicios');

        // Gestión de barrios
        const toggleBarrioContainer = () => {
            const selectedCity = ciudadSelect.options[ciudadSelect.selectedIndex].text;
            const isSantiago = selectedCity.toLowerCase().includes('santiago');

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
                const gentilicios = {
                    'argentina': 'argentina',
                    'brasil': 'brasilena',
                    'chile': 'chilena',
                    'colombia': 'colombiana',
                    'ecuador': 'ecuatoriana',
                    'uruguay': 'uruguaya'
                };

                const gentilicio = gentilicios[nacionalidadSelect.value.toLowerCase()] ||
                    nacionalidadSelect.value.toLowerCase();

                if (shouldAddToUrl()) {
                    url += `/${gentilicio}`;
                    hasAddedUrlFilter = true;
                } else {
                    params.append('n', nacionalidadSelect.value);
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
                const categoria = selectedCategory.querySelector('.category-name')
                    .textContent.toLowerCase().replace(/\s+/g, '_');

                if (shouldAddToUrl()) {
                    url += `/${categoria}`;
                    hasAddedUrlFilter = true;
                } else {
                    params.append('categoria', categoria);
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
            const checkedAtributos = Array.from(document.querySelectorAll('input[name="atributos[]"]:checked'))
                .map(cb => cb.value);
            if (checkedAtributos.length > 0) {
                if (shouldAddToUrl()) {
                    const normalizedAtributo = normalizeText(checkedAtributos[0]);
                    url += `/${normalizedAtributo}`;
                    hasAddedUrlFilter = true;

                    // Si hay más atributos, los agregamos todos como parámetros
                    if (checkedAtributos.length > 1) {
                        params.append('a', checkedAtributos.slice(1).join(','));
                    }
                } else {
                    // Agregamos todos los atributos como parámetros
                    params.append('a', checkedAtributos.join(','));
                }
            }

            // Procesar servicios
            const checkedServicios = Array.from(document.querySelectorAll('input[name="servicios[]"]:checked'))
                .map(cb => cb.value);
            if (checkedServicios.length > 0) {
                if (shouldAddToUrl()) {
                    // Si podemos agregar a la URL, agregamos solo el primer servicio
                    const servicioParaUrl = normalizeText(checkedServicios[0]);
                    url += `/${servicioParaUrl}`;
                    hasAddedUrlFilter = true;

                    // Si hay más servicios, los agregamos todos como parámetros
                    if (checkedServicios.length > 1) {
                        params.append('s', checkedServicios.slice(1).join(','));
                    }
                } else {
                    // Agregamos todos los servicios como parámetros
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
        });

        // Mostrar modal
        document.querySelector('.btn-filters').addEventListener('click', () => modal.show());
    });
</script>

</html>