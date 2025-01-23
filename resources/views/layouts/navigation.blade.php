<link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png">

<style>
    /* Estilos para navegación móvil */
    .mobile-nav {
        display: none;
        /* Oculto por defecto */
    }

    /* Solo mostrar en móviles (menos de 768px) */
    @media (max-width: 767px) {
        .mobile-nav {
            display: block;
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            fill: #7f7f7f;
            /* Color morado */
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-family: 'Montserrat', sans-serif;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            padding: 4px 0;
            font-family: 'Montserrat', sans-serif;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            background-color: #fff;
            border: none;
            font-family: 'Montserrat', sans-serif;
        }

        .nav-text {
            font-size: 12px;
            margin-top: 4px;
            color: #7f7f7f;
        }

        /* Menú Desplegable */
        #menuOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0);
            z-index: 9999;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        #menuOverlay.active {
            visibility: visible;
            opacity: 1;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .menu-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/images/pexels-79380313-9007274-scaled.jpg');
            background-size: cover;
            background-position: center;
            box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.5);
            padding: 0px 0px;
            transform: translateX(100%);
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Montserrat', sans-serif;
        }

        .menu-logo {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 40px;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .menu-logo img {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .menu-logo-text {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        #menuOverlay.active .menu-content {
            transform: translateX(0);
        }

        #menuOverlay.active .menu-logo {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.3s;
        }

        .fa-search {
            font-size: 24px;
            color: #7f7f7f;
        }

        /* Enlaces del menú */
        .menu-links {
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            gap: 35px;
        }

        .menu-link {
            color: white;
            text-decoration: none;
            font-size: 24px;
            padding: 8px 0;
            display: block;
            text-align: center;
            opacity: 0;
            transform: translateX(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        #menuOverlay.active .menu-link {
            opacity: 1;
            transform: translateX(0);
        }

        #menuOverlay.active .menu-link:nth-child(1) {
            transition-delay: 0.4s;
        }

        #menuOverlay.active .menu-link:nth-child(2) {
            transition-delay: 0.5s;
        }

        #menuOverlay.active .menu-link:nth-child(3) {
            transition-delay: 0.6s;
        }

        #menuOverlay.active .menu-link:nth-child(4) {
            transition-delay: 0.7s;
        }

        #menuOverlay.active .menu-link:nth-child(5) {
            transition-delay: 0.8s;
        }

        /* Botón cerrar */
        #closeMenu {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            z-index: 10000;
            opacity: 0;
            transform: rotate(-90deg);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        #menuOverlay.active #closeMenu {
            opacity: 1;
            transform: rotate(0);
            transition-delay: 0.5s;
        }
    }

    @media (max-width: 767px) {
   .dropdown-content {
       display: none;
       position: absolute;
       flex-wrap: wrap;
       background-color: rgba(255, 255, 255, 0.9);
       width: 100%;
       box-shadow: 0 8px 16px rgba(0,0,0,0.2);
       z-index: 10001;
       max-height: 80vh;
       overflow-y: auto;
       border-radius: 8px;
   }

   .dropdown-button {
   width: 100%;
   text-align: center;
   background: none;
   border: none;
   color: white;
   font-size: 24px;
   cursor: pointer;
   font-family: 'Montserrat', sans-serif;
}


   @keyframes fadeIn {
       from {
           opacity: 0;
           transform: translateY(-10px);
       }
       to {
           opacity: 1;
           transform: translateY(0);
       }
   }

   .dropdown-column {
       flex: 1;
       padding: 15px;
       min-width: 200px;
   }

   .dropdown-column h3 {
       color: #7f7f7f;
       margin-bottom: 10px;
       font-size: 16px;
       text-align: center;
   }

   .ciudad-link {
       display: block;
       padding: 8px 12px;
       color: #333;
       text-decoration: none;
       font-size: 14px;
       text-align: center;
   }

   .ciudad-link:hover {
       background-color: #f5f5f5;
   }
}
</style>


<div class="mobile-nav">
    <!-- Menú desplegable -->
    <div id="menuOverlay">
        <div class="menu-content">
            <button id="closeMenu">✕</button>

            <div class="menu-logo">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo OnlyEscorts">
            </div>

            <div class="menu-links">
               <div class="dropdown">
                   <button class="dropdown-button menu-link">CIUDADES</button>
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
               <a href="{{ route('favoritos.show') }}" class="menu-link">Favoritos</a>
               <a href="{{ route('blog') }}" class="menu-link">Blog</a>
               <a href="{{ route('foro') }}" class="menu-link">Foro</a>
               <a href="{{ route('publicate.form') }}" class="menu-link">Publícate</a>
           </div>
        </div>
    </div>

    <!-- Barra de navegación inferior -->
    <nav class="mobile-bottom-nav">
        <div class="nav-items">
            <a href="{{ route('home') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M12 2L2 12h3v8h6v-6h2v6h6v-8h3L12 2z" />
                </svg>
                <span class="nav-text">Inicio</span>
            </a>

            <a href="#" class="nav-item" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-search"></i>
                <span class="nav-text">Filtro avanzado</span>
            </a>



            <a href="{{ route('favoritos.show') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
                <span class="nav-text">Favoritos</span>
            </a>

            <button id="menuButton" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
                </svg>
                <span class="nav-text">Menú</span>
            </button>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('menuButton');
            const menuOverlay = document.getElementById('menuOverlay');
            const closeMenu = document.getElementById('closeMenu');

            menuButton.addEventListener('click', () => {
                menuOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            closeMenu.addEventListener('click', () => {
                menuOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            menuOverlay.addEventListener('click', (e) => {
                if (e.target === menuOverlay) {
                    menuOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        document.querySelector('.dropdown-button').addEventListener('click', function(e) {
    e.preventDefault();
    this.classList.toggle('active');
});
    </script>
</div>