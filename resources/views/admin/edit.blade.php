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
            {{-- Inicio disponible para todos los roles --}}
            <li><a href="{{ route('home') }}">Inicio</a></li>

            {{-- Perfil disponible para todos los roles --}}
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>

            @if($usuarioAutenticado->rol == 1)
                {{-- Menú completo solo para rol 1 --}}
                <li><a href="{{ route('panel_control') }}">Chicas</a></li>
                <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
                <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
                <li><a href="{{ route('foroadmin') }}">Foro</a></li>
                <li><a href="{{ route('blogadmin') }}">Blog</a></li>
                <li><a href="{{ route('seo') }}">SEO</a></li>
            @endif

            @if($usuarioAutenticado->rol == 3)
                {{-- Foro solo disponible para rol 3 --}}
                <li><a href="{{ route('foroadmin') }}">Foro</a></li>
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
                @elseif($usuarioAutenticado->rol == 2)
                    (Chica)
                @elseif($usuarioAutenticado->rol == 3)
                    (Usuario)
                @endif
            </p>
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
                    <option value="premium" {{ old('categorias', $usuario->categorias) == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="VIP" {{ old('categorias', $usuario->categorias) == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="masajes" {{ old('categorias', $usuario->categorias) == 'masajes' ? 'selected' : '' }}>Masajes</option>
                    <option value="under" {{ old('categorias', $usuario->categorias) == 'under' ? 'selected' : '' }}>Under</option>
                    <option value="de_lujo" {{ old('categorias', $usuario->categorias) == 'de_lujo' ? 'selected' : '' }}>De Lujo</option>
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
                <label>Ubicación en Mapa</label>
                <div class="map-tools mb-2">
                    <input type="text"
                        id="search-input"
                        class="form-control"
                        placeholder="Buscar ubicación..."
                        style="margin-bottom: 10px;">
                </div>
                <div id="map-container" style="height: 400px; width: 50%; border-radius: 8px; margin-bottom: 15px;"></div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="latitud">Latitud</label>
                            <input type="text"
                                name="latitud"
                                id="latitud"
                                class="form-control"
                                readonly
                                value="{{ old('latitud', $usuario->location->latitud ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="longitud">Longitud</label>
                            <input type="text"
                                name="longitud"
                                id="longitud"
                                class="form-control"
                                readonly
                                value="{{ old('longitud', $usuario->location->longitud ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="direccion_mapa">Dirección de Referencia</label>
                            <input type="text"
                                name="direccion_mapa"
                                id="direccion_mapa"
                                class="form-control"
                                value="{{ old('direccion_mapa', $usuario->location->direccion ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="u1">Ubicaciones donde atiendo</label>
                <input type="text" name="u1" value="{{ old('u1', $usuario->u1) }}">
            </div>

            <div class="form-group">
                <label for="u2">Servicios del Sitio</label>
                <input type="text" name="u2" value="{{ old('u1', $usuario->u2) }}">
            </div>


            <div class="form-group">
                <label for="nacionalidad">Nacionalidad</label>
                <select name="nacionalidad" id="nacionalidad" class="form-select" required>
                    <option value="">Seleccionar nacionalidad</option>
                    <option value="argentina" {{ old('nacionalidad', $usuario->nacionalidad) == 'argentina' ? 'selected' : '' }}>Argentina</option>
                    <option value="brasil" {{ old('nacionalidad', $usuario->nacionalidad) == 'brasil' ? 'selected' : '' }}>Brasil</option>
                    <option value="chile" {{ old('nacionalidad', $usuario->nacionalidad) == 'chile' ? 'selected' : '' }}>Chile</option>
                    <option value="colombia" {{ old('nacionalidad', $usuario->nacionalidad) == 'colombia' ? 'selected' : '' }}>Colombia</option>
                    <option value="ecuador" {{ old('nacionalidad', $usuario->nacionalidad) == 'ecuador' ? 'selected' : '' }}>Ecuador</option>
                    <option value="uruguay" {{ old('nacionalidad', $usuario->nacionalidad) == 'uruguay' ? 'selected' : '' }}>Uruguay</option>
                </select>
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

                        // Array asociativo de valores normalizados => textos de visualización
                        $serviciosMapping = [
                        'Anal' => 'Anal',
                        'Atencion a domicilio' => 'Atención a domicilio',
                        'Atencion en hoteles' => 'Atención en hoteles',
                        'Baile erotico' => 'Baile Erótico',
                        'Besos' => 'Besos',
                        'Cambio de rol' => 'Cambio de rol',
                        'Departamento propio' => 'Departamento Propio',
                        'Disfraces' => 'Disfraces',
                        'Ducha erotica' => 'Ducha Erótica',
                        'Eventos y cenas' => 'Eventos y Cenas',
                        'Eyaculacion cuerpo' => 'Eyaculación Cuerpo',
                        'Eyaculacion facial' => 'Eyaculación Facial',
                        'Hetero' => 'Hetero',
                        'Juguetes' => 'Juguetes',
                        'Lesbico' => 'Lésbico',
                        'Lluvia dorada' => 'Lluvia dorada',
                        'Masaje erotico' => 'Masaje Erótico',
                        'Masaje prostatico' => 'Masaje prostático',
                        'Masaje tantrico' => 'Masaje Tántrico',
                        'Masaje thai' => 'Masaje Thai',
                        'Masajes con final feliz' => 'Masajes con final feliz',
                        'Masajes desnudos' => 'Masajes desnudos',
                        'Masajes eroticos' => 'Masajes Eróticos',
                        'Masajes para hombres' => 'Masajes para hombres',
                        'Masajes sensitivos' => 'Masajes sensitivos',
                        'Masajes sexuales' => 'Masajes sexuales',
                        'Masturbacion rusa' => 'Masturbación Rusa',
                        'Oral americana' => 'Oral Americana',
                        'Oral con preservativo' => 'Oral con preservativo',
                        'Oral sin preservativo' => 'Oral sin preservativo',
                        'Orgias' => 'Orgías',
                        'Parejas' => 'Parejas',
                        'Trio' => 'Trío'
                        ];
                        @endphp

                        @foreach($serviciosMapping as $valorNormalizado => $textoMostrado)
                        <label class="admin-service-item">
                            <input type="checkbox"
                                name="servicios[]"
                                value="{{ $valorNormalizado }}"
                                {{ in_array($valorNormalizado, $serviciosActuales) ? 'checked' : '' }}>
                            <span>{{ $textoMostrado }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-services-wrapper">
                    <label class="admin-services-label">Servicios Adicionales<span class="required-asterisk">*</span></label>
                    <div class="publicate-services-grid">
                        @php
                        $serviciosAdicionalesActuales = json_decode($usuario->servicios_adicionales, true) ?? [];

                        // Array asociativo de valores normalizados => textos de visualización
                        $serviciosAdicionalesMapping = [
                        'Anal' => 'Anal',
                        'Atencion a domicilio' => 'Atención a domicilio',
                        'Atencion en hoteles' => 'Atención en hoteles',
                        'Baile erotico' => 'Baile Erótico',
                        'Besos' => 'Besos',
                        'Cambio de rol' => 'Cambio de rol',
                        'Departamento propio' => 'Departamento Propio',
                        'Disfraces' => 'Disfraces',
                        'Ducha erotica' => 'Ducha Erótica',
                        'Eventos y cenas' => 'Eventos y Cenas',
                        'Eyaculacion cuerpo' => 'Eyaculación Cuerpo',
                        'Eyaculacion facial' => 'Eyaculación Facial',
                        'Hetero' => 'Hetero',
                        'Juguetes' => 'Juguetes',
                        'Lesbico' => 'Lésbico',
                        'Lluvia dorada' => 'Lluvia dorada',
                        'Masaje erotico' => 'Masaje Erótico',
                        'Masaje prostatico' => 'Masaje prostático',
                        'Masaje tantrico' => 'Masaje Tántrico',
                        'Masaje thai' => 'Masaje Thai',
                        'Masajes con final feliz' => 'Masajes con final feliz',
                        'Masajes desnudos' => 'Masajes desnudos',
                        'Masajes eroticos' => 'Masajes Eróticos',
                        'Masajes para hombres' => 'Masajes para hombres',
                        'Masajes sensitivos' => 'Masajes sensitivos',
                        'Masajes sexuales' => 'Masajes sexuales',
                        'Masturbacion rusa' => 'Masturbación Rusa',
                        'Oral americana' => 'Oral Americana',
                        'Oral con preservativo' => 'Oral con preservativo',
                        'Oral sin preservativo' => 'Oral sin preservativo',
                        'Orgias' => 'Orgías',
                        'Parejas' => 'Parejas',
                        'Trio' => 'Trío'
                        ];
                        @endphp

                        @foreach($serviciosAdicionalesMapping as $valorNormalizado => $textoMostrado)
                        <label class="admin-service-item">
                            <input type="checkbox"
                                name="servicios_adicionales[]"
                                value="{{ $valorNormalizado }}"
                                {{ in_array($valorNormalizado, $serviciosAdicionalesActuales) ? 'checked' : '' }}>
                            <span>{{ $textoMostrado }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-form-group">
                <div class="admin-services-wrapper">
                    <label class="admin-services-label">Atributos<span class="required-asterisk">*</span></label>
                    <div class="publicate-services-grid">
                        @php
                        $atributosActuales = json_decode($usuario->atributos, true) ?? [];

                        // Array asociativo de valores normalizados => textos de visualización
                        $atributosMapping = [
                        'Busto grande' => 'Busto Grande',
                        'Busto mediano' => 'Busto Mediano',
                        'Busto pequeño' => 'Busto Pequeño',
                        'Cara visible' => 'Cara Visible',
                        'Cola grande' => 'Cola Grande',
                        'Cola mediana' => 'Cola Mediana',
                        'Cola pequeña' => 'Cola Pequeña',
                        'Con video' => 'Con Video',
                        'Contextura delgada' => 'Contextura Delgada',
                        'Contextura grande' => 'Contextura Grande',
                        'Contextura mediana' => 'Contextura Mediana',
                        'Depilacion full' => 'Depilación Full',
                        'Depto propio' => 'Depto Propio',
                        'En promocion' => 'En Promoción',
                        'English' => 'English',
                        'Escort independiente' => 'Escort Independiente',
                        'Español' => 'Español',
                        'Estatura alta' => 'Estatura Alta',
                        'Estatura mediana' => 'Estatura Mediana',
                        'Estatura pequeña' => 'Estatura Pequeña',
                        'Hentai' => 'Hentai',
                        'Morena' => 'Morena',
                        'Mulata' => 'Mulata',
                        'No fuma' => 'No fuma',
                        'Ojos claros' => 'Ojos Claros',
                        'Ojos oscuros' => 'Ojos Oscuros',
                        'Peliroja' => 'Peliroja',
                        'Portugues' => 'Portugues',
                        'Relato erotico' => 'Relato Erótico',
                        'Rubia' => 'Rubia',
                        'Tatuajes' => 'Tatuajes',
                        'Trigueña' => 'Trigueña'
                        ];
                        @endphp

                        @foreach($atributosMapping as $valorNormalizado => $textoMostrado)
                        <label class="admin-service-item">
                            <input type="checkbox"
                                name="atributos[]"
                                value="{{ $valorNormalizado }}"
                                {{ in_array($valorNormalizado, $atributosActuales) ? 'checked' : '' }}>
                            <span>{{ $textoMostrado }}</span>
                        </label>
                        @endforeach
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
            value="{{ old('precio', $usuario->precio) ? 'CLP $' . number_format(old('precio', $usuario->precio), 0, ',', '.') : 'Consultar' }}"
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

                            <!-- Videos -->
                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Videos</h6>
                    <div class="form-group">
                        <input type="file" name="videos[]" multiple id="videosInput" accept="video/*" class="form-control">
                        <small class="form-text text-muted">Puede subir múltiples videos. Tamaño máximo por video: 20MB. Formatos aceptados: MP4, WEBM, OGG</small>

                        <div class="videos-actuales mt-2" id="videoPreviewContainer" data-user-id="{{ $usuario->id }}">
                            @if(!empty(json_decode($usuario->videos)))
                            @foreach(json_decode($usuario->videos) as $video)
                            <div class="publicate-preview-item video-item"
                                data-video="{{ $video }}"
                                data-user-id="{{ $usuario->id }}">
                                <video src="{{ asset('storage/chicas/'.$usuario->id.'/videos/'.$video) }}"
                                    controls
                                    class="video-preview"
                                    onerror="this.src='{{ asset('images/default-video.png') }}'">
                                </video>
                                <button type="button" class="publicate-remove-button" onclick="removeExistingVideo('{{ $video }}', this)">&times;</button>
                            </div>
                            @endforeach
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
                                        @foreach(array_slice(json_decode($usuario->fotos), 1) as $foto)
                                        <div class="publicate-preview-item"
                                            data-foto="{{ $foto }}"
                                            data-user-id="{{ $usuario->id }}"
                                            data-position="{{ json_decode($usuario->foto_positions, true)[$foto] ?? 'center' }}">

                                            <div class="content-overlay" style="display: {{ in_array($foto, json_decode($usuario->blocked_images ?? '[]', true)) ? 'flex' : 'none' }}">
                                                <span>Contenido bloqueado</span>
                                            </div>

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
                                            <button type="button"
                                                class="publicate-block-button {{ in_array($foto, json_decode($usuario->blocked_images ?? '[]', true)) ? 'active' : '' }}"
                                                onclick="toggleContentBlock(this)">
                                                {{ in_array($foto, json_decode($usuario->blocked_images ?? '[]', true)) ? 'Desbloquear' : 'Bloquear' }}
                                            </button>
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