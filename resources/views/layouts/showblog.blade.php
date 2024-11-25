@extends('layouts.app_foro')

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
                <a href="mailto:?subject={{ urlencode($articulo->titulo) }}&body={{ urlencode(Request::url()) }}"
                    class="social-button email">
                    <i class="fas fa-envelope"></i>
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
            <span class="blog-date">
                {{ \Carbon\Carbon::parse($articulo->created_at)->format('F d, Y') }}
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
    </div>
</div>
</div>

@endsection