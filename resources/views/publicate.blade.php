@extends('layouts.app_publicate')

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
            <input type="text" id="ubicacion" name="ubicacion" placeholder=" " required>
            <label for="ubicacion">Ubicación</label>
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
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Anal">
                <span>Anal</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Atención a domicilio">
                <span>Atención a domicilio</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Atención en hoteles">
                <span>Atención en hoteles</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Baile Erotico">
                <span>Baile Erótico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Besos">
                <span>Besos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Cambio de rol">
                <span>Cambio de rol</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Departamento Propio">
                <span>Departamento Propio</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Disfraces">
                <span>Disfraces</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Ducha Erotica">
                <span>Ducha Erótica</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Eventos y Cenas">
                <span>Eventos y Cenas</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Eyaculación Cuerpo">
                <span>Eyaculación Cuerpo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Eyaculación Facial">
                <span>Eyaculación Facial</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Hetero">
                <span>Hetero</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Juguetes">
                <span>Juguetes</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Lesbico">
                <span>Lésbico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Lluvia dorada">
                <span>Lluvia dorada</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masaje Erotico">
                <span>Masaje Erótico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masaje prostatico">
                <span>Masaje prostático</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masaje Tantrico">
                <span>Masaje Tántrico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masaje Thai">
                <span>Masaje Thai</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes con final feliz">
                <span>Masajes con final feliz</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes desnudos">
                <span>Masajes desnudos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes Eroticos">
                <span>Masajes Eróticos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes para hombres">
                <span>Masajes para hombres</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes sensitivos">
                <span>Masajes sensitivos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masajes sexuales">
                <span>Masajes sexuales</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Masturbación Rusa">
                <span>Masturbación Rusa</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Oral Americana">
                <span>Oral Americana</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Oral con preservativo">
                <span>Oral con preservativo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Oral sin preservativo">
                <span>Oral sin preservativo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Orgias">
                <span>Orgías</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Parejas">
                <span>Parejas</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios[]" value="Trio">
                <span>Trío</span>
            </label>
        </div>
    </div>
</div>

<div class="publicate-form-group">
    <div class="publicate-services-wrapper">
        <label class="publicate-services-label">Servicios Adicionales<span class="required-asterisk">*</span></label>
        <div class="publicate-services-grid">
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Anal">
                <span>Anal</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Atención a domicilio">
                <span>Atención a domicilio</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Atención en hoteles">
                <span>Atención en hoteles</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Baile Erotico">
                <span>Baile Erótico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Besos">
                <span>Besos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Cambio de rol">
                <span>Cambio de rol</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Departamento Propio">
                <span>Departamento Propio</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Disfraces">
                <span>Disfraces</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Ducha Erotica">
                <span>Ducha Erótica</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Eventos y Cenas">
                <span>Eventos y Cenas</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Eyaculación Cuerpo">
                <span>Eyaculación Cuerpo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Eyaculación Facial">
                <span>Eyaculación Facial</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Hetero">
                <span>Hetero</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Juguetes">
                <span>Juguetes</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Lesbico">
                <span>Lésbico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Lluvia dorada">
                <span>Lluvia dorada</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masaje Erotico">
                <span>Masaje Erótico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masaje prostatico">
                <span>Masaje prostático</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masaje Tantrico">
                <span>Masaje Tántrico</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masaje Thai">
                <span>Masaje Thai</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes con final feliz">
                <span>Masajes con final feliz</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes desnudos">
                <span>Masajes desnudos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes Eroticos">
                <span>Masajes Eróticos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes para hombres">
                <span>Masajes para hombres</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes sensitivos">
                <span>Masajes sensitivos</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masajes sexuales">
                <span>Masajes sexuales</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Masturbación Rusa">
                <span>Masturbación Rusa</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Oral Americana">
                <span>Oral Americana</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Oral con preservativo">
                <span>Oral con preservativo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Oral sin preservativo">
                <span>Oral sin preservativo</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Orgias">
                <span>Orgías</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Parejas">
                <span>Parejas</span>
            </label>
            <label class="publicate-service-item">
                <input type="checkbox" name="servicios_adicionales[]" value="Trio">
                <span>Trío</span>
            </label>
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
            <textarea id="about" name="about" required></textarea>
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
    </div>

    <div class="publicate-image-section">
        <img src="{{ asset('images/pexels-79380313-9007274-scaled.jpg') }}" alt="Imagen de Registro">
    </div>
</div>
@endsection
