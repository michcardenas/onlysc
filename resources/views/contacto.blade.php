@extends('layouts.app_contacto')

@section('content')
<header class="banner">
    <img src="{{ isset($meta->fondo) ? Storage::url($meta->fondo) : asset('images/banner1.jpg') }}" alt="Banner Image" class="banner-img">
    <div class="banner-content">
        <div class="texto_banner">
            <div class="heading-container">
                <h1 class="thin">
                    {{ $meta->heading_h1 ?? 'Encuentra tu' }}
                </h1>
                <h2 class="bold">
                    {{ $meta->heading_h2 ?? 'experiencia perfecta' }}
                </h2>
            </div>
        </div>
    </div>
</header>
<div class="contact-container">
    <h1 class="contact-title">Contáctanos</h1>
    
    <div class="contact-row">
        <div class="contact-col">
            <!-- Tarjeta principal -->
            <div class="contact-card">
                <div class="contact-card-body">
                    <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                        @csrf
                        
                        <!-- Grupo: Nombre -->
                        <div class="contact-form-group">
                            <label for="name" class="contact-label">Nombre</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                class="contact-input @error('name') contact-input-error @enderror" 
                                value="{{ old('name') }}" 
                                placeholder="Tu nombre" 
                                required
                            >
                            @error('name')
                                <div class="contact-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Grupo: Email -->
                        <div class="contact-form-group">
                            <label for="email" class="contact-label">Correo electrónico</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="contact-input @error('email') contact-input-error @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="tucorreo@ejemplo.com" 
                                required
                            >
                            @error('email')
                                <div class="contact-error">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Grupo: Asunto -->
                        <div class="contact-form-group">
                            <label for="subject" class="contact-label">Asunto</label>
                            <input 
                                type="text" 
                                name="subject" 
                                id="subject" 
                                class="contact-input @error('subject') contact-input-error @enderror" 
                                value="{{ old('subject') }}"
                                placeholder="Motivo de tu mensaje"
                                required
                            >
                            @error('subject')
                                <div class="contact-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Grupo: Mensaje -->
                        <div class="contact-form-group">
                            <label for="message" class="contact-label">Mensaje</label>
                            <textarea 
                                name="message" 
                                id="message" 
                                class="contact-textarea @error('message') contact-input-error @enderror" 
                                rows="5"
                                placeholder="Escribe tu mensaje aquí"
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <div class="contact-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botón de envío -->
                        <div class="contact-btn-container">
                            <button type="submit" class="contact-btn">
                                Enviar mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Texto adicional (opcional) -->
            <div class="contact-extra-info">
                <p class="contact-text-muted">
                    O si lo prefieres, puedes escribirnos directamente a 
                    <strong>contacto@onlyescorts.cl</strong>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection