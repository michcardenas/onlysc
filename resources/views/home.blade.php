@extends('layouts.app_home')

@section('selector')
    <!-- Contenido del selector de ciudades -->
    <div class="location-selector">
    <i class="fas fa-map-marker-alt"></i>
    <form id="location-form" action="/inicio" method="POST">
        @csrf
        <select id="ciudad" name="ciudad" required>
            <option value="" disabled selected>Seleccionar ubicación</option>
            @foreach($ciudades as $ciudad)
                <option value="{{ strtolower($ciudad->nombre) }}">{{ ucfirst($ciudad->nombre) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-location">ENTRAR</button>
</form>

</div>
@endsection

@section('content')
    <!-- Swiper Container -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <!-- Tarjeta 1 -->
            <div class="swiper-slide">
                <div class="card">
                    <a href="/foro" class="card-link">
                        <img src="{{ asset('images/foro.jpg') }}" alt="Foro">
                        <div class="card-content">
                            <p>Participa en nuestra comunidad</p> <!-- Se corrigió <php> a <p> -->
                            <h3>Foro</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Tarjeta 2 -->
            <div class="swiper-slide">
                <div class="card">
                    <a href="/sitemap" class="card-link">
                        <img src="{{ asset('images/sitemap.webp') }}" alt="Sitemap">
                        <div class="card-content">
                            <p>Mapa de nuestro sitio</p>
                            <h3>Sitemap</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Tarjeta 3 -->
            <div class="swiper-slide">
                <div class="card">
                    <a href="/contacto" class="card-link">
                        <img src="{{ asset('images/contacto.jpg') }}" alt="Contacto">
                        <div class="card-content">
                            <p>Ponte en contacto</p>
                            <h3>Contacto</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Tarjeta 4 -->
            <div class="swiper-slide">
                <div class="card contacto">
                    <a href="/blog" class="card-link">
                        <img src="{{ asset('images/blog.jpg') }}" alt="Blog">
                        <div class="card-content">
                            <p>Actualizaciones y noticias</p>
                            <h3>Blog</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Tarjeta 5 -->
            <div class="swiper-slide">
                <div class="card contacto">
                    <a href="/publicate" class="card-link">
                        <img src="{{ asset('images/publicate.jpeg') }}" alt="Publicate">
                        <div class="card-content">
                            <p>Publica tu anuncio ahora</p>
                            <h3>Publicate</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Paginación (opcional) -->
        <div class="swiper-pagination"></div>
    </div>
@endsection
