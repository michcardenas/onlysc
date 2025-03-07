@extends('layouts.app_foro')

@section('content')
<div class="foro-banner">
    <video autoplay muted loop playsinline class="banner-img">
        <source src="{{ asset('images/Minimalist-Motivational-Quotes-Videos.mp4') }}" type="video/mp4">
        Tu navegador no soporta el elemento de video.
    </video>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            @if($metaTags)
                <h1>
                    <span class="700">{{ $metaTags->heading_h1 ?? 'Nuestros Foros' }}</span>
                </h1>
                @if($metaTags->heading_h2)
                    <h2>
                        <span>{{ $metaTags->heading_h2 }}</span>
                    </h2>
                @endif
                @if($metaTags->heading_h2_secondary)
                    <h2>
                        <span>{{ $metaTags->heading_h2_secondary }}</span>
                    </h2>
                @endif
            @else
                <h1>
                    <span class="700">Nuestros Foros</span>
                </h1>
            @endif
        </div>
    </div>
</div>
<div class="pruebaflex">
<div class="foro-search-container">
    <input type="text" id="foro-search-input" class="foro-search-input" placeholder="Buscar tema...">
    <button id="foro-search-btn" class="foro-search-btn">
        <svg viewBox="0 0 24 24">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
        </svg>
    </button>
</div>


<!-- Contenedor principal -->
<div class="foro-layout">
    <!-- Columna principal: Foros -->
    <div class="foro-main-column">
        <div class="foro-grid">
            @foreach($foros as $foro)
            <a href="{{ route('foro.show_foro', $foro->id) }}" class="foro-card">
                <div class="foro-card-content">
                    <h3 class="foro-card-title">{{ $foro->titulo }}</h3>
                    <p class="foro-card-description">{{ $foro->subtitulo }}</p>
                </div>
                <div class="foro-card-image">
                    @if($foro->foto)
                        <img src="{{ asset('storage/' . $foro->foto) }}" alt="{{ $foro->titulo }}">
                    @else
                        <img src="{{ asset('images/default-foro.jpg') }}" alt="Imagen por defecto">
                    @endif
                </div>
                <div class="foro-card-footer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"/>
                    </svg>
                    <span>Por: {{ $foro->nombre_usuario }}</span>
                    <span class="ml-2">{{ \Carbon\Carbon::parse($foro->fecha)->diffForHumans() }}</span>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Paginación -->
   
    </div>
  
    <!-- Columna lateral: Últimas publicaciones -->
    <div class="foro-sidebar">
    <div class="foro-comments">
        <h3 class="foro-comments__title">Últimas Publicaciones</h3>
        @if($foros->count() > 0)
            @foreach($foros->take(3) as $foro)
            <a href="{{ route('foro.show_foro', $foro->id) }}" class="foro-comment-link">
                <div class="foro-comment">
                    <h4>{{ $foro->titulo }}</h4>
                    <p>{!! Str::limit($foro->contenido, 150) !!}</p>
                    <div class="foro-comment__footer">
                        <span>Por: {{ $foro->nombre_usuario }}</span>
                        <span>{{ \Carbon\Carbon::parse($foro->fecha)->diffForHumans() }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        @else
            <p class="foro-comments__empty">No hay publicaciones recientes.</p>
        @endif
    </div>
</div>
</div>
<div class="foro-pagination">
    {{ $foros->links() }}
</div>
</div>
@endsection
@include('layouts.navigation')