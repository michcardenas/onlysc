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
            <option value="{{ strtolower($ciudad->url) }}">{{ ucfirst($ciudad->nombre) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-location">ENTRAR</button>
</form>

</div>
@endsection

@section('content')
    <!-- Swiper Container -->
    <div class="swiper-container home-swiper">
    <div class="swiper-wrapper" style="display: flex;">
        @foreach($tarjetas as $tarjeta)
            <div class="swiper-slide">
                <div class="card">
                    <a href="{{ $tarjeta->link }}" class="card-link">
                        <img src="{{ asset('storage/' . $tarjeta->imagen) }}" alt="{{ $tarjeta->titulo }}">
                        <div class="card-content">
                            <p>{{ $tarjeta->descripcion }}</p>
                            <h3>{{ $tarjeta->titulo }}</h3>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

 <!-- Paginación (opcional) -->
 <div class="swiper-pagination"></div>
    </div>

    <!-- Encabezado H2 con texto descriptivo -->
    <div class="wrapper">
    <div class="glass-container">
        <h2 class="mission-title">{{ $meta->heading_h2 }}</h2>
        <div class="content-wrapper">
            <div class="mission-text" id="mission-text">
                {!! $meta->additional_text !!}
            </div>
            <div class="extended-text" id="additional-text" style="display: none;">
                <br>
                {!! $meta->additional_text_more !!}
            </div>
        </div>
        <button id="btn-ver-mas" class="btn-glass">Ver más</button>
    </div>
</div>

        <!-- Paginación (opcional) -->
        <div class="swiper-pagination"></div>
    </div>
@endsection
