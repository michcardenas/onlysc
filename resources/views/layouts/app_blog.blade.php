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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/nouislider/distribute/nouislider.min.js"></script>
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
                <a href="/foro/" class="nav-link">FORO ESCORTS</a>
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

           <!-- Ciudad se mantiene fuera del form -->
           <div class="filters-container">
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
           </div>

           <form id="filterForm">
               <div class="modal-body">
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
                           <div class="price-category" data-min="0" data-max="300000" data-categorias="Under">
                               <span class="category-name">Under</span>
                           </div>
                           <div class="price-category" data-min="0" data-max="300000" data-categorias="masajes">
                               <span class="category-name">Masajes</span>
                           </div>
                       </div>
                       <div class="price-categories">
                           <div class="price-category" data-min="0" data-max="70000" data-categorias="premium">
                               <span class="category-name">Premium</span>
                           </div>
                           <div class="price-category" data-min="70000" data-max="130000" data-categorias="vip">
                               <span class="category-name">VIP</span>
                           </div>
                           <div class="price-category" data-min="130000" data-max="300000" data-categorias="de_lujo">
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/kql75cq5qezo4szqfurmv0g3uxe4cpksh2e1f4zzk1autj96/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

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