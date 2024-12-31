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
            <h3 class="card-title">Templates SEO</h3>
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

            <!-- Pestañas -->
            <!-- Pestañas -->
            <ul class="nav nav-tabs custom-tabs mb-4" id="templateTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">
                        Template 1 Filtro
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="multiple-tab" data-bs-toggle="tab" data-bs-target="#multiple" type="button" role="tab">
                        Template 2-4 Filtros
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="complex-tab" data-bs-toggle="tab" data-bs-target="#complex" type="button" role="tab">
                        Template +4 Filtros
                    </button>
                </li>
            </ul>


<!-- En el contenido de las pestañas -->
<div class="tab-content" id="templateTabsContent">
    @foreach(['single' => '1 Filtro', 'multiple' => '2-4 Filtros', 'complex' => '+4 Filtros'] as $tipo => $titulo)
    <div class="tab-pane fade {{ $tipo === 'single' ? 'show active' : '' }}" id="{{ $tipo }}" role="tabpanel">
        <form action="{{ route('seo.templates.update') }}" method="POST">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">

            <div class="mb-3">
                <label for="{{ $tipo }}_ciudad" class="form-label">Ciudad</label>
                <select class="form-control custom-textarea" id="{{ $tipo }}_ciudad" name="ciudad_id" 
                        onchange="loadTemplate('{{ $tipo }}', this.value)">
                    @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="{{ $tipo }}_template" class="form-label">Template para {{ $titulo }}</label>
                <textarea
                    class="form-control custom-textarea"
                    id="{{ $tipo }}_template"
                    name="description_template"
                    rows="4"
                    onkeyup="updatePreview('{{ $tipo }}')">{{ isset($templates[$tipo][$ciudad->id]) ? $templates[$tipo][$ciudad->id] : $defaultTemplates[$tipo] }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Vista previa:</label>
                <div id="{{ $tipo }}_preview" class="p-3 bg-light border rounded"></div>
            </div>

            <button type="submit" class="btn custom-button">Guardar Template</button>
        </form>
    </div>
    @endforeach
</div>


            <!-- Variables disponibles -->
            <div class="mt-4">
    <h4 class="mb-3">Variables disponibles:</h4>
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
    </ul>
    <small class="text-muted mt-2">* Todas las variables están disponibles en cualquier tipo de template. Si una variable no tiene valor, será omitida automáticamente del texto.</small>
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

    .mt-2 {
        color: #FFF;
    }

    /* Logo */
    .logo1 {
        max-height: 50px;
    }
</style>
@endsection