@extends('layouts.app_foro')

@section('content')
{{-- Banner del Blog --}}
<div class="foro-banner">
    <div class="blog-banner-img" style="background-image: url('{{ asset('images/728160.jpg') }}');">
    </div>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            <h1>
                <span class="700">Blog</span>
            </h1>
        </div>
    </div>
</div>


<div class="blog-container">
    <div class="blog-main">
        {{-- Artículos por categoría --}}
        @foreach($categorias as $categoria)
        <div class="blog-section">
            <h2>{{ $categoria->nombre }}</h2>
            @php
                $articulos_categoria = $articulos->filter(function($articulo) use ($categoria) {
                    return $articulo->categories->contains('id', $categoria->id);
                });
            @endphp

            @if($articulos_categoria->count() > 3)
                <div class="blog-carousel-container">
                    <div class="swiper blog-carousel-{{ $categoria->id }}">
                        <div class="swiper-wrapper">
                            @foreach($articulos_categoria as $articulo)
                            <div class="swiper-slide">
                                <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card">
                                    <div class="blog-card-image">
                                        <div class="inicio-image" style="background-image: url('{{ Storage::url($articulo->imagen) }}')"></div>
                                        <div class="blog-card-overlay">
                                            <h3 class="blog-card-title">{{ $articulo->titulo }}</h3>
                                            <div class="blog-card-meta">
                                                <span>{{ $articulo->fecha_publicacion->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            @else
                <div class="blog-card-container">
                    @foreach($articulos_categoria as $articulo)
                    <a href="{{ route('blog.show_article', $articulo->id) }}" class="blog-card">
                        <div class="blog-card-image">
                            <div class="blog-image" style="background-image: url('{{ Storage::url($articulo->imagen) }}')"></div>
                            <div class="blog-card-overlay">
                                <h3 class="blog-card-title">{{ $articulo->titulo }}</h3>
                                <div class="blog-card-meta">
                                    <span>{{ $articulo->fecha_publicacion->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
    </div>
    
    {{-- Sidebar --}}
    <div class="blog-sidebar">
        <h3>Más Leídos</h3>
        @foreach($articulos->sortByDesc('visitas')->take(5) as $articulo)
        <div class="blog-sidebar-post">
            <h4>{{ $articulo->titulo }}</h4>
            <p>{{ strip_tags(Str::limit($articulo->contenido, 100)) }}</p>
            <div class="blog-sidebar-meta">
                <span>{{ $articulo->visitas }} visitas</span>
                <span>{{ $articulo->fecha_publicacion->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection