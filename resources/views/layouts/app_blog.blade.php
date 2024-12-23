<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blog') - Escorts</title>
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
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