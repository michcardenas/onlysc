@extends('layouts.app')

@section('content')
<header class="banner" style="background-color: #e00037;">
    <div class="banner-content">
        <div class="texto_banner" style="height: 36rem;">
            <h1>
                <span class="thin">Mis</span>
                <span class="bold">Favoritos</span>
            </h1>
        </div>
    </div>
</header>

<main class="inicio-container">
    <div class="main-content-wrapper">
        <section class="inicio-usuarios-section">
            <div class="inicio-card-wrapper">
                <div class="inicio-card-container">
                    @if($favorites->isEmpty())
                        <div class="no-results">No tienes favoritos guardados</div>
                    @else
                        @foreach($favorites as $favorite)
                            @php
                                $usuario = $favorite->usuarioPublicate;
                                if (!$usuario) continue;

                                $fotos = json_decode($usuario->fotos, true);
                                $positions = json_decode($usuario->foto_positions, true) ?? [];
                                $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                                $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ? $positions[$primeraFoto] : 'center';

                                $now = Carbon\Carbon::now();
                                $disponibilidadTexto = $usuario->disponibilidad;
                                $isAvailable = true; // Puedes implementar tu lógica de disponibilidad aquí

                                $mostrarPuntoVerde = ($usuario->estadop == 1 || $usuario->estadop == 3) && $isAvailable;
                            @endphp

                            <a href="{{ $usuario->getPerfilUrl() }}" class="inicio-card">
                                <div class="inicio-card-category">{{ strtoupper($usuario->categorias) }}</div>
                                <div class="inicio-card-image">
                                    <div class="inicio-image"
                                        style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');
                                                background-position: {{ $posicionFoto }} center;">
                                    </div>
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
                    @endif
                </div>
            </div>
        </section>

        <div class="pagination-container">
            {{ $favorites->links('layouts.pagination') }}
        </div>
    </div>
</main>
@endsection
@include('layouts.navigation')