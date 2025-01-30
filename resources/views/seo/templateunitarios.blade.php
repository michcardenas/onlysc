@extends('layouts.app_login')

@section('content')
<style>
    .container-fluid {
        width: 100%;
        margin: 0 auto;
        padding: 2rem;
    }

    .card.custom-card {
        min-width: 1200px;
        background-color: rgba(26, 26, 26, 0.95);
        margin: 20px auto;
    }

    .nav-link,
    .custom-button,
    a {
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #ffffff;
    }

    .custom-button {
        background-color: #e00037;
        border: none;
    }

    .custom-button:hover {
        background-color: #ff1a1a;
        color: #ffffff;
    }

    .template-group {
        margin-bottom: 1.5rem;
    }

    @media (max-width: 992px) {
        .card.custom-card {
            min-width: 100%;
        }
    }
</style>

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
            @if($usuarioAutenticado->rol == 1)
            <li><a href="{{ route('panel_control') }}">Chicas</a></li>
            <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
            @endif
            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" style="background: none; border: none; padding: 0; color: white; font: inherit; cursor: pointer; text-decoration: none;">
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
        <div class="user-info-admin">
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }}
                @if($usuarioAutenticado->rol == 1)
                (Administrador)
                @endif
            </p>
        </div>
    </nav>
</header>

<div class="container mt-4">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card custom-card">
        <div class="card-header">
            <ul class="nav nav-tabs" id="seoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios" type="button" role="tab">
                        Servicios
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="atributos-tab" data-bs-toggle="tab" data-bs-target="#atributos" type="button" role="tab">
                        Atributos
                    </button>
                </li>
                <!-- En la sección de nav-tabs, después de atributos-tab -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nacionalidades-tab" data-bs-toggle="tab" data-bs-target="#nacionalidades" type="button" role="tab">
                        Nacionalidades
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sectores-tab" data-bs-toggle="tab" data-bs-target="#sectores" type="button" role="tab">
                        Sectores
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="seoTabsContent">
                <!-- Tab Servicios -->
                <div class="tab-pane fade show active" id="servicios" role="tabpanel">
                    <div class="form-group mb-4">
                        <label for="servicio_select" class="text-white">Seleccionar Servicio</label>
                        <select class="form-control" id="servicio_select" name="servicio_id">
                            <option value="">Seleccione un servicio...</option>
                            @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{ route('seo.servicios.update') }}" method="POST" id="servicioForm">
                        @csrf
                        <input type="hidden" name="servicio_id" id="servicio_id_input">

                        <!-- Meta Title -->
                        <div class="form-group template-group">
                            <label for="meta_title" class="text-white">Título Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_title_servicio"
                                name="meta_title"
                                placeholder="Ingrese el título meta"
                                required>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group template-group">
                            <label for="meta_description" class="text-white">Descripción Meta</label>
                            <textarea class="form-control custom-textarea"
                                id="meta_description_servicio"
                                name="meta_description"
                                rows="3"
                                placeholder="Ingrese la descripción meta"
                                required></textarea>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="form-group template-group">
                            <label for="meta_keywords" class="text-white">Palabras Clave Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_keywords_servicio"
                                name="meta_keywords"
                                placeholder="Ingrese las palabras clave separadas por comas">
                        </div>

                        <!-- Canonical URL -->
                        <div class="form-group template-group">
                            <label for="canonical_url" class="text-white">URL Canónica</label>
                            <input type="url"
                                class="form-control"
                                id="canonical_url_servicio"
                                name="canonical_url"
                                placeholder="Ingrese la URL canónica (opcional)">
                        </div>

                        <!-- Meta Robots -->
                        <div class="form-group template-group">
                            <label for="meta_robots" class="text-white">Meta Robots</label>
                            <select class="form-control" id="meta_robots_servicio" name="meta_robots" required>
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, nofollow">No Index, No Follow</option>
                                <option value="index, nofollow">Index, No Follow</option>
                                <option value="noindex, follow">No Index, Follow</option>
                            </select>
                        </div>

                        <!-- Heading H1 -->
                        <div class="form-group template-group">
                            <label for="heading_h1" class="text-white">Encabezado H1</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h1_servicio"
                                name="heading_h1"
                                placeholder="Ingrese el encabezado H1">
                        </div>

                        <!-- Heading H2 -->
                        <div class="form-group template-group">
                            <label for="heading_h2" class="text-white">Encabezado H2</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h2_servicio"
                                name="heading_h2"
                                placeholder="Ingrese el encabezado H2">
                        </div>

                        <!-- Additional Text -->
                        <div class="form-group template-group">
                            <label for="additional_text" class="text-white">Texto Adicional</label>
                            <textarea class="form-control"
                                id="additional_text_servicio"
                                name="additional_text"
                                rows="4"
                                placeholder="Ingrese texto adicional"></textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn custom-button">Guardar Cambios</button>
                            <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
                        </div>
                    </form>
                </div>

                <!-- Tab Atributos -->
                <div class="tab-pane fade" id="atributos" role="tabpanel">
                    <div class="form-group mb-4">
                        <label for="atributo_select" class="text-white">Seleccionar Atributo</label>
                        <select class="form-control" id="atributo_select" name="atributo_id">
                            <option value="">Seleccione un atributo...</option>
                            @foreach($atributos as $atributo)
                            <option value="{{ $atributo->id }}">{{ $atributo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{ route('seo.atributos.update') }}" method="POST" id="atributoForm">
                        @csrf
                        <input type="hidden" name="atributo_id" id="atributo_id_input">

                        <!-- Meta Title -->
                        <div class="form-group template-group">
                            <label for="meta_title" class="text-white">Título Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_title_atributo"
                                name="meta_title"
                                placeholder="Ingrese el título meta"
                                required>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group template-group">
                            <label for="meta_description" class="text-white">Descripción Meta</label>
                            <textarea class="form-control custom-textarea"
                                id="meta_description_atributo"
                                name="meta_description"
                                rows="3"
                                placeholder="Ingrese la descripción meta"
                                required></textarea>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="form-group template-group">
                            <label for="meta_keywords" class="text-white">Palabras Clave Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_keywords_atributo"
                                name="meta_keywords"
                                placeholder="Ingrese las palabras clave separadas por comas">
                        </div>

                        <!-- Canonical URL -->
                        <div class="form-group template-group">
                            <label for="canonical_url" class="text-white">URL Canónica</label>
                            <input type="url"
                                class="form-control"
                                id="canonical_url_atributo"
                                name="canonical_url"
                                placeholder="Ingrese la URL canónica (opcional)">
                        </div>

                        <!-- Meta Robots -->
                        <div class="form-group template-group">
                            <label for="meta_robots" class="text-white">Meta Robots</label>
                            <select class="form-control" id="meta_robots_atributo" name="meta_robots" required>
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, nofollow">No Index, No Follow</option>
                                <option value="index, nofollow">Index, No Follow</option>
                                <option value="noindex, follow">No Index, Follow</option>
                            </select>
                        </div>

                        <!-- Heading H1 -->
                        <div class="form-group template-group">
                            <label for="heading_h1" class="text-white">Encabezado H1</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h1_atributo"
                                name="heading_h1"
                                placeholder="Ingrese el encabezado H1">
                        </div>

                        <!-- Heading H2 -->
                        <div class="form-group template-group">
                            <label for="heading_h2" class="text-white">Encabezado H2</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h2_atributo"
                                name="heading_h2"
                                placeholder="Ingrese el encabezado H2">
                        </div>

                        <!-- Additional Text -->
                        <div class="form-group template-group">
                            <label for="additional_text" class="text-white">Texto Adicional</label>
                            <textarea class="form-control"
                                id="additional_text_atributo"
                                name="additional_text"
                                rows="4"
                                placeholder="Ingrese texto adicional"></textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn custom-button">Guardar Cambios</button>
                            <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="nacionalidades" role="tabpanel">
                    <div class="form-group mb-4">
                        <label for="nacionalidad_select" class="text-white">Seleccionar Nacionalidad</label>
                        <select class="form-control" id="nacionalidad_select" name="nacionalidad_id">
                            <option value="">Seleccione una nacionalidad...</option>
                            @foreach($nacionalidades as $nacionalidad)
                            <option value="{{ $nacionalidad->id }}">{{ $nacionalidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{ route('seo.nacionalidades.update') }}" method="POST" id="nacionalidadForm">
                        @csrf
                        <input type="hidden" name="nacionalidad_id" id="nacionalidad_id_input">

                        <!-- Meta Title -->
                        <div class="form-group template-group">
                            <label for="meta_title" class="text-white">Título Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_title_nacionalidad"
                                name="meta_title"
                                placeholder="Ingrese el título meta"
                                required>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group template-group">
                            <label for="meta_description" class="text-white">Descripción Meta</label>
                            <textarea class="form-control custom-textarea"
                                id="meta_description_nacionalidad"
                                name="meta_description"
                                rows="3"
                                placeholder="Ingrese la descripción meta"
                                required></textarea>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="form-group template-group">
                            <label for="meta_keywords" class="text-white">Palabras Clave Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_keywords_nacionalidad"
                                name="meta_keywords"
                                placeholder="Ingrese las palabras clave separadas por comas">
                        </div>

                        <!-- Canonical URL -->
                        <div class="form-group template-group">
                            <label for="canonical_url" class="text-white">URL Canónica</label>
                            <input type="url"
                                class="form-control"
                                id="canonical_url_nacionalidad"
                                name="canonical_url"
                                placeholder="Ingrese la URL canónica (opcional)">
                        </div>

                        <!-- Meta Robots -->
                        <div class="form-group template-group">
                            <label for="meta_robots" class="text-white">Meta Robots</label>
                            <select class="form-control" id="meta_robots_nacionalidad" name="meta_robots" required>
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, nofollow">No Index, No Follow</option>
                                <option value="index, nofollow">Index, No Follow</option>
                                <option value="noindex, follow">No Index, Follow</option>
                            </select>
                        </div>

                        <!-- Heading H1 -->
                        <div class="form-group template-group">
                            <label for="heading_h1" class="text-white">Encabezado H1</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h1_nacionalidad"
                                name="heading_h1"
                                placeholder="Ingrese el encabezado H1">
                        </div>

                        <!-- Heading H2 -->
                        <div class="form-group template-group">
                            <label for="heading_h2" class="text-white">Encabezado H2</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h2_nacionalidad"
                                name="heading_h2"
                                placeholder="Ingrese el encabezado H2">
                        </div>

                        <!-- Additional Text -->
                        <div class="form-group template-group">
                            <label for="additional_text" class="text-white">Texto Adicional</label>
                            <textarea class="form-control"
                                id="additional_text_nacionalidad"
                                name="additional_text"
                                rows="4"
                                placeholder="Ingrese texto adicional"></textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn custom-button">Guardar Cambios</button>
                            <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
                        </div>
                    </form>
                </div>
                <!-- En la sección de tab-content, después del div de nacionalidades -->
                <!-- Tab Sectores -->
                <div class="tab-pane fade" id="sectores" role="tabpanel">
                    <div class="form-group mb-4">
                        <label for="sector_select" class="text-white">Seleccionar Sector</label>
                        <select class="form-control" id="sector_select" name="sector_id">
                            <option value="">Seleccione un sector...</option>
                            @foreach($sectores as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{ route('seo.sectores.update') }}" method="POST" id="sectorForm">
                        @csrf
                        <input type="hidden" name="sector_id" id="sector_id_input">

                        <!-- Meta Title -->
                        <div class="form-group template-group">
                            <label for="meta_title" class="text-white">Título Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_title_sector"
                                name="meta_title"
                                placeholder="Ingrese el título meta"
                                required>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group template-group">
                            <label for="meta_description" class="text-white">Descripción Meta</label>
                            <textarea class="form-control custom-textarea"
                                id="meta_description_sector"
                                name="meta_description"
                                rows="3"
                                placeholder="Ingrese la descripción meta"
                                required></textarea>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="form-group template-group">
                            <label for="meta_keywords" class="text-white">Palabras Clave Meta</label>
                            <input type="text"
                                class="form-control"
                                id="meta_keywords_sector"
                                name="meta_keywords"
                                placeholder="Ingrese las palabras clave separadas por comas">
                        </div>

                        <!-- Canonical URL -->
                        <div class="form-group template-group">
                            <label for="canonical_url" class="text-white">URL Canónica</label>
                            <input type="url"
                                class="form-control"
                                id="canonical_url_sector"
                                name="canonical_url"
                                placeholder="Ingrese la URL canónica (opcional)">
                        </div>

                        <!-- Meta Robots -->
                        <div class="form-group template-group">
                            <label for="meta_robots" class="text-white">Meta Robots</label>
                            <select class="form-control" id="meta_robots_sector" name="meta_robots" required>
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, nofollow">No Index, No Follow</option>
                                <option value="index, nofollow">Index, No Follow</option>
                                <option value="noindex, follow">No Index, Follow</option>
                            </select>
                        </div>

                        <!-- Heading H1 -->
                        <div class="form-group template-group">
                            <label for="heading_h1" class="text-white">Encabezado H1</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h1_sector"
                                name="heading_h1"
                                placeholder="Ingrese el encabezado H1">
                        </div>

                        <!-- Heading H2 -->
                        <div class="form-group template-group">
                            <label for="heading_h2" class="text-white">Encabezado H2</label>
                            <input type="text"
                                class="form-control"
                                id="heading_h2_sector"
                                name="heading_h2"
                                placeholder="Ingrese el encabezado H2">
                        </div>

                        <!-- Additional Text -->
                        <div class="form-group template-group">
                            <label for="additional_text" class="text-white">Texto Adicional</label>
                            <textarea class="form-control"
                                id="additional_text_sector"
                                name="additional_text"
                                rows="4"
                                placeholder="Ingrese texto adicional"></textarea>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn custom-button">Guardar Cambios</button>
                            <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection