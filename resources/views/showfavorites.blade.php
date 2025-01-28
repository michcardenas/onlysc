@extends('layouts.app')

@section('content')
<header class="banner">
<img src="{{ isset($meta->fondo) ? Storage::url($meta->fondo) : asset('images/banner1.jpg') }}" alt="Banner Image" class="banner-img">
    <div class="banner-content">
        <div class="texto_banner">
            <div class="heading-container">
                <h1 class="thin">
                    {{ $meta->heading_h1 ?? 'Encuentra tu' }}
                </h1>
                <h2 class="bold">
                    {{ $meta->heading_h2 ?? 'experiencia perfecta' }}
                </h2>
            </div>
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

                            <div class="inicio-card">
                            <a href="{{ route('perfil.show', ['nombre' => $usuario->fantasia . '-' . $usuario->id]) }}" class="inicio-card">

                                    <div class="inicio-card-category">{{ strtoupper(str_replace('_', ' ', $usuario->categorias)) }}</div>
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
                                                <span class="inicio-card-price">{{ $usuario->precio > 0 ? '$' . number_format($usuario->precio, 0, ',', '.') : 'Consultar' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
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