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
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/foro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if($metaTags)
        {{-- Meta título --}}
        <title>{{ $metaTags->meta_title }}</title>
        
        {{-- Meta tags principales --}}
        <meta name="description" content="{{ $metaTags->meta_description }}">
        <meta name="keywords" content="{{ $metaTags->meta_keywords }}">
        
        {{-- Meta tags adicionales --}}
        <meta name="author" content="{{ $metaTags->meta_author }}">
        <meta name="robots" content="{{ $metaTags->meta_robots }}">
        
        {{-- URL Canónica --}}
        @if($metaTags->canonical_url)
            <link rel="canonical" href="{{ $metaTags->canonical_url }}" />
        @endif
        
        {{-- Open Graph tags para redes sociales --}}
        <meta property="og:title" content="{{ $metaTags->meta_title }}">
        <meta property="og:description" content="{{ $metaTags->meta_description }}">
        <meta property="og:type" content="website">
        @if($metaTags->canonical_url)
            <meta property="og:url" content="{{ $metaTags->canonical_url }}">
        @endif
    @endif
    <!-- Scripts -->
    <script src="https://cdn.tiny.cloud/1/z94ao1xzansr93pi0qe5kfxgddo1f4ltb8q7qa8pw9g52txs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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

</html>