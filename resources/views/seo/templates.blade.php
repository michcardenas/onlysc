@extends('layouts.app_login')

@section('content')
<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>
            <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="logout-button">
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
        <div class="user-info-admin">
            @if(isset($usuarioAutenticado))
            <p>Bienvenido, {{ $usuarioAutenticado->name }}
                ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
            @else
            <p>Usuario no autenticado</p>
            @endif
        </div>
    </nav>
</header>

<div class="container-fluid main-content">
    <div class="card custom-card">
        <div class="card-header">
            <h3 class="card-title">Contenido SEO para Filtros</h3>
        </div>
        <div class="card-body">
            <!-- Pestañas -->
            <ul class="nav nav-tabs custom-tabs mb-4" id="templateTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="single-tab" data-bs-toggle="tab" href="#single" role="tab">
                        Un filtro
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="multiple-tab" data-bs-toggle="tab" href="#multiple" role="tab">
                        2-4 filtros
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="complex-tab" data-bs-toggle="tab" href="#complex" role="tab">
                        Más de 4 filtros
                    </a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="templateTabsContent">
                <!-- Template para un filtro -->
                <div class="tab-pane fade show active" id="single" role="tabpanel">
                    <form action="{{ route('seo.templates.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tipo" value="single">

                        <div class="mb-3">
                            <label class="form-label">Variables disponibles:</label>
                            <ul class="list-group custom-list">
                                <li class="list-group-item"><code>{ciudad}</code> - Nombre de la ciudad</li>
                                <li class="list-group-item"><code>{nacionalidad}</code> - Nacionalidad seleccionada</li>
                                <li class="list-group-item"><code>{edad_min}</code> - Edad mínima</li>
                                <li class="list-group-item"><code>{edad_max}</code> - Edad máxima</li>
                                <li class="list-group-item"><code>{precio_min}</code> - Precio mínimo</li>
                                <li class="list-group-item"><code>{precio_max}</code> - Precio máximo</li>
                                <li class="list-group-item"><code>{atributos}</code> - Lista de atributos seleccionados</li>
                                <li class="list-group-item"><code>{servicios}</code> - Lista de servicios seleccionados</li>
                            </ul>
                            <small class="text-muted mt-2">* Todas las variables están disponibles en cualquier tipo de template. Si una variable no tiene valor, será omitida automáticamente del texto.</small>
                        </div>

                        <div class="mb-3">
                            <label for="single_template" class="form-label">Descripción para un filtro</label>
                            <textarea class="form-control custom-textarea" id="single_template" name="description_template" rows="4">{{ $templates['single'] ?? 'Encuentra escorts {nacionalidad} en {ciudad}. Explora nuestro catálogo de escorts seleccionadas.' }}</textarea>
                        </div>

                        <button type="submit" class="btn custom-button">Guardar template de un filtro</button>
                    </form>
                </div>

                <!-- Template para 2-4 filtros -->
                <div class="tab-pane fade" id="multiple" role="tabpanel">
                    <form action="{{ route('seo.templates.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tipo" value="multiple">

                        <div class="mb-3">
                            <label class="form-label">Variables disponibles:</label>
                            <ul class="list-group custom-list">
                                <li class="list-group-item"><code>{ciudad}</code> - Nombre de la ciudad</li>
                                <li class="list-group-item"><code>{nacionalidad}</code> - Nacionalidad seleccionada</li>
                                <li class="list-group-item"><code>{edad_min}</code> - Edad mínima</li>
                                <li class="list-group-item"><code>{edad_max}</code> - Edad máxima</li>
                                <li class="list-group-item"><code>{precio_min}</code> - Precio mínimo</li>
                                <li class="list-group-item"><code>{precio_max}</code> - Precio máximo</li>
                                <li class="list-group-item"><code>{atributos}</code> - Lista de atributos seleccionados</li>
                                <li class="list-group-item"><code>{servicios}</code> - Lista de servicios seleccionados</li>
                            </ul>
                            <small class="text-muted mt-2" style="color: #333;">* Todas las variables están disponibles en cualquier tipo de template. Si una variable no tiene valor, será omitida automáticamente del texto.</small>
                        </div>

                        <div class="mb-3">
                            <label for="multiple_template" class="form-label">Descripción para 2-4 filtros</label>
                            <textarea class="form-control custom-textarea" id="multiple_template" name="description_template" rows="4">{{ $templates['multiple'] ?? 'Encuentra escorts {nacionalidad} de {edad_min} a {edad_max} años con precios desde ${precio_min} hasta ${precio_max} en {ciudad}.' }}</textarea>
                        </div>

                        <button type="submit" class="btn custom-button">Guardar template de 2-4 filtros</button>
                    </form>
                </div>

                <!-- Template para más de 4 filtros -->
                <div class="tab-pane fade" id="complex" role="tabpanel">
                    <form action="{{ route('seo.templates.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tipo" value="complex">

                        <div class="mb-3">
                            <label class="form-label">Variables disponibles:</label>
                            <ul class="list-group custom-list">
                                <li class="list-group-item"><code>{ciudad}</code> - Nombre de la ciudad</li>
                                <li class="list-group-item"><code>{nacionalidad}</code> - Nacionalidad seleccionada</li>
                                <li class="list-group-item"><code>{edad_min}</code> - Edad mínima</li>
                                <li class="list-group-item"><code>{edad_max}</code> - Edad máxima</li>
                                <li class="list-group-item"><code>{precio_min}</code> - Precio mínimo</li>
                                <li class="list-group-item"><code>{precio_max}</code> - Precio máximo</li>
                                <li class="list-group-item"><code>{atributos}</code> - Lista de atributos seleccionados</li>
                                <li class="list-group-item"><code>{servicios}</code> - Lista de servicios seleccionados</li>
                            </ul>
                            <small class="text-muted mt-2">* Todas las variables están disponibles en cualquier tipo de template. Si una variable no tiene valor, será omitida automáticamente del texto.</small>
                        </div>

                        <div class="mb-3">
                            <label for="complex_template" class="form-label">Descripción para más de 4 filtros</label>
                            <textarea class="form-control custom-textarea" id="complex_template" name="description_template" rows="4">{{ $templates['complex'] ?? 'Descubre escorts {nacionalidad} en {ciudad} que cumplen con tus preferencias específicas. Contamos con una amplia selección de servicios y características como {atributos} y servicios de {servicios}.' }}</textarea>
                        </div>

                        <button type="submit" class="btn custom-button">Guardar template complejo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} OnlyEscorts chile. Todos los derechos reservados.</p>
</footer>

<style>
    /* Estilos generales */
    body {
        background-color: #1e1e1e;
        color: #888;
    }

    /* Navbar */
    .navbar-admin {
        background-color: #1e1e1e;
        padding: 1rem;
        border-bottom: 1px solid #333;
    }

    .nav-links-admin {
        display: flex;
        gap: 20px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-links-admin a {
        color: #888;
        text-decoration: none;
        transition: color 0.3s;
    }

    .nav-links-admin a:hover {
        color: #fff;
    }

    /* Contenedor principal */
    .main-content {
        padding: 2rem;
    }

    /* Card personalizada */
    .custom-card {
        background-color: #2d2d2d;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        width: 100%;
    }

    .card-header {
        background-color: #333;
        border-bottom: 1px solid #444;
        color: #888;
    }

    /* Tabs personalizados */
    .custom-tabs {
        border-bottom: 1px solid #444;
    }

    .custom-tabs .nav-link {
        color: #888;
        border: none;
        padding: 0.75rem 1.5rem;
    }

    .custom-tabs .nav-link:hover {
        color: #fff;
        border: none;
        background-color: #333;
    }

    .custom-tabs .nav-link.active {
        color: #fff;
        background-color: #333;
        border: none;
    }

    /* Lista personalizada */
    .custom-list {
        background-color: #2d2d2d;
        border: 1px solid #444;
    }

    .custom-list .list-group-item {
        background-color: #2d2d2d;
        border-color: #444;
        color: #888;
    }

    .custom-list code {
        background-color: #333;
        color: #f1033d;
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Textarea personalizado */
    .custom-textarea {
        background-color: #333;
        border: 1px solid #444;
        color: #888;
        resize: vertical;
    }

    .custom-textarea:focus {
        background-color: #383838;
        border-color: #555;
        color: #fff;
        box-shadow: none;
    }

    /* Botón personalizado */
    .custom-button {
        background-color: #f1033d;
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        transition: all 0.3s;
    }

    .custom-button:hover {
        background-color: #d00235;
        transform: translateY(-1px);
    }

    /* Botón de logout */
    .logout-button {
        background: none;
        border: none;
        padding: 0;
        color: #888;
        font: inherit;
        cursor: pointer;
        text-decoration: none;
    }

    .logout-button:hover {
        color: #fff;
    }

    /* Labels */
    .form-label {
        color: #888;
        margin-bottom: 0.5rem;
    }

    /* Footer */
    .footer-admin {
        background-color: #1e1e1e;
        color: #888;
        text-align: center;
        padding: 1rem;
        border-top: 1px solid #333;
        margin-top: 2rem;
    }

    /* Mensajes de error y éxito */
    .invalid-feedback {
        color: #f1033d;
    }

    .alert {
        background-color: #2d2d2d;
        border: 1px solid #444;
        color: #888;
    }

    /* User info */
    .user-info-admin {
        color: #888;
    }

    .text-muted mt-2 {
        color: #888;
    }

    /* Logo */
    .logo1 {
        max-height: 50px;
    }
</style>
@endsection