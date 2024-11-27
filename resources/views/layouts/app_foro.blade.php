<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Foro - Escorts</title>
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">

    <!-- Scripts -->
    <script src="https://cdn.tiny.cloud/1/z94ao1xzansr93pi0qe5kfxgddo1f4ltb8q7qa8pw9g52txs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
    tinymce.init({
        selector: '#comentario', // Cambiado para coincidir con tu ID de textarea
        plugins: 'emoticons',
        toolbar: 'undo redo | bold italic underline | emoticons',
        menubar: false,
        height: 200,
        forced_root_block: false,

        // Configuración para prevenir etiquetas HTML
        verify_html: false,
        cleanup: true,
        paste_as_text: true, // Pega todo como texto plano

        // Evento para limpiar el contenido antes de enviar
        setup: function (editor) {
            editor.on('change', function () {
                // Obtener el texto plano y actualizar el textarea
                let contenidoLimpio = editor.getBody().innerText;
                editor.targetElm.value = contenidoLimpio;
            });

            // Antes de enviar el formulario
            editor.getElement().form.addEventListener('submit', function(e) {
                let contenidoLimpio = editor.getBody().innerText;
                editor.setContent(contenidoLimpio);
            });
        },

        // Estilo visual para coincidir con tu diseño
        skin: 'oxide',
        content_css: false,
        content_style: `
            body {
                background-color: #fffff;
                color: #000;
                font-family: 'Poppins', sans-serif;
                padding: 10px;
                border-radius: 4px;
            }
        `,

        init_instance_callback: function (editor) {
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

            // Mantener la validación de Laravel
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
</script>




</head>

<body>
    <!-- Navbar original -->
    <header>
        <nav class="navbar navbar-top">
            <!-- Sección izquierda con el botón "PUBLICATE" -->
            <div class="navbar-left">
                <button class="btn-publish">PUBLICATE</button>
            </div>

            <!-- Sección derecha con el selector de ubicación y botones de login/register -->
            <div class="navbar-right">
                <div class="location-dropdown">
                    <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon">
                    <select name="location" id="location">
                        <option value="" disabled selected>Seleccionar ciudad</option>
                        @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->id }}">{{ ucfirst($ciudad->nombre) }}</option>
                        @endforeach
                    </select>
                </div>



                <!-- Verificamos si el usuario está autenticado -->
                @if(Auth::check())
                <a class="btn-user">{{ Auth::user()->name }}</a>
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
            <!-- Logo, barra de búsqueda y botón de filtros -->
            <div class="navbar-left">
                <a href="/" class="logo">
                    <img src="{{ asset('images/logo_v2.png') }}" alt="Logo" class="logo1">
                </a>

                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <input type="text" placeholder="Buscar por nombre, servicio o atributo...">
                    <button type="submit" class="btn-search">
                        <img src="{{ asset('images/search.svg') }}" alt="search icon" class="search-icon">
                    </button>
                </div>

                <!-- Botón de Filtros -->
                <button class="btn-filters">
                    <img src="{{ asset('images/filtro.svg') }}" alt="Filtros" class="icon-filter"> Filtros
                </button>
            </div>

            <!-- Enlaces de navegación con Select de CATEGORÍAS -->
            <div class="navbar-right">
                <a href="/" class="nav-link">INICIO</a>

                <!-- Select de categorías -->
                <select name="categorias" id="categorias" class="nav-link">
                    <option value="" disabled selected>CATEGORÍAS</option>
                    <option value="DELUXE">DELUXE</option>
                    <option value="VIP">VIP</option>
                    <option value="PREMIUM">PREMIUM</option>
                    <option value="MASAJES">MASAJES</option>
                </select>

                <a href="#" class="nav-link">FAVORITOS</a>
                <a href="#" class="nav-link">BLOG</a>
                <a href="#" class="nav-link">FORO</a>
            </div>
        </div>



    </header>

    <!-- Aquí se insertará el contenido de las vistas que extiendan este layout -->
    <main>
        @yield('content')
    </main>


    <!-- Footer original -->
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
        <!-- Sección del logo, enlaces y redes sociales -->
        <div class="custom-footer-bottom">
            <div class="custom-container">
                <!-- Logo centrado -->
                <div class="custom-footer-logo">
                    <img src="{{ asset('images/logo_XL-2.png') }}" alt="OnlyEscorts Logo" class="custom-logo-footer">
                </div>

                <!-- Enlaces en tres filas -->
                <div class="custom-footer-middle">
                    <div class="custom-links">
                        <div class="custom-link-row">
                            <a href="#">FORO</a>
                            <a href="#">BLOG</a>
                        </div>
                        <div class="custom-link-row">
                            <a href="#">CONTACTO</a>
                            <a href="#">POLÍTICA DE PRIVACIDAD</a>
                        </div>
                        <div class="custom-link-row">
                            <a href="#">REGISTRO</a>
                            <a href="#">PUBLICATE</a>
                        </div>
                    </div>
                </div>

                <!-- Redes sociales centradas -->
                <div class="custom-social-links">
                    <a href="#"><img src="{{ asset('images/facebook.svg') }}" alt="Facebook" class="custom-social-icon"></a>
                    <a href="#"><img src="{{ asset('images/instagram.svg') }}" alt="Instagram" class="custom-social-icon"></a>
                    <a href="#"><img src="{{ asset('images/x.svg') }}" alt="X" class="custom-social-icon"></a>
                </div>
            </div>
        </div>


        <!-- Derechos reservados y aviso -->
        <div class="footer-info">
            <p>© 2024 Only Escorts | Todos los derechos reservados</p>
        </div>
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