@extends('layouts.app_blog')

@section('title')
{{ $articulo->titulo }}
@endsection

@section('content')
<div class="blog-container">
    {{-- Sección de compartir y tabla de contenidos --}}
    <div class="blog-sidebar">
        <div class="share-section">
            <span>Compartir</span>
            <div class="social-icons">
                <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(Request::url()) }}&title={{ urlencode($articulo->titulo) }}"
                    target="_blank"
                    class="social-button linkedin">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text={{ urlencode($articulo->titulo) }}"
                    target="_blank"
                    class="social-button twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://github.com/share?url={{ urlencode(Request::url()) }}"
                    target="_blank"
                    class="social-button github">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($articulo->titulo . ' ' . Request::url()) }}"
                    class="social-button email">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>

        <div class="table-contents">
            <h3>TABLA DE CONTENIDOS</h3>
            <div id="contenidos-dinamicos" class="contents-list">
                <!-- Se llenará dinámicamente con JavaScript -->
            </div>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="blog-content">
        <h1 class="blog-title">{{ $articulo->titulo }}</h1>
        <div class="article-meta">
            <span class="blog-author">Por {{ $articulo->user->name }}</span>
            <span class="blog-date">
                {{ \Carbon\Carbon::parse($articulo->fecha_publicacion)->format('F d, Y') }}
            </span>
        </div>

        @if($articulo->imagen)
        <div class="featured-image">
            <img src="{{ asset('storage/' . $articulo->imagen) }}"
                alt="{{ $articulo->titulo }}"
                class="rounded-lg shadow-lg">
        </div>
        @endif

        <div class="blog-text">
            {!! $articulo->contenido !!}
        </div>

        <div class="author-section">
            <div class="author-card">
                <div class="author-avatar">
                    @if($articulo->user->foto)
                    <img src="{{ Storage::url($articulo->user->foto) }}" alt="{{ $articulo->user->name }}">
                    @else
                    <img src="{{ asset('images/default-avatar.jpg') }}" alt="{{ $articulo->user->name }}">
                    @endif
                </div>
                <div class="author-info">
                    <h3 class="author-title">
                        <span class="author-name">{{ $articulo->user->name }}</span>
                        @if($articulo->user->linkedin)
                        <a href="https://{{ $articulo->user->linkedin }}" target="_blank" class="linkedin-icon">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        @endif
                    </h3>
                    <p class="author-bio">{{ $articulo->user->descripcion ?? 'No hay descripción disponible' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
@include('layouts.navigation')