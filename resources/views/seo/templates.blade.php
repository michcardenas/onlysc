@extends('layouts.app_login')

@section('content')
<style>
    /* Estilos generales */
    .template-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .template-group:last-child {
        margin-bottom: 0;
    }

    .tinymce-editor {
        display: block;
        width: 100%;
        background-color: #1a1a1a;
        color: white !important;
    }

    .preview-section {
        margin-top: 0.5rem;
    }

    /* Estilos para la vista previa */
    .preview-content {
        min-height: 50px;
        max-height: 150px;
        overflow-y: auto;
        padding: 0.75rem;
        border: 1px solid #333;
        border-radius: 0.25rem;
        background-color: #1a1a1a;
    }

    .preview-content p {
        margin: 0;
        padding: 0;

    }

    .preview-content strong {
        font-weight: bold;
    }

    .preview-content em {
        font-style: italic;
    }

    .preview-content ul,
    .preview-content ol {
        margin-left: 20px;
    }

    .preview-content a {
        color: #e00037;
        text-decoration: underline;
    }

    /* Asegurarse de que los estilos del TinyMCE se apliquen */
    .preview-content [style] {
        all: revert;
    }

    /* Control específico para el espacio entre pestañas */
    .tab-content>.tab-pane {
        display: none;
    }

    .tab-content>.active {
        display: block;
    }

    /* Ajustes TinyMCE */
    .tox-tinymce {
        margin-bottom: 0.5rem !important;
        min-height: 150px !important;
        max-height: 300px !important;
        border: 1px solid #333 !important;
        background-color: #1a1a1a !important;
    }

    .tox .tox-toolbar {
        background-color: #2d2d2d !important;
        border-bottom: 1px solid #333 !important;
    }

    .tox .tox-edit-area {
        background-color: #1a1a1a !important;
    }

    /* Elimina espacios innecesarios en los contenedores */
    #templateTabsContent {
        margin-top: 1rem;
    }

    .custom-tabs {
        border-bottom: 1px solid #333;
    }

    /* Estilos para las pestañas */
    .nav-tabs .nav-link {
        margin-bottom: -1px;
        background-color: #2d2d2d;
        border: 1px solid #333;
        color: #fff !important;
    }

    .nav-tabs .nav-link.active {
        background-color: #1a1a1a;
        border-color: #333 #333 #1a1a1a;
        color: #fff !important;
    }

    .nav-tabs .nav-link:hover {
        border-color: #444;
        background-color: #333;
    }

    .card.custom-card {
        background-color: #1a1a1a;
        border: 1px solid #333;
        border-radius: 0.5rem;
        width: 100%;
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


    .card-header {
        background-color: #2d2d2d !important;
        border-bottom: 1px solid #333;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    /* Formularios y botones */
    .form-control,
    .custom-textarea {
        background-color: #2d2d2d;
        border: 1px solid #333;
        color: white;
    }

    .form-control:focus,
    .custom-textarea:focus {
        background-color: #2d2d2d;
        border-color: #444;
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(66, 70, 73, 0.5);
    }

    .custom-button {
        background-color: #3d3d3d;
        border: 1px solid #444;
        color: white;
    }

    .custom-button:hover {
        background-color: #444;
        border-color: #555;
    }

    /* Lista de variables */
    .list-group.custom-list .list-group-item {
        background-color: #2d2d2d;
        border: 1px solid #333;
        color: white;
    }

    .list-group.custom-list code {
        background-color: #1a1a1a;
        color: #e00037;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }

    /* Alertas */
    .alert-success {
        background-color: #1e4620;
        border-color: #255827;
        color: #75b798;
    }

    .alert-danger {
        background-color: #461e1e;
        border-color: #582525;
        color: #b77575;
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
            <li><a href="{{ route('home') }}" class="text-white">Inicio</a></li>
            <li><a href="{{ route('admin.profile') }}" class="text-white">Perfil</a></li>
            <li><a href="{{ route('admin.perfiles') }}" class="text-white">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}" class="text-white">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}" class="text-white">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}" class="text-white">Blog</a></li>
            <li><a href="{{ route('seo') }}" class="text-white">SEO</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="logout-button text-white">
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
        <div class="user-info-admin text-white">
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
        <div class="card-header bg-dark">
            <h3 class="card-title text-white">Templates SEO</h3>
        </div>
        <div class="card-body">
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

            <!-- Selector de Ciudad Global -->
            <div class="mb-4">
                <label for="global_ciudad" class="form-label text-white">Seleccionar Ciudad</label>
                <select class="form-control custom-textarea" id="global_ciudad" name="global_ciudad">
                    <option value="">Seleccione una ciudad</option>
                    @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pestañas -->
            <ul class="nav nav-tabs custom-tabs mb-3" id="templateTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-white" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        Templates Generales
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-white" id="unitary-tab" data-bs-toggle="tab" data-bs-target="#unitary" type="button" role="tab" aria-controls="unitary" aria-selected="false">
                        Templates Unitarios
                    </button>
                </li>
            </ul>

            <form id="templatesForm">
                @csrf
                <input type="hidden" name="ciudad_id" id="selected_ciudad_id">

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="templateTabsContent">
                    <!-- Templates Generales -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        @foreach(['single' => '1 Filtro', 'multiple' => '2-4 Filtros', 'complex' => '+4 Filtros'] as $tipo => $titulo)
                        <div class="template-group" data-tipo="{{ $tipo }}">
                            <label for="{{ $tipo }}_title" class="form-label text-white">Título para {{ $titulo }}</label>
                            <textarea
                                class="tinymce-editor"
                                id="{{ $tipo }}_title"
                                data-tipo="{{ $tipo }}_title"
                                data-is-title="true">{{ isset($templates[$tipo][$ciudades->first()->id]['titulo']) ? $templates[$tipo][$ciudades->first()->id]['titulo'] : $defaultTemplates[$tipo]['titulo'] }}</textarea>

                            <label for="{{ $tipo }}_template" class="form-label text-white">Template para {{ $titulo }}</label>
                            <textarea
                                class="tinymce-editor"
                                id="{{ $tipo }}_template"
                                data-tipo="{{ $tipo }}"
                                data-is-description="true">{{ isset($templates[$tipo][$ciudades->first()->id]['description_template']) ? $templates[$tipo][$ciudades->first()->id]['description_template'] : $defaultTemplates[$tipo]['description_template'] }}</textarea>
                            <div class="preview-section mt-2">
                                <label class="text-white">Vista previa:</label>
                                <div id="{{ $tipo }}_preview" class="preview-content bg-light"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="tab-pane fade" id="unitary" role="tabpanel" aria-labelledby="unitary-tab">
                        @foreach(['ciudad', 'nacionalidad', 'edad', 'precio', 'atributos', 'servicios', 'disponible', 'resena', 'categorias', 'sector'] as $filtro)
                        <div class="template-group" data-tipo="{{ $filtro }}">
                            <label for="{{ $filtro }}_title" class="form-label text-white">Título para {{ ucfirst($filtro) }}</label>
                            <textarea
                                class="tinymce-editor"
                                id="{{ $filtro }}_title"
                                data-tipo="{{ $filtro }}_title"
                                data-is-title="true">{{ isset($templates['filtros'][$filtro][$ciudades->first()->id]['titulo']) ? $templates['filtros'][$filtro][$ciudades->first()->id]['titulo'] : $defaultTemplates[$filtro]['titulo'] }}</textarea>

                            <label for="{{ $filtro }}_template" class="form-label text-white">Template para {{ ucfirst($filtro) }}</label>
                            <textarea
                                class="tinymce-editor"
                                id="{{ $filtro }}_template"
                                name="description_template"
                                data-tipo="{{ $filtro }}">{{ isset($templates['filtros'][$filtro][$ciudades->first()->id]['description_template']) ? $templates['filtros'][$filtro][$ciudades->first()->id]['description_template'] : $defaultTemplates[$filtro]['description_template'] }}</textarea>
                            <div class="preview-section mt-2">
                                <label class="text-white">Vista previa:</label>
                                <div id="{{ $filtro }}_preview" class="preview-content bg-light"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botón Guardar -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn custom-button text-white">Guardar Todos los Templates</button>
                </div>
            </form>

            <!-- Variables disponibles -->
            <div class="mt-4">
                <h4 class="mb-3 text-white">Variables disponibles:</h4>
                <ul class="list-group custom-list">
                    <li class="list-group-item"><code>{ciudad}</code> - Nombre de la ciudad</li>
                    <li class="list-group-item"><code>{sector}</code> - Sector específico (si aplica)</li>
                    <li class="list-group-item"><code>{nacionalidad}</code> - Nacionalidad seleccionada</li>
                    <li class="list-group-item"><code>{edad_min}</code> - Edad mínima</li>
                    <li class="list-group-item"><code>{edad_max}</code> - Edad máxima</li>
                    <li class="list-group-item"><code>{precio_min}</code> - Precio mínimo</li>
                    <li class="list-group-item"><code>{precio_max}</code> - Precio máximo</li>
                    <li class="list-group-item"><code>{atributos}</code> - Lista de atributos seleccionados</li>
                    <li class="list-group-item"><code>{servicios}</code> - Lista de servicios seleccionados</li>
                    <li class="list-group-item"><code>{disponible}</code> - Estado de disponibilidad</li>
                    <li class="list-group-item"><code>{resena}</code> - Estado de reseñas</li>
                    <li class="list-group-item"><code>{categorias}</code> - Categorías seleccionadas</li>
                </ul>
                <small class="text-white mt-2 d-block">* Todas las variables están disponibles en cualquier tipo de template. Si una variable no tiene valor, será omitida automáticamente del texto.</small>
            </div>
        </div>
    </div>
</div>

<footer class="footer-admin">
    <p class="text-white">&copy; {{ date('Y') }} OnlyEscorts Chile. Todos los derechos reservados.</p>
</footer>
@endsection