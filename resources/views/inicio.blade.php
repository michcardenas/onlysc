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

<section class="estados-historias">
    <div class="historias-wrapper">
        <div class="historias-titulos">
            <h4>ÚLTIMAS HISTORIAS</h4>
            <a href="#" class="ver-todas">Ver todas</a>
        </div>
        
        <div class="historias-container">
            @if($estados->isEmpty())
                <p class="no-historias">No hay historias disponibles</p>
            @else
                <div class="historias-scroll">
                    @php
                        $estadosAgrupados = $estados->groupBy('usuarios_publicate_id');
                    @endphp

                    @foreach($estadosAgrupados as $usuarioId => $estadosUsuario)
                        @php
                            $primerEstado = $estadosUsuario->first();
                            $vistoPorUsuarioActual = $primerEstado->vistoPor
                                ->where('id', auth()->id())
                                ->isNotEmpty();
                            $todasVistas = $estadosUsuario->every(function($estado) {
                                return $estado->vistoPor->where('id', auth()->id())->isNotEmpty();
                            });
                            $mediaFiles = json_decode($primerEstado->fotos, true);
                        @endphp
                        
                        @if($primerEstado->usuarioPublicate)
                            <div class="historia-item" 
                                 data-usuario-id="{{ $usuarioId }}"
                                 onclick="mostrarHistorias({{ json_encode($estadosUsuario) }}, {{ auth()->id() }})">
                                <div class="historia-circle {{ $todasVistas ? 'historia-vista todas-vistas' : ($vistoPorUsuarioActual ? 'historia-vista' : '') }}">
                                    @if(!empty($mediaFiles['imagenes']))
                                        <img src="{{ Storage::url($mediaFiles['imagenes'][0]) }}" 
                                             alt="{{ $primerEstado->usuarioPublicate->fantasia }}">
                                    @elseif(!empty($mediaFiles['videos']))
                                        <video>
                                            <source src="{{ Storage::url($mediaFiles['videos'][0]) }}" 
                                                    type="video/{{ pathinfo($mediaFiles['videos'][0], PATHINFO_EXTENSION) }}">
                                        </video>
                                    @endif
                                </div>
                                <span class="historia-nombre">{{ Str::lower($primerEstado->usuarioPublicate->fantasia) }}</span>
                                <span class="historia-tiempo">hace {{ $primerEstado->created_at->diffForHumans(null, true) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Modal para las historias -->
<div id="historiaModal" class="historia-modal">
    <div class="modal-contenido">
        <span class="cerrar-modal">&times;</span>
        <div class="modal-header">
            <div class="usuario-info">
                <div class="usuario-avatar">
                    <img id="modal-profile-image" src="" alt="Perfil">
                </div>
                <div class="usuario-detalles">
                <a id="modal-usuario-nombre" href="#" class="nombre-link"></a>
                    <span id="modal-historia-tiempo"></span>
                </div>
            </div>
        </div>
        <div class="modal-navigation">
            <button class="nav-btn prev-btn" onclick="previousHistoria()">&lt;</button>
            <button class="nav-btn next-btn" onclick="nextHistoria()">&gt;</button>
        </div>
        <div id="historia-contenido"></div>
        <div class="historia-indicators"></div>
    </div>
</div>


<main class="inicio-container">
    <div class="main-content-wrapper">
        <section class="inicio-usuarios-section">
            <div class="inicio-card-wrapper">
                <div class="inicio-card-container">
                    @if($usuarios->isEmpty())
                    <div class="no-results">No hay chicas disponibles</div>
                    @else
                    @foreach($usuarios as $usuario)
                    @php
                    $fotos = json_decode($usuario->fotos, true);
                    $positions = json_decode($usuario->foto_positions, true) ?? [];
                    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                    $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ? $positions[$primeraFoto] : 'center';

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

            @if($usuarioDestacado)
            @php
            $fotosDestacado = json_decode($usuarioDestacado->fotos, true);
            $positionsDestacado = json_decode($usuarioDestacado->foto_positions, true) ?? [];
            $primeraFotoDestacado = is_array($fotosDestacado) && !empty($fotosDestacado) ? $fotosDestacado[0] : null;
            $posicionFotoDestacado = in_array(($positionsDestacado[$primeraFotoDestacado] ?? ''), ['left', 'right', 'center']) ? $positionsDestacado[$primeraFotoDestacado] : 'center';
            @endphp

            <a href="{{ url('escorts/' . $usuarioDestacado->id) }}" class="inicio-featured-card">
                <div class="inicio-featured-label">CHICA DEL MES</div>
                <div class="inicio-featured-image"
                    style="background-image: url('{{ $primeraFotoDestacado ? asset("storage/chicas/{$usuarioDestacado->id}/{$primeraFotoDestacado}") : asset("images/default-avatar.png") }}');
                            background-position: {{ $posicionFotoDestacado }} center;">
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
                    $positions = json_decode($usuario->foto_positions, true) ?? [];
                    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                    $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ? $positions[$primeraFoto] : 'center';
                    @endphp

                    <li class="online-item">
                        <img src="{{ $primeraFoto ? asset('storage/chicas/' . $usuario->id . '/' . $primeraFoto) : asset('images/default-avatar.png') }}"
                            alt="{{ $usuario->fantasia }}"
                            class="online-image"
                            style="object-position: {{ $posicionFoto }} center;"
                            loading="lazy">
                        <div class="online-info">
                            <div class="online-name">
                                {{ $usuario->fantasia }}
                                <span class="online-age">{{ $usuario->edad }}</span>
                            </div>
                            <div class="online-status">ONLINE</div>
                            <a href="{{ url('escorts/' . $usuario->id) }}" class="online-profile-button">Ver perfil</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <div class="pagination-container">
            {{ $usuarios->links('layouts.pagination') }}
        </div>
    </div>
</main>

@endsection