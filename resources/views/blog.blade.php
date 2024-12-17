@extends('layouts.app_blog')

@section('content')
{{-- Banner y sidebar se mantienen igual --}}
<div class="blog-banner">
    <div class="blog-banner-img" style="background-image: url('{{ asset('images/728160.jpg') }}');"></div>
    <div class="blog-banner-content">
        <div class="blog-texto_banner">
            <h1><span class="700">Blog</span></h1>
        </div>
    </div>
</div>

<div class="blog-container">
    <div class="blog-blade-sidebar">
        <div class="blog-search-container">
        </div>

        <div class="blog-categories">
            <h3>TEMAS</h3>
            @foreach($categorias as $categoria)
            <div class="blog-category-item">
                <a href="#{{ $categoria->id }}" class="blog-category-link">
                    {{ $categoria->nombre }}
                    <span class="category-count">
                        ({{ $articulos->filter(function($articulo) use ($categoria) {
                            return $articulo->categories->contains('id', $categoria->id);
                        })->count() }})
                    </span>
                </a>
            </div>
            @endforeach
        </div>

        <div class="blog-popular-posts">
            <h3>POSTS POPULARES</h3>
            @foreach($articulos->sortByDesc('visitas')->take(5) as $articulo)
            <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-popular-post-item">
                {{ $articulo->titulo }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="blog-main">
        @foreach($categorias as $categoria)
        <div class="blog-section" id="{{ $categoria->id }}">
            <a href="{{ route('blog.show_category', $categoria->id) }}" class="section-title-link">
                <h2>{{ $categoria->nombre }}</h2>
            </a>
            @php
            $articulos_categoria = $articulos->filter(function($articulo) use ($categoria) {
                return $articulo->categories->contains('id', $categoria->id);
            });
            
            $destacados = $articulos_categoria->where('destacado', 1)->sortByDesc('fecha_publicacion');
            $no_destacados = $articulos_categoria->where('destacado', 0)->sortByDesc('fecha_publicacion');
            $articulos_categoria = $destacados->concat($no_destacados);
            @endphp

            @if($articulos_categoria->count() >= 1)
            <div class="blog-carousel-container">
                <div class="swiper blog-carousel-{{ $categoria->id }}">
                    <div class="swiper-wrapper">
                        @foreach($articulos_categoria as $articulo)
                        <div class="swiper-slide {{ $articulo->destacado ? 'destacado' : '' }}">
                            <div class="blog-card">
                                <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card-content">
                                    <div class="blog-card-image">
                                        <div class="blog-image" style="background-image: url('{{ Storage::url($articulo->imagen) }}')"></div>
                                        @if($articulo->destacado)
                                        <div class="destacado-badge">Destacado</div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                            <div class="blog-card-category">
                                <span>{{ $categoria->nombre }}</span>
                            </div>
                            <div class="blog-card-title-container">
                                <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card-title-link">
                                    <h3 class="blog-card-title">{{ $articulo->titulo }}</h3>
                                </a>
                                <div class="blog-card-meta">
                                    <span class="blog-card-author">Por {{ $articulo->user->name }}</span>
                                    <span class="blog-card-date">{{ \Carbon\Carbon::parse($articulo->fecha_publicacion)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            @else
            <div class="blog-grid">
                {{-- Mismo código del card pero para vista grid --}}
                @foreach($articulos_categoria as $articulo)
                <div class="blog-card-wrapper {{ $articulo->destacado ? 'destacado' : '' }}">
                    <div class="blog-card">
                        <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card-content">
                            <div class="blog-card-image">
                                <div class="blog-image" style="background-image: url('{{ Storage::url($articulo->imagen) }}')"></div>
                                @if($articulo->destacado)
                                <div class="destacado-badge">Destacado</div>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="blog-card-category">
                        <span>{{ $categoria->nombre }}</span>
                    </div>
                    <div class="blog-card-title-container">
                        <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card-title-link">
                            <h3 class="blog-card-title">{{ $articulo->titulo }}</h3>
                        </a>
                        <div class="blog-card-meta">
                            <span class="blog-card-author">Por {{ $articulo->user->name }}</span>
                            <span class="blog-card-date">{{ \Carbon\Carbon::parse($articulo->fecha_publicacion)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
@include('layouts.navigation')