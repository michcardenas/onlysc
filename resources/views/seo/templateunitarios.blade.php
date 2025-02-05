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

    <!-- Ciudad Select - Global Filter -->
    <div class="form-group mb-4">
        <label for="ciudad_select" class="text-white">Filtrar por Ciudad</label>
        <select class="form-control" id="ciudad_select" name="ciudad_id">
            <option value="">Seleccione una ciudad...</option>
            @foreach($ciudades as $ciudad)
            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
            @endforeach
        </select>
    </div>

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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="disponibilidad-tab" data-bs-toggle="tab" data-bs-target="#disponibilidad" type="button" role="tab">
                        Disponibilidad
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="resenas-tab" data-bs-toggle="tab" data-bs-target="#resenas" type="button" role="tab">
                        Reseñas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="categorias-tab" data-bs-toggle="tab" data-bs-target="#categorias" type="button" role="tab">
                        Categorías
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
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'servicio'])
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
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'atributo'])
                    </form>
                </div>

                <!-- Tab Nacionalidades -->
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
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'nacionalidad'])
                    </form>
                </div>

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
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'sector'])
                    </form>
                </div>

                <!-- Tab Disponibilidad -->
                <div class="tab-pane fade" id="disponibilidad" role="tabpanel">
                    <form action="{{ route('seo.disponibilidad.update') }}" method="POST" id="disponibilidadForm">
                        @csrf
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'disponibilidad'])
                    </form>
                </div>

                <!-- Tab Reseñas -->
                <div class="tab-pane fade" id="resenas" role="tabpanel">
                    <form action="{{ route('seo.resenas.update') }}" method="POST" id="resenasForm">
                        @csrf
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'resenas'])
                    </form>
                </div>

                <!-- Tab Categorías -->
                <div class="tab-pane fade" id="categorias" role="tabpanel">
                    <div class="form-group mb-4">
                        <label for="categoria_select" class="text-white">Seleccionar Categoría</label>
                        <select class="form-control" id="categoria_select" name="categoria_id">
                            <option value="">Seleccione una categoría...</option>
                            <option value="vip">VIP</option>
                            <option value="premium">Premium</option>
                            <option value="de_lujo">De Lujo</option>
                            <option value="under">Under</option>
                            <option value="masajes">Masajes</option>
                        </select>
                    </div>

                    <form action="{{ route('seo.categorias.update') }}" method="POST" id="categoriaForm">
                        @csrf
                        <input type="hidden" name="categoria_id" id="categoria_id_input">
                        <input type="hidden" name="ciudad_id" class="ciudad-input">

                        @include('admin.partials.seo-fields', ['prefix' => 'categoria'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
