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
    <div class="main-content-wrapper">
        <section class="inicio-usuarios-section">
            <div class="inicio-card-wrapper">
                <div class="inicio-card-container">
                    @foreach($usuarios as $usuario)
                    @php
                    $fotos = json_decode($usuario->fotos, true);
                    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;

                    $now = Carbon\Carbon::now();
                    $currentDay = strtolower($now->locale('es')->dayName);
                    $isAvailable = false;

                    foreach ($usuario->disponibilidad as $disponibilidad) {
                    if (strtolower($disponibilidad->dia) === $currentDay) {
                    $horaDesde = Carbon\Carbon::parse($disponibilidad->hora_desde);
                    $horaHasta = Carbon\Carbon::parse($disponibilidad->hora_hasta);

                    if ($horaHasta->lessThan($horaDesde)) {
                    if ($now->greaterThanOrEqualTo($horaDesde) || $now->lessThanOrEqualTo($horaHasta)) {
                    $isAvailable = true;
                    break;
                    }
                    } else {
                    if ($now->between($horaDesde, $horaHasta)) {
                    $isAvailable = true;
                    break;
                    }
                    }
                    }
                    }

                    $mostrarPuntoVerde = $usuario->estadop == 1 && $isAvailable;
                    @endphp

                    <a href="{{ url('escorts/' . $usuario->id) }}" class="inicio-card">
                        <div class="inicio-card-category">{{ strtoupper($usuario->categorias) }}</div>
                        <div class="inicio-card-image">
                            <div class="inicio-image" style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');"></div>
                            <div class="inicio-card-overlay">
                                <h3 class="inicio-card-title" style="display: flex; align-items: center;">
                                    {{ $usuario->fantasia }}
                                    @if($mostrarPuntoVerde)
                                    <span class="online-dot"></span>
                                    @endif
                                    <span class="inicio-card-age">{{ $usuario->edad }}</span>
                                </h3>
                                <div class="inicio-card-location">
                                    <i class="fa fa-map-marker"></i> {{ $usuario->ubicacion }}
                                    <span class="inicio-card-price">${{ number_format($usuario->precio, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>



            <!-- Usuario destacado -->
            @if($usuarioDestacado)
            @php
            $fotosDestacado = json_decode($usuarioDestacado->fotos, true);
            $primeraFotoDestacado = is_array($fotosDestacado) && !empty($fotosDestacado) ? $fotosDestacado[0] : null;
            @endphp

            <a href="{{ url('escorts/' . $usuarioDestacado->id) }}" class="inicio-featured-card">
                <div class="inicio-featured-label">CHICA DEL MES</div>
                <div class="inicio-featured-image" style="background-image: url('{{ $primeraFotoDestacado ? asset("storage/chicas/{$usuarioDestacado->id}/{$primeraFotoDestacado}") : asset("images/default-avatar.png") }}');">
                    <div class="inicio-featured-overlay">
                        <h3 class="inicio-featured-title">
                            {{ $usuarioDestacado->fantasia }}
                            <span class="inicio-featured-age">{{ $usuarioDestacado->edad }}</span>
                        </h3>
                        <div class="location-price">
                            <span class="inicio-featured-location">
                                <i class="fa fa-map-marker" aria-hidden="true"></i> {{ $usuarioDestacado->ubicacion }}
                            </span>
                            <span class="price">${{ number_format($usuarioDestacado->precio, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endif
        </section>

        <aside class="online-panel">
    <h2 class="online-panel-title">Chicas online</h2>
    <div class="online-count">
        {{ $totalOnline }} Disponibles
    </div>
    <div class="online-container">
        <ul class="online-list">
            @foreach($usuariosOnline as $usuario)
            @php
            $fotos = json_decode($usuario->fotos, true);
            $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
            @endphp

            <li class="online-item">
                <img
                    src="{{ $primeraFoto ? asset('storage/chicas/' . $usuario->id . '/' . $primeraFoto) : asset('images/default-avatar.png') }}"
                    alt="{{ $usuario->fantasia }}"
                    class="online-image"
                    loading="lazy">
                <div class="online-info">
                    <div class="online-name">
                        {{ $usuario->fantasia }}
                        <span class="online-age">{{ $usuario->edad }}</span>
                    </div>
                    <div class="online-status">ONLINE</div>
                    <a href="{{ url('escorts/' . $usuario->id) }}"
                        class="online-profile-button">Ver perfil</a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</aside>

<div class="pagination-container">
    {{ $usuarios->links('layouts.pagination') }}
</div>

@endsection