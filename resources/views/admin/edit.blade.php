@extends('layouts.app_login')

@section('content')
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

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('panel_control') }}">chicas</a></li>

            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>
            <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
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
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
        </div>
    </nav>
</header>

<main class="main-admin">
    <section class="form-section">
        <h2 style="color:white;">Editar Usuario - {{ $usuario->nombre }}</h2>

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
                <select name="categorias" id="categorias" class="form-control" required>
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
                <select name="ubicacion" id="ubicacion" class="form-control" required>
                    <option value="">Seleccione una ciudad</option>
                    @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->nombre }}"
                        {{ old('ubicacion', $usuario->ubicacion) == $ciudad->nombre ? 'selected' : '' }}>
                        {{ $ciudad->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-3">
                <a href="{{ route('ciudades.index') }}" class="btn btn-primary">Agregar ciudad</a>
            </div>
            <div class="form-group">
                <label for="nacionalidad">Nacionalidad</label>
                <input type="text" name="nacionalidad" value="{{ old('nacionalidad', $usuario->nacionalidad) }}" required>
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

            <!-- Checkboxes para los días de la semana -->
            <div class="form-group">
                <label>Días Disponibles</label>
                <div class="admin-checkbox-group">
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                    <div class="admin-checkbox-item">
                        <div class="day-container">
                            <div class="day-checkboxes">
                                <input type="checkbox"
                                    id="admin-dia-{{ $dia }}"
                                    name="dias_disponibles[]"
                                    value="{{ $dia }}"
                                    {{ in_array($dia, $diasDisponibles ?? []) ? 'checked' : '' }}>
                                <label for="admin-dia-{{ $dia }}">{{ $dia }}</label>
                                <input type="checkbox"
                                    id="fulltime-{{ $dia }}"
                                    name="fulltime[{{ $dia }}]"
                                    value="1"
                                    {{ isset($horarios[$dia]) && $horarios[$dia]['desde'] === '00:00' && $horarios[$dia]['hasta'] === '23:59' ? 'checked' : '' }}>
                                <label for="fulltime-{{ $dia }}">Full Time</label>
                            </div>
                        </div>
                        <div class="time-inputs">
                            <div class="time-input-group">
                                <input type="time"
                                    id="desde-{{ $dia }}"
                                    name="horario[{{ $dia }}][desde]"
                                    value="{{ $horarios[$dia]['desde'] ?? '10:00' }}"
                                    class="time-input"
                                    {{ isset($horarios[$dia]) && $horarios[$dia]['desde'] === '00:00' && $horarios[$dia]['hasta'] === '23:59' ? 'disabled' : '' }}>
                                <span class="time-separator">-</span>
                                <input type="time"
                                    id="hasta-{{ $dia }}"
                                    name="horario[{{ $dia }}][hasta]"
                                    value="{{ $horarios[$dia]['hasta'] ?? '02:00' }}"
                                    class="time-input"
                                    {{ isset($horarios[$dia]) && $horarios[$dia]['desde'] === '00:00' && $horarios[$dia]['hasta'] === '23:59' ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="admin-form-group">
                <div class="admin-services-wrapper">
                    <label class="admin-services-label">Servicios <span class="required-asterisk">*</span></label>
                    <div class="publicate-services-grid">
                        @php
                        $serviciosActuales = json_decode($usuario->servicios, true) ?? [];
                        @endphp

                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Anal"
                                {{ in_array('Anal', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Anal</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Atención a domicilio"
                                {{ in_array('Atención a domicilio', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Atención a domicilio</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Atención en hoteles"
                                {{ in_array('Atención en hoteles', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Atención en hoteles</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Baile Erotico"
                                {{ in_array('Baile Erotico', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Baile Erótico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Besos"
                                {{ in_array('Besos', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Besos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Cambio de rol"
                                {{ in_array('Cambio de rol', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Cambio de rol</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Departamento Propio"
                                {{ in_array('Departamento Propio', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Departamento Propio</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Disfraces"
                                {{ in_array('Disfraces', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Disfraces</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Ducha Erotica"
                                {{ in_array('Ducha Erotica', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Ducha Erótica</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Eventos y Cenas"
                                {{ in_array('Eventos y Cenas', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Eventos y Cenas</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Eyaculación Cuerpo"
                                {{ in_array('Eyaculación Cuerpo', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Eyaculación Cuerpo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Eyaculación Facial"
                                {{ in_array('Eyaculación Facial', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Eyaculación Facial</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Hetero"
                                {{ in_array('Hetero', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Hetero</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Juguetes"
                                {{ in_array('Juguetes', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Juguetes</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Lesbico"
                                {{ in_array('Lesbico', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Lésbico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Lluvia dorada"
                                {{ in_array('Lluvia dorada', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Lluvia dorada</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masaje Erotico"
                                {{ in_array('Masaje Erotico', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masaje Erótico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masaje prostatico"
                                {{ in_array('Masaje prostatico', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masaje prostático</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masaje Tantrico"
                                {{ in_array('Masaje Tantrico', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masaje Tántrico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masaje Thai"
                                {{ in_array('Masaje Thai', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masaje Thai</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes con final feliz"
                                {{ in_array('Masajes con final feliz', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes con final feliz</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes desnudos"
                                {{ in_array('Masajes desnudos', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes desnudos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes Eroticos"
                                {{ in_array('Masajes Eroticos', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes Eróticos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes para hombres"
                                {{ in_array('Masajes para hombres', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes para hombres</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes sensitivos"
                                {{ in_array('Masajes sensitivos', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes sensitivos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masajes sexuales"
                                {{ in_array('Masajes sexuales', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masajes sexuales</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Masturbación Rusa"
                                {{ in_array('Masturbación Rusa', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Masturbación Rusa</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Oral Americana"
                                {{ in_array('Oral Americana', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Oral Americana</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Oral con preservativo"
                                {{ in_array('Oral con preservativo', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Oral con preservativo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Oral sin preservativo"
                                {{ in_array('Oral sin preservativo', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Oral sin preservativo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Orgias"
                                {{ in_array('Orgias', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Orgías</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Parejas"
                                {{ in_array('Parejas', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Parejas</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios[]" value="Trio"
                                {{ in_array('Trio', $serviciosActuales) ? 'checked' : '' }}>
                            <span>Trío</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-services-wrapper">
                    <label class="admin-services-label">Servicios Adicionales<span class="required-asterisk">*</span></label>
                    <div class="publicate-services-grid">
                        @php
                        $serviciosAdicionalesActuales = json_decode($usuario->servicios_adicionales, true) ?? [];
                        @endphp

                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Anal"
                                {{ in_array('Anal', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Anal</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Atención a domicilio"
                                {{ in_array('Atención a domicilio', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Atención a domicilio</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Atención en hoteles"
                                {{ in_array('Atención en hoteles', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Atención en hoteles</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Baile Erotico"
                                {{ in_array('Baile Erotico', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Baile Erótico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Besos"
                                {{ in_array('Besos', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Besos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Cambio de rol"
                                {{ in_array('Cambio de rol', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Cambio de rol</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Departamento Propio"
                                {{ in_array('Departamento Propio', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Departamento Propio</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Disfraces"
                                {{ in_array('Disfraces', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Disfraces</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Ducha Erotica"
                                {{ in_array('Ducha Erotica', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Ducha Erótica</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Eventos y Cenas"
                                {{ in_array('Eventos y Cenas', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Eventos y Cenas</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Eyaculación Cuerpo"
                                {{ in_array('Eyaculación Cuerpo', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Eyaculación Cuerpo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Eyaculación Facial"
                                {{ in_array('Eyaculación Facial', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Eyaculación Facial</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Hetero"
                                {{ in_array('Hetero', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Hetero</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Juguetes"
                                {{ in_array('Juguetes', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Juguetes</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Lesbico"
                                {{ in_array('Lesbico', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Lésbico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Lluvia dorada"
                                {{ in_array('Lluvia dorada', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Lluvia dorada</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masaje Erotico"
                                {{ in_array('Masaje Erotico', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masaje Erótico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masaje prostatico"
                                {{ in_array('Masaje prostatico', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masaje prostático</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masaje Tantrico"
                                {{ in_array('Masaje Tantrico', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masaje Tántrico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masaje Thai"
                                {{ in_array('Masaje Thai', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masaje Thai</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes con final feliz"
                                {{ in_array('Masajes con final feliz', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes con final feliz</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes desnudos"
                                {{ in_array('Masajes desnudos', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes desnudos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes Eroticos"
                                {{ in_array('Masajes Eroticos', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes Eróticos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes para hombres"
                                {{ in_array('Masajes para hombres', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes para hombres</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes sensitivos"
                                {{ in_array('Masajes sensitivos', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes sensitivos</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masajes sexuales"
                                {{ in_array('Masajes sexuales', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masajes sexuales</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Masturbación Rusa"
                                {{ in_array('Masturbación Rusa', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Masturbación Rusa</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Oral Americana"
                                {{ in_array('Oral Americana', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Oral Americana</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Oral con preservativo"
                                {{ in_array('Oral con preservativo', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Oral con preservativo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Oral sin preservativo"
                                {{ in_array('Oral sin preservativo', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Oral sin preservativo</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Orgias"
                                {{ in_array('Orgias', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Orgías</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Parejas"
                                {{ in_array('Parejas', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Parejas</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="servicios_adicionales[]" value="Trio"
                                {{ in_array('Trio', $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>Trío</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-services-wrapper">
                    <label class="admin-services-label">Atributos<span class="required-asterisk">*</span></label>
                    <div class="publicate-services-grid">
                        @php
                        $atributosActuales = json_decode($usuario->atributos, true) ?? [];
                        @endphp

                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Busto Grande"
                                {{ in_array('Busto Grande', $atributosActuales) ? 'checked' : '' }}>
                            <span>Busto Grande</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Busto Mediano"
                                {{ in_array('Busto Mediano', $atributosActuales) ? 'checked' : '' }}>
                            <span>Busto Mediano</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Busto Pequeño"
                                {{ in_array('Busto Pequeño', $atributosActuales) ? 'checked' : '' }}>
                            <span>Busto Pequeño</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Cara Visible"
                                {{ in_array('Cara Visible', $atributosActuales) ? 'checked' : '' }}>
                            <span>Cara Visible</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Cola Grande"
                                {{ in_array('Cola Grande', $atributosActuales) ? 'checked' : '' }}>
                            <span>Cola Grande</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Cola Mediana"
                                {{ in_array('Cola Mediana', $atributosActuales) ? 'checked' : '' }}>
                            <span>Cola Mediana</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Cola Pequeña"
                                {{ in_array('Cola Pequeña', $atributosActuales) ? 'checked' : '' }}>
                            <span>Cola Pequeña</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Con Video"
                                {{ in_array('Con Video', $atributosActuales) ? 'checked' : '' }}>
                            <span>Con Video</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Contextura Delgada"
                                {{ in_array('Contextura Delgada', $atributosActuales) ? 'checked' : '' }}>
                            <span>Contextura Delgada</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Contextura Grande"
                                {{ in_array('Contextura Grande', $atributosActuales) ? 'checked' : '' }}>
                            <span>Contextura Grande</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Contextura Mediana"
                                {{ in_array('Contextura Mediana', $atributosActuales) ? 'checked' : '' }}>
                            <span>Contextura Mediana</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Depilación Full"
                                {{ in_array('Depilación Full', $atributosActuales) ? 'checked' : '' }}>
                            <span>Depilación Full</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Depto Propio"
                                {{ in_array('Depto Propio', $atributosActuales) ? 'checked' : '' }}>
                            <span>Depto Propio</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="En Promoción"
                                {{ in_array('En Promoción', $atributosActuales) ? 'checked' : '' }}>
                            <span>En Promoción</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="English"
                                {{ in_array('English', $atributosActuales) ? 'checked' : '' }}>
                            <span>English</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Escort Independiente"
                                {{ in_array('Escort Independiente', $atributosActuales) ? 'checked' : '' }}>
                            <span>Escort Independiente</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Español"
                                {{ in_array('Español', $atributosActuales) ? 'checked' : '' }}>
                            <span>Español</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Estatura Alta"
                                {{ in_array('Estatura Alta', $atributosActuales) ? 'checked' : '' }}>
                            <span>Estatura Alta</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Estatura Mediana"
                                {{ in_array('Estatura Mediana', $atributosActuales) ? 'checked' : '' }}>
                            <span>Estatura Mediana</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Estatura Pequeña"
                                {{ in_array('Estatura Pequeña', $atributosActuales) ? 'checked' : '' }}>
                            <span>Estatura Pequeña</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Hentai"
                                {{ in_array('Hentai', $atributosActuales) ? 'checked' : '' }}>
                            <span>Hentai</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Morena"
                                {{ in_array('Morena', $atributosActuales) ? 'checked' : '' }}>
                            <span>Morena</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Mulata"
                                {{ in_array('Mulata', $atributosActuales) ? 'checked' : '' }}>
                            <span>Mulata</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="No fuma"
                                {{ in_array('No fuma', $atributosActuales) ? 'checked' : '' }}>
                            <span>No fuma</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Ojos Claros"
                                {{ in_array('Ojos Claros', $atributosActuales) ? 'checked' : '' }}>
                            <span>Ojos Claros</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Ojos Oscuros"
                                {{ in_array('Ojos Oscuros', $atributosActuales) ? 'checked' : '' }}>
                            <span>Ojos Oscuros</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Peliroja"
                                {{ in_array('Peliroja', $atributosActuales) ? 'checked' : '' }}>
                            <span>Peliroja</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Portugues"
                                {{ in_array('Portugues', $atributosActuales) ? 'checked' : '' }}>
                            <span>Portugues</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Relato Erotico"
                                {{ in_array('Relato Erotico', $atributosActuales) ? 'checked' : '' }}>
                            <span>Relato Erótico</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Rubia"
                                {{ in_array('Rubia', $atributosActuales) ? 'checked' : '' }}>
                            <span>Rubia</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Tatuajes"
                                {{ in_array('Tatuajes', $atributosActuales) ? 'checked' : '' }}>
                            <span>Tatuajes</span>
                        </label>
                        <label class="admin-service-item">
                            <input type="checkbox" name="atributos[]" value="Trigueña"
                                {{ in_array('Trigueña', $atributosActuales) ? 'checked' : '' }}>
                            <span>Trigueña</span>
                        </label>
                    </div>
                </div>
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

            <!-- Nuevo checkbox para Chica del Mes -->
            <div class="admin-service-item">
                <input type="checkbox"
                    name="chica_del_mes"
                    id="chica_del_mes"
                    value="1"
                    {{ old('estadop', $usuario->estadop) == 3 ? 'checked' : '' }}>
                <span>Chica del Mes</span>
            </div>

            <div class="form-group">
                <label for="posicion">Posición</label>
                <input type="number" name="posicion" value="{{ old('posicion', $usuario->posicion) }}" placeholder="Ingrese la posición" min="1" step="1">
            </div>

            <div class="form-group">
                <label for="precio">Precio</label>
                <div class="input-group">
                    <input type="text"
                        name="precio"
                        value="CLP ${{ number_format(old('precio', $usuario->precio), 0, ',', '.') }}"
                        placeholder="Ingrese el precio"
                        class="precio-input">
                </div>
            </div>



            <!-- Botón para abrir el modal -->
            <button type="button" class="btn" style="background-color: #e00037; color: white;" data-bs-toggle="modal" data-bs-target="#fotosModal">
                Gestionar Fotos
            </button>

            <!-- Modal -->
            <div class="modal fade" id="fotosModal" tabindex="-1" aria-labelledby="fotosModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fotosModalLabel">Gestionar Fotos y Videos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Foto Destacada -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Foto/Video Destacado</h6>
                                <div class="form-group">
                                    <input type="file" name="foto_destacada" id="fotoDestacada" accept="image/*,video/*" class="form-control">
                                    <small class="form-text text-muted">Esta será la imagen principal que se mostrará en las tarjetas. Acepta imágenes y videos. Tamaño máximo: 2MB</small>

                                    <div id="previewDestacada" class="mt-2">
                                        @if(!empty(json_decode($usuario->fotos)[0] ?? null))
                                        <div class="destacada-container">
                                            <div class="publicate-preview-item destacada"
                                                data-foto="{{ json_decode($usuario->fotos)[0] }}"
                                                data-user-id="{{ $usuario->id }}"
                                                data-position="{{ $positions[json_decode($usuario->fotos)[0]] ?? 'center' }}">
                                                @php
                                                $extension = pathinfo(json_decode($usuario->fotos)[0], PATHINFO_EXTENSION);
                                                $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg']);
                                                @endphp

                                                @if($isVideo)
                                                <video src="{{ asset('storage/chicas/'.$usuario->id.'/'.json_decode($usuario->fotos)[0]) }}"
                                                    controls
                                                    class="foto-preview-destacada image-{{ $positions[json_decode($usuario->fotos)[0]] ?? 'center' }}"
                                                    onerror="this.src='{{ asset('images/default-video.png') }}'">
                                                </video>
                                                @else
                                                <img src="{{ asset('storage/chicas/'.$usuario->id.'/'.json_decode($usuario->fotos)[0]) }}"
                                                    alt="Foto destacada de {{ $usuario->nombre }}"
                                                    class="foto-preview-destacada image-{{ $positions[json_decode($usuario->fotos)[0]] ?? 'center' }}"
                                                    onerror="this.src='{{ asset('images/default-image.png') }}'">
                                                @endif
                                                <button type="button" class="publicate-remove-button" onclick="removeDestacada(this)">&times;</button>
                                            </div>

                                            <!-- Controles de posición para la foto destacada -->
                                            <div class="destacada-controls">
                                                <span class="destacada-controls-title">Posición de imagen</span>
                                                <button type="button"
                                                    class="position-btn {{ ($positions[json_decode($usuario->fotos)[0]] ?? 'center') == 'left' ? 'active' : '' }}"
                                                    data-position="left">Izquierda</button>
                                                <button type="button"
                                                    class="position-btn {{ ($positions[json_decode($usuario->fotos)[0]] ?? 'center') == 'center' ? 'active' : '' }}"
                                                    data-position="center">Centro</button>
                                                <button type="button"
                                                    class="position-btn {{ ($positions[json_decode($usuario->fotos)[0]] ?? 'center') == 'right' ? 'active' : '' }}"
                                                    data-position="right">Derecha</button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Fotos Adicionales -->
                            <div>
                                <h6 class="border-bottom pb-2">Fotos/Videos Adicionales</h6>
                                <div class="form-group">
                                    <input type="file" name="fotos[]" multiple id="fotosAdicionales" accept="image/*,video/*" class="form-control">
                                    <small class="form-text text-muted">Puede seleccionar múltiples archivos. Tamaño máximo por archivo: 2MB</small>

                                    <div class="fotos-actuales" id="previewContainer" data-user-id="{{ $usuario->id }}">
                                        @if(!empty(json_decode($usuario->fotos)))
                                        @foreach(array_slice(json_decode($usuario->fotos) ?? [], 1) as $foto)
                                        <div class="publicate-preview-item" data-foto="{{ $foto }}" data-user-id="{{ $usuario->id }}">
                                            @php
                                            $extension = pathinfo($foto, PATHINFO_EXTENSION);
                                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg']);
                                            @endphp

                                            @if($isVideo)
                                            <video src="{{ asset('storage/chicas/'.$usuario->id.'/'.$foto) }}"
                                                controls
                                                class="foto-preview"
                                                onerror="this.src='{{ asset('images/default-video.png') }}'">
                                            </video>
                                            @else
                                            <img src="{{ asset('storage/chicas/'.$usuario->id.'/'.$foto) }}"
                                                alt="Foto de {{ $usuario->nombre }}"
                                                class="foto-preview"
                                                onerror="this.src='{{ asset('images/default-image.png') }}'">
                                            @endif
                                            <button type="button" class="publicate-remove-button" onclick="removeExistingPhoto('{{ $foto }}', this)">&times;</button>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
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