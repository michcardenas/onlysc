@extends('layouts.app_home') <!-- Extiende el layout app_home -->

@section('selector')
    <!-- Contenido del selector de ciudades -->
    <div class="location-selector">
        <i class="fas fa-map-marker-alt"></i> <!-- Ícono de ubicación -->
        <form action="/set-location" method="POST">
            @csrf
            <select name="ciudad" required>
                <option value="" disabled selected>Seleccionar ubicación</option>
                @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->id }}">{{ ucfirst($ciudad->nombre) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-location">ENTRAR</button>
        </form>
    </div>
@endsection

@section('content')
    <!-- Tarjetas debajo del selector -->
    <div class="card">
        <a href="/foro" class="card-link">
            <img src="{{ asset('images/foro.jpg') }}" alt="Foro">
            <div class="card-content">
                <h3>Foro</h3>
                <p>Participa en nuestra comunidad</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="/sitemap" class="card-link">
            <img src="{{ asset('images/sitemap.webp') }}" alt="Sitemap">
            <div class="card-content">
                <h3>Sitemap</h3>
                <p>Mapa de nuestro sitio</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="/contacto" class="card-link">
            <img src="{{ asset('images/contacto.jpg') }}" alt="Contacto">
            <div class="card-content">
                <h3>Contacto</h3>
                <p>Ponte en contacto</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="/blog" class="card-link">
            <img src="{{ asset('images/blog.jpg') }}" alt="Blog">
            <div class="card-content">
                <h3>Blog</h3>
                <p>Actualizaciones y noticias</p>
            </div>
        </a>
    </div>
@endsection
