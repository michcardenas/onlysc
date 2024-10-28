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
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foro') }}">Foro</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p>Bienvenido, {{ Auth::user()->name }} ({{ Auth::user()->role == 1 ? 'Administrador' : 'Usuario' }})</p>
        </div>
    </nav>
</header>

<main class="main-admin">
    <section class="form-section">
        <h2>Editar Usuario - {{ $usuario->nombre }}</h2>
        
        <form action="{{ route('usuarios_publicate.update', ['id' => $usuario->id]) }}" method="POST" enctype="multipart/form-data" class="form-admin">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="fantasia">Fantasia</label>
                <input type="text" name="fantasia" value="{{ old('fantasia', $usuario->fantasia) }}" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
            </div>
            <div class="form-group">
                <label for="categorias">Categoría</label>
                <select name="categorias" id="categorias" class="form-control">
                    <option value="">Seleccione una categoría</option>
                    <option value="deluxe" {{ old('categorias', $usuario->categorias) == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                    <option value="premium" {{ old('categorias', $usuario->categorias) == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="VIP" {{ old('categorias', $usuario->categorias) == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="masajes" {{ old('categorias', $usuario->categorias) == 'masajes' ? 'selected' : '' }}>Masajes</option>
                </select>
            </div>


            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}">
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" name="ubicacion" value="{{ old('ubicacion', $usuario->ubicacion) }}" required>
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input type="number" name="edad" value="{{ old('edad', $usuario->edad) }}" required min="18" max="100">
            </div>

            <div class="form-group">
                <label for="color_ojos">Color de Ojos</label>
                <input type="text" name="color_ojos" value="{{ old('color_ojos', $usuario->color_ojos) }}">
            </div>

            <div class="form-group">
                <label for="altura">Altura (cm)</label>
                <input type="number" name="altura" value="{{ old('altura', $usuario->altura) }}" min="0" max="300">
            </div>

            <div class="form-group">
                <label for="peso">Peso (kg)</label>
                <input type="number" name="peso" value="{{ old('peso', $usuario->peso) }}" min="0" max="300">
            </div>

            <div class="form-group">
                <label for="disponibilidad">Disponibilidad</label>
                <textarea name="disponibilidad" rows="2">{{ old('disponibilidad', $usuario->disponibilidad) }}</textarea>
            </div>

            <div class="form-group">
                <label for="servicios">Servicios</label>
                <textarea name="servicios" rows="3">{{ old('servicios', json_encode($usuario->servicios)) }}</textarea>
            </div>

            <div class="form-group">
                <label for="servicios_adicionales">Servicios Adicionales</label>
                <textarea name="servicios_adicionales" rows="3">{{ old('servicios_adicionales', json_encode($usuario->servicios_adicionales)) }}</textarea>
            </div>

            <div class="form-group">
                <label for="cuentanos">Cuéntanos sobre ti</label>
                <textarea name="cuentanos" rows="5">{{ old('cuentanos', $usuario->cuentanos) }}</textarea>
            </div>

            <div class="form-group">
                <label for="estadop">Estado</label>
                <select name="estadop">
                    <option value="0" {{ old('estadop', $usuario->estadop) == 0 ? 'selected' : '' }}>Inactivo</option>
                    <option value="1" {{ old('estadop', $usuario->estadop) == 1 ? 'selected' : '' }}>Activo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fotos">Fotos</label>
                <input type="file" name="fotos[]" multiple>
                <div class="fotos-actuales">
                    @foreach(json_decode($usuario->fotos) as $foto)
                    <img src="{{ asset('storage/chicas/'.$usuario->id.'/'.$foto) }}" alt="Foto de {{ $usuario->nombre }}" class="foto-preview">
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection


