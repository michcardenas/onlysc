@extends('layouts.app')

@section('content')
<header class="banner">
    <img src="{{ asset('images/banner1.jpg') }}" alt="Banner Image" class="banner-img">
    <div class="banner-content">
        <div class="texto_banner">
            <h1>
                <span class="thin">Encuentra tu</span>
                <span class="bold">experiencia perfecta</span>
            </h1>
        </div>
    </div>
</header>

<main class="inicio-container">
    <section class="inicio-usuarios-section">
        <!-- Contenedor de tarjetas y tarjeta destacada -->
        <div class="inicio-card-wrapper">
            <!-- Contenedor de tarjetas -->
            <div class="inicio-card-container">
                @foreach($usuarios as $usuario)
                @php
                // Decodificar el campo fotos y obtener la primera imagen
                $fotos = json_decode($usuario->fotos, true);
                $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                @endphp
                <div class="inicio-card">
                    <div class="inicio-card-category">{{ strtoupper($usuario->categorias) }}</div>
                    <div class="inicio-card-image">
                        <div class="inicio-image" style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');"></div>
                        <div class="inicio-card-overlay">
                            <h3 class="inicio-card-title">{{ $usuario->fantasia }}
                                <span class="inicio-card-age">{{ $usuario->edad }}</span>
                            </h3>
                            <div class="inicio-card-location">
                                <i class="fa fa-map-marker"></i> {{ $usuario->ubicacion }}
                                <span class="inicio-card-price">${{ number_format($usuario->precio, 0, ',', '.') }}</span>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
            @php
            // Datos de prueba para el usuario destacado
            $usuarioDestacado = (object) [
            'id' => 1,
            'fantasia' => 'Julieta',
            'ubicacion' => 'Santiago',
            'edad' => 25,
            'fotos' => json_encode(['sample-photo.jpg']), // Foto de prueba
            'precio' => '250000', // Foto de prueba
            ];

            $fotosDestacado = json_decode($usuarioDestacado->fotos, true);
            $primeraFotoDestacado = is_array($fotosDestacado) && !empty($fotosDestacado) ? $fotosDestacado[0] : null;
            @endphp

            <!-- Tarjeta destacada a la derecha -->
            @if($usuarioDestacado)
            <div class="inicio-featured-card">
                <!-- Texto destacado en la parte superior -->
                <div class="inicio-featured-label">CHICA DEL MES</div>

                @php
                $fotosDestacado = json_decode($usuarioDestacado->fotos, true);
                $primeraFotoDestacado = is_array($fotosDestacado) && !empty($fotosDestacado) ? $fotosDestacado[0] : null;
                @endphp
                <div class="inicio-featured-image" style="background-image: url('{{ $primeraFotoDestacado ? asset("storage/chicas/{$usuarioDestacado->id}/{$primeraFotoDestacado}") : asset("images/default-avatar.png") }}');">
                    <<div class="inicio-featured-overlay">
                        <!-- Título con nombre y edad en la misma línea -->
                        <h3 class="inicio-featured-title">
                            {{ $usuarioDestacado->fantasia }}
                            <span class="inicio-featured-age">{{ $usuarioDestacado->edad }}</span>
                        </h3>

                        <!-- Ubicación y precio en la misma línea -->
                        <div class="location-price">
                            <span class="inicio-featured-location">
                                <i class="fa fa-map-marker" aria-hidden="true"></i> {{ $usuarioDestacado->ubicacion }}
                            </span>
                            <span class="price">${{ number_format($usuarioDestacado->precio, 0, ',', '.') }}</span>
                        </div>
                </div>
            </div>
        </div>
        @endif
        </div>
    </section>
</main>

@if($usuarioDestacado)
<!-- Tarjeta destacada HTML -->
@else
<p>No hay usuario destacado.</p>
@endif

<div class="online-panel">
    <h2 class="online-panel-title">Chicas online</h2>
    <div class="online-count">
        {{ $totalOnline }} Disponibles
    </div>
    <ul class="online-list">
        @foreach($usuariosOnline as $usuario)
        @php
        $fotos = json_decode($usuario->fotos, true);
        $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
        @endphp
        <li class="online-item">
            <img src="{{ $primeraFoto ? asset('storage/chicas/' . $usuario->id . '/' . $primeraFoto) : asset('images/default-avatar.png') }}"
                alt="{{ $usuario->fantasia }}"
                class="online-image">
            <div class="online-info">
                <div class="online-name">
                    {{ $usuario->fantasia }}
                    <span class="online-age">{{ $usuario->edad }}</span>
                </div>
                <div class="online-status">ONLINE</div>
                <a href="{{ route('perfil.show', ['id' => $usuario->id]) }}" class="online-profile-button">Ver perfil</a>
            </div>
        </li>
        @endforeach
    </ul>
</div>

@endsection