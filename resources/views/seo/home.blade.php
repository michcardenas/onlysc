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

.preview-content ul, .preview-content ol {
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
.tab-content > .tab-pane {
    display: none;
}

.tab-content > .active {
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
.form-control, .custom-textarea {
    background-color: #2d2d2d;
    border: 1px solid #333;
    color: white;
}

.form-control:focus, .custom-textarea:focus {
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

.alert {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 80%;
    margin-top: 5px;
}
/* Estilo para placeholders en todos los inputs y textareas */
.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7) !important; /* Color blanco con transparencia */
    opacity: 1; /* Firefox */
}

/* Necesario para compatibilidad con diferentes navegadores */
.form-control:-ms-input-placeholder {
    color: rgba(255, 255, 255, 0.7) !important;
}

.form-control::-ms-input-placeholder {
    color: rgba(255, 255, 255, 0.7) !important;
}

/* Estilo para el fondo del input */
.form-control {
    background-color: rgba(0, 0, 0, 0.3) !important; /* Fondo oscuro semi-transparente */
    color: #ffffff !important; /* Color del texto cuando se escribe */
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Estilo cuando el input está enfocado */
.form-control:focus {
    background-color: rgba(0, 0, 0, 0.5) !important;
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
}

/* Estilo para el texto de los select */
select.form-control option {
    background-color: #343a40;
    color: white;
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
<div class="container mt-4">
    <div class="card custom-card">
        <div class="card-header">
            <h2 class="text-white">Editar Etiquetas Meta - Página Home</h2>
        </div>
        <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
        <form action="{{ route('seo.update', ['page' => 'home']) }}" method="POST">
        @csrf
                @method('PUT')
                
                <!-- Meta Title -->
                <div class="form-group template-group">
                    <label for="meta_title" class="text-white">Título Meta</label>
                    <input type="text" 
                           class="form-control" 
                           id="meta_title" 
                           name="meta_title" 
                           placeholder="Ingrese el título meta" 
                           value="{{ old('meta_title', $meta->meta_title ?? '') }}" 
                           required>
                </div>

                <!-- Meta Description -->
                <div class="form-group template-group">
                    <label for="meta_description" class="text-white">Descripción Meta</label>
                    <textarea class="form-control custom-textarea" 
                              id="meta_description" 
                              name="meta_description" 
                              rows="3" 
                              placeholder="Ingrese la descripción meta" 
                              required>{{ old('meta_description', $meta->meta_description ?? '') }}</textarea>
                </div>

                <!-- Meta Keywords -->
                <div class="form-group template-group">
                    <label for="meta_keywords" class="text-white">Palabras Clave Meta</label>
                    <input type="text" 
                           class="form-control" 
                           id="meta_keywords" 
                           name="meta_keywords" 
                           placeholder="Ingrese las palabras clave separadas por comas" 
                           value="{{ old('meta_keywords', $meta->meta_keywords ?? '') }}">
                </div>

                <!-- Canonical URL -->
                <div class="form-group template-group">
                    <label for="canonical_url" class="text-white">URL Canónica</label>
                    <input type="url" 
                           class="form-control" 
                           id="canonical_url" 
                           name="canonical_url" 
                           placeholder="Ingrese la URL canónica (opcional)" 
                           value="{{ old('canonical_url', $meta->canonical_url ?? '') }}">
                </div>

                <!-- Meta Robots -->
                <div class="form-group template-group">
                    <label for="meta_robots" class="text-white">Meta Robots</label>
                    <select class="form-control" id="meta_robots" name="meta_robots" required>
                        <option value="index, follow" {{ old('meta_robots', $meta->meta_robots ?? '') == 'index, follow' ? 'selected' : '' }}>Index, Follow</option>
                        <option value="noindex, nofollow" {{ old('meta_robots', $meta->meta_robots ?? '') == 'noindex, nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                        <option value="index, nofollow" {{ old('meta_robots', $meta->meta_robots ?? '') == 'index, nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                        <option value="noindex, follow" {{ old('meta_robots', $meta->meta_robots ?? '') == 'noindex, follow' ? 'selected' : '' }}>No Index, Follow</option>
                    </select>
                </div>

                <!-- Heading H1 -->
                    <div class="form-group template-group">
                        <label for="heading_h1" class="text-white">Encabezado H1</label>
                        <input type="text" 
                            class="form-control bg-dark text-white @error('heading_h1') is-invalid @enderror" 
                            id="heading_h1" 
                            name="heading_h1" 
                            placeholder="Ingrese el encabezado H1" 
                            value="{{ old('heading_h1', $meta->heading_h1 ?? '') }}">
                        @error('heading_h1')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Heading H2 -->
                    <div class="form-group template-group">
                        <label for="heading_h2" class="text-white">Encabezado H2</label>
                        <input type="text" 
                            class="form-control bg-dark text-white @error('heading_h2') is-invalid @enderror" 
                            id="heading_h2" 
                            name="heading_h2" 
                            placeholder="Ingrese el encabezado H2" 
                            value="{{ old('heading_h2', $meta->heading_h2 ?? '') }}">
                        @error('heading_h2')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Additional Text -->
                    <div class="form-group template-group">
                        <label for="additional_text" class="text-white">Texto Adicional</label>
                        <textarea class="form-control bg-dark text-white @error('additional_text') is-invalid @enderror" 
                                id="additional_text" 
                                name="additional_text" 
                                rows="4" 
                                placeholder="Ingrese texto adicional">{{ old('additional_text', $meta->additional_text ?? '') }}</textarea>
                        @error('additional_text')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                <!-- Botones de Acción -->
                <div class="form-group text-right">
                    <button type="submit" class="btn custom-button">Guardar Cambios</button>
                    <a href="{{ route('home') }}" class="btn custom-button">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>


<footer class="footer-admin">
    <p class="text-white">&copy; {{ date('Y') }} OnlyEscorts Chile. Todos los derechos reservados.</p>
</footer>
@endsection