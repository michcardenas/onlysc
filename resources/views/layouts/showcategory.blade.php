@extends('layouts.app_foro')

@section('content')
{{-- El banner se mantiene igual --}}
<div class="foro-banner">
    <div class="blogcategory-banner-img" style="background-image: url('{{ asset('images/728160.jpg') }}');">
    </div>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            <h1>
                <span class="700">{{ $categoria->nombre }}</span>
            </h1>
        </div>
    </div>
</div>

<div class="blogcategory-container">
    {{-- Sidebar se mantiene igual --}}
    <div class="blogcategory-blade-sidebar">
        <div class="blogcategory-search-container">
            <input type="text" class="blogcategory-search-input" placeholder="Search...">
            <button class="blogcategory-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="blogcategory-categories">
            <h3>TEMAS</h3>
            @foreach($categorias as $cat)
            <div class="blogcategory-category-item">
                <a href="{{ route('blog.show_category', $cat->id) }}" class="blogcategory-category-link">
                    {{ $cat->nombre }}
                    <span class="category-count">
                        ({{ $cat->articles_count }})
                    </span>
                </a>
            </div>
            @endforeach
        </div>

        <div class="blogcategory-popular-posts">
            <h3>POSTS POPULARES</h3>
            @foreach($articulos->sortByDesc('visitas')->take(5) as $articulo)
            <a href="{{ route('blog.show_article', $articulo->id) }}" class="blogcategory-popular-post-item">
                {{ $articulo->titulo }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Contenido principal mostrando solo artículos de la categoría seleccionada --}}
    <div class="blogcategory-main">
        <div class="blogcategory-section">
            <div class="blogcategory-grid">
                @foreach($articulos as $articulo)
                <div class="blogcategory-card-wrapper">
                    <div class="blogcategory-card">
                        <a href="{{ route('blog.show_article', $articulo->id) }}" class="blogcategory-card-content">
                            <div class="blogcategory-card-image">
                                <div class="blogcategory-image" style="background-image: url('{{ Storage::url($articulo->imagen) }}')"></div>
                            </div>
                        </a>
                    </div>
                    <div class="blogcategory-card-tags">
                        @foreach($articulo->tags as $tag)
                        <div class="blogcategory-card-tag">
                            <span>{{ $tag->nombre }}</span>
                        </div>
                        @endforeach
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
        </div>
    </div>
</div>

@endsection