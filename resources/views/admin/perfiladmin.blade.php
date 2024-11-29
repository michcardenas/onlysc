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
            @if($usuarioAutenticado->rol == 1)
                <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
                <li><a href="{{ route('foroadmin') }}">Foro</a></li>
                <li><a href="{{ route('blogadmin') }}">Blog</a></li>
                <li><a href="{{ route('seo') }}">SEO</a></li>
            @endif
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} 
                ({{ $usuarioAutenticado->rol == 1 ? 'Administrador' : 
                    ($usuarioAutenticado->rol == 2 ? 'Chica' : 'Usuario') }})
            </p>
        </div>
    </nav>
</header>

<main>
    <section class="perfil-admin-section">
        @if($usuarioAutenticado->rol == 1)
            <h2 style="color:white;">Perfil de Administrador</h2>
        @elseif($usuarioAutenticado->rol == 2)
            <h2 style="color:white;">Perfil de Chica</h2>
        @else
            <h2 style="color:white;">Perfil de Usuario</h2>
        @endif

        <div class="perfil-container">
            <div class="foto-perfil">
                @if($usuario->foto)
                <img src="{{ Storage::url($usuario->foto) }}" alt="Foto de perfil" id="preview-foto">
                @else
                <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil" id="preview-foto">
                @endif
                <form action="{{ route('admin.updatePhoto') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="custom-file-upload">
                        <input type="file" name="foto" id="foto" accept="image/*" onchange="previewImage(this)" class="hidden-file-input">
                        <label for="foto" class="file-upload-button">
                            <i class="fas fa-cloud-upload-alt"></i> Seleccionar Imagen
                        </label>
                        <span class="selected-file-name" id="fileNameDisplay"></span>
                    </div>
                    <button type="submit" class="btn btn-secondary">Actualizar Foto</button>
                </form>
            </div>

            <form action="{{ route('admin.updateProfile') }}" method="POST" class="form-admin">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="{{ $usuario->name }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="{{ $usuario->email }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4">{{ $usuario->descripcion }}</textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-secondary">Guardar Cambios</button>
                    <a href="/password/reset" class="btn btn-secondary">Restablecer Contraseña</a>
                </div>
            </form>
        </div>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Only scorts chile. Todos los derechos reservados.</p>
</footer>

@endsection