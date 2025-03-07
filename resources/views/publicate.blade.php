@extends('layouts.app_publicate')
@php
use App\Models\Servicio;
use App\Models\Atributo;
use App\Models\Nacionalidad;
use App\Models\Sector;
@endphp




@section('content')
<div class="publicate-container">
    <div class="publicate-form-section">
    <img src="{{ asset('images/logo_v2.png') }}" alt="Logo OnlyEscorts" class="publicate-logo">
        <h1>Formulario de registro</h1>
        <p>Bienvenida, sabemos que tu tiempo es valioso y no queremos abrumarte con las innumerables razones por las que debes anunciar con nosotros, pero si te contaremos algo, somos Líderes en Resultados, que no te cuenten cuentos, con nosotros conseguirás los clientes respetuosos y solventes que buscas, estás a solo un paso de tomar una excelente decisión.</p>
        
        <form action="{{ route('publicate.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="publicate-form-group">
        <div class="publicate-input-wrapper">
            <input type="text" id="fantasia" name="fantasia" placeholder=" " required>
            <label for="fantasia">Nombre de Fantasía</label>
        </div>
        <div class="publicate-input-wrapper">
            <input type="email" id="email" name="email" placeholder=" " required>
            <label for="email">Email</label>
        </div>
    </div>
    
    <div class="publicate-form-group">
        <div class="publicate-input-wrapper">
            <input type="text" id="nombre" name="nombre" placeholder=" " required>
            <label for="nombre">Nombre</label>
        </div>
        <div class="publicate-input-wrapper">
            <input type="password" id="password" name="password" placeholder=" " required>
            <label for="password">Contraseña</label>
        </div>
    </div>
    
    <div class="publicate-form-group">
        <div class="publicate-input-wrapper">
            <input type="text" id="telefono" name="telefono" placeholder=" ">
            <label for="telefono">Teléfono</label>
        </div>
        <div class="publicate-input-wrapper">
            <select id="ubicacion" name="ubicacion" required class="styled-select">
                <option value="" disabled selected>Seleccione una ciudad</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->nombre }}">{{ $ciudad->nombre }}</option>
                @endforeach
            </select>
        </div>

    </div>
    
    <div class="publicate-form-group">
        <div class="publicate-input-wrapper">
            <input type="number" id="edad" name="edad" placeholder=" " required>
            <label for="edad">Edad</label>
        </div>
        <div class="publicate-input-wrapper">
            <input type="text" id="color_ojos" name="color_ojos" placeholder=" " required>
            <label for="color_ojos">Color de ojos</label>
        </div>
    </div>
    
    <div class="publicate-form-group">
        <div class="publicate-input-wrapper">
            <input type="number" id="altura" name="altura" placeholder=" " required>
            <label for="altura">Altura</label>
        </div>
        <div class="publicate-input-wrapper">
            <input type="number" id="peso" name="peso" placeholder=" " required>
            <label for="peso">Peso</label>
        </div>
    </div>
    
    <div class="publicate-form-group">
    <div class="publicate-input-wrapper">
        <input 
            type="text" 
            id="disponibilidad" 
            name="disponibilidad" 
            placeholder="Ej: lunes a viernes de 18 a 02am" 
            required
        >
        <label for="disponibilidad">Disponibilidad</label>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-services-wrapper">
        <label class="publicate-services-label">Servicios <span class="required-asterisk">*</span></label>
        <div class="publicate-services-grid">
            @php
            $servicios = Servicio::orderBy('posicion')->get();
            @endphp
            @foreach($servicios as $servicio)
            <label class="publicate-service-item">
                <input type="checkbox" 
                    name="servicios[]" 
                    value="{{ $servicio->id }}">
                <span>{{ $servicio->nombre }}</span>
            </label>
            @endforeach
        </div>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-services-wrapper">
        <label class="publicate-services-label">Servicios Adicionales<span class="required-asterisk">*</span></label>
        <div class="publicate-services-grid">
            @php
            $serviciosAdicionales = Servicio::orderBy('posicion')->get();
            @endphp
            @foreach($serviciosAdicionales as $servicio)
            <label class="publicate-service-item">
                <input type="checkbox" 
                    name="servicios_adicionales[]" 
                    value="{{ $servicio->id }}">
                <span>{{ $servicio->nombre }}</span>
            </label>
            @endforeach
        </div>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-services-wrapper">
        <label class="publicate-services-label">Atributos<span class="required-asterisk">*</span></label>
        <div class="publicate-services-grid">
            @php
            $atributos = Atributo::orderBy('posicion')->get();
            @endphp
            @foreach($atributos as $atributo)
            <label class="publicate-service-item">
                <input type="checkbox" 
                    name="atributos[]" 
                    value="{{ $atributo->id }}">
                <span>{{ $atributo->nombre }}</span>
            </label>
            @endforeach
        </div>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-upload-wrapper">
        <label class="publicate-upload-label">Envíanos tus fotos <span class="required-asterisk">*</span></label>
        <div class="publicate-upload-area" id="dropZone">
            <div class="publicate-upload-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 15V3M12 3L7 8M12 3L17 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 15V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="publicate-upload-text">Drag and Drop (or) <span class="publicate-upload-link">Choose Files</span></p>
        </div>
        <input type="file" class="publicate-upload-input" id="fileInput" name="fotos[]" multiple accept="image/*">
                <div class="publicate-preview-container" id="previewContainer"></div>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-textarea-container">
        <div class="publicate-textarea-label">
            <label>Cuéntanos sobre ti <span class="required-asterisk">*</span></label>
        </div>
        <div class="publicate-textarea-wrapper">
            <textarea id="cuentanos" name="cuentanos" required></textarea>
        </div>
    </div>
</div>
<div class="publicate-form-group">
    <div class="publicate-declaration-wrapper">
        <label class="publicate-declaration-item">
            <input type="checkbox" name="declaration" required>
            <span>Declaro que soy mayor de 18 años</span>
        </label>
    </div>
</div>

            <button type="submit" class="publicate-btn">Enviar solicitud</button>
        </form>
        @if(session('success'))
    <div class="alert alert-success">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <span>{{ session('error') }}</span>
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

    </div>

    <div class="publicate-image-section">
        <img src="{{ asset('images/pexels-79380313-9007274-scaled.jpg') }}" alt="Imagen de Registro">
    </div>
</div>
@endsection
