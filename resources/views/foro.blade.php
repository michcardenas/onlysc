@extends('layouts.app_foro')

@section('content')
<div class="foro-banner">
    <video autoplay muted loop playsinline class="banner-img">
        <source src="{{ asset('images/Minimalist-Motivational-Quotes-Videos.mp4') }}" type="video/mp4">
        Tu navegador no soporta el elemento de video.
    </video>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            <h1>
                <span class="700">Nuestros Foros</span>
            </h1>
        </div>
    </div>
</div>

<!-- Buscador con CSS personalizado -->

<div class="foro-search-container">
    <input type="text" class="foro-search-input" placeholder="Buscar tema...">
    <button class="foro-search-btn">
        <svg viewBox="0 0 24 24">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </svg>
    </button>
</div>

<div class="foro-container">
    <div class="foro-main">

    <div class="foro-cards-container">
    <div class="foro-cards-grid">
        <!-- Tarjeta de Conversaciones sobre Sexo -->
        <a href="{{ route('foro.show_foro', 'conversaciones') }}" class="foro-card">
            <div class="foro-card-content">
                <h3 class="foro-card-title">Conversaciones sobre Sexo</h3>
                <p class="foro-card-description">Bienvenidos a "Charla sobre Sexo", un espacio sin tabúes para discutir todo lo relacionado con la sexualidad.</p>
            </div>
            <div class="foro-card-image">
                <img src="{{ asset('images/foro1.jpg') }}" alt="Imagen representativa">
            </div>
            <div class="foro-card-footer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                </svg>
                <span>0 comentarios</span>
            </div>
        </a>

        <!-- Tarjeta de Experiencias -->
        <a href="{{ route('foro.show_foro', 'experiencias') }}" class="foro-card">
            <div class="foro-card-content">
                <h3 class="foro-card-title">Experiencias</h3>
                <p class="foro-card-description">Descubre y comparte tu experiencia con las chicas de la plataforma.</p>
            </div>
            <div class="foro-card-image">
                <img src="{{ asset('images/pexels-79380313-9007274-scaled.jpg') }}" alt="Imagen representativa">
            </div>
            <div class="foro-card-footer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                </svg>
                <span>10 comentarios</span>
            </div>
        </a>

        <!-- Tarjeta de Gentlemen's Club -->
        <a href="{{ route('foro.show_foro', 'gentlemens-club') }}" class="foro-card">
            <div class="foro-card-content">
                <h3 class="foro-card-title">Gentlemen's Club</h3>
                <p class="foro-card-description">Para hablar con libertad de lo que desees.</p>
            </div>
            <div class="foro-card-image">
                <img src="{{ asset('images/foro2.jpeg') }}" alt="Imagen representativa">
            </div>
            <div class="foro-card-footer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                </svg>
                <span>0 comentarios</span>
            </div>
        </a>
    </div>
</div>
</div>

<div class="foro-sidebar">
        <div class="foro-comments">
            <h3 class="foro-comments__title">Últimos comentarios</h3>
            <p class="foro-comments__empty">No hay comentarios recientes.</p>
        </div>
    </div>
</div>

<!-- Aquí iría el resto del contenido del foro -->
@endsection