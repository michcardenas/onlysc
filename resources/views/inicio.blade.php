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
                @endphp

                @if($primerEstado->usuarioPublicate)
                <div class="historia-item"
                    data-usuario-id="{{ $usuarioId }}"
                    onclick="mostrarHistorias({{ json_encode($estadosUsuario) }}, {{ auth()->id() }})">
                    <div class="historia-circle {{ $todasVistas ? 'historia-vista todas-vistas' : ($vistoPorUsuarioActual ? 'historia-vista' : '') }}">
                        @if($primerEstado->user_foto)
                        <img src="{{ asset('storage/' . $primerEstado->user_foto) }}"
                            alt="{{ $primerEstado->usuarioPublicate->fantasia }}">
                        @else
                        <img src="{{ asset('storage/profile_photos/default-avatar.jpg') }}"
                            alt="{{ $primerEstado->usuarioPublicate->fantasia }}">
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
                            <span class="inicio-featured-price">${{ number_format($usuarioDestacado->precio, 0, ',', '.') }}</span>
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

    <div class="sections-container">
        <!-- Volvieron Section -->
        <section class="volvieronyprimera-section">
            <div class="volvieronyprimera-header">
                <h2>Volvieron</h2>
            </div>

            <div class="swiper-container2">
                <div class="swiper-wrapper" style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                    @foreach($volvieron as $usuario)
                    @php
                    $fotos = json_decode($usuario->fotos, true);
                    $positions = json_decode($usuario->foto_positions, true) ?? [];
                    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                    $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ?
                    $positions[$primeraFoto] : 'center';

                    $isAvailable = false;
                    if ($usuario->disponibilidad) {
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
                    }

                    $mostrarPuntoVerde = $usuario->estadop == 1 && $isAvailable;
                    @endphp
                    <a href="{{ url('escorts/' . $usuario->id) }}" class="swiper-slide2" style="flex: 0 0 auto; margin-right: 0;">
                        <div class="volvieronyprimera-card">
                            <div class="volvieronyprimera-vip-tag">{{ $usuario->estadop == 3 ? 'DELUXE' : 'VIP' }}</div>
                            @php
                            $fotos = json_decode($usuario->fotos, true);
                            $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                            @endphp
                            <div class="volvieronyprimera-image"
                                style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');">
                            </div>
                            <div class="volvieronyprimera-content">
                                <div class="volvieronyprimera-user-info">
                                    <div class="volvieronyprimera-user-main">
                                        <h3 style="display: flex; align-items: center; gap: 0.5rem;">
                                            {{ $usuario->fantasia }}
                                            @if($mostrarPuntoVerde)
                                            <span class="online-dot1"></span>
                                            @endif
                                        </h3>
                                        <span class="volvieronyprimera-age">{{ $usuario->edad }}</span>
                                    </div>
                                    <div class="volvieronyprimera-location-price">
                                        <div class="location-container">
                                            <i class="fa fa-map-marker"></i>
                                            <span class="volvieronyprimera-location">{{ $usuario->ubicacion }}</span>
                                        </div>
                                        <span class="volvieronyprimera-price">${{ number_format($usuario->precio, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Primera vez Section - Misma estructura pero con $primeraVez -->
        <section class="volvieronyprimera-section">
            <div class="volvieronyprimera-header">
                <h2>Primera vez</h2>
            </div>

            <div class="swiper-container2">
                <div class="swiper-wrapper" style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                    @foreach($primeraVez as $usuario)
                    @php
                    $fotos = json_decode($usuario->fotos, true);
                    $positions = json_decode($usuario->foto_positions, true) ?? [];
                    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                    $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ?
                    $positions[$primeraFoto] : 'center';

                    $isAvailable = false;
                    if ($usuario->disponibilidad) {
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
                    }

                    $mostrarPuntoVerde = $usuario->estadop == 1 && $isAvailable;
                    @endphp
                    <a href="{{ url('escorts/' . $usuario->id) }}" class="swiper-slide2" style="flex: 0 0 auto; margin-right: 0;">
                        <div class="volvieronyprimera-card">
                            <div class="volvieronyprimera-vip-tag">{{ $usuario->estadop == 3 ? 'DELUXE' : 'VIP' }}</div>
                            @php
                            $fotos = json_decode($usuario->fotos, true);
                            $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                            @endphp
                            <div class="volvieronyprimera-image"
                                style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');">
                            </div>
                            <div class="volvieronyprimera-content">
                                <div class="volvieronyprimera-user-info">
                                    <div class="volvieronyprimera-user-main">
                                        <h3 style="display: flex; align-items: center; gap: 0.5rem;">
                                            {{ $usuario->fantasia }}
                                            @if($mostrarPuntoVerde)
                                            <span class="online-dot1"></span>
                                            @endif

                                        </h3>
                                        <span class="volvieronyprimera-age">{{ $usuario->edad }}</span>
                                    </div>
                                    <div class="volvieronyprimera-location-price">
                                        <div class="location-container">
                                            <i class="fa fa-map-marker"></i>
                                            <span class="volvieronyprimera-location">{{ $usuario->ubicacion }}</span>
                                        </div>
                                        <span class="volvieronyprimera-price">${{ number_format($usuario->precio, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>



        </section>
        <section class="UEInicio">
    <div class="UEInicio-header">
        <h2>Últimas experiencias</h2>
    </div>

    <div class="UEInicio-grid">
        @foreach($experiencias as $experiencia)
            <a href="{{ route('post.show', ['id_blog' => $experiencia->id_blog, 'id' => $experiencia->id]) }}" class="UEInicio-card">
                <div class="UEInicio-image-container">
                    <img src="{{ $experiencia->blog_imagen ? asset('storage/' . $experiencia->blog_imagen) : asset('images/default-experiencia.png') }}" 
                         alt="{{ $experiencia->titulo }}" 
                         class="UEInicio-image">
                </div>
                <div class="UEInicio-content">
                    <span class="UEInicio-date">
                        {{ \Carbon\Carbon::parse($experiencia->created_at)->format('d F, Y') }}
                    </span>
                    <h3 class="UEInicio-title">{{ $experiencia->titulo }}</h3>
                    <span class="UEInicio-author">{{ $experiencia->autor_nombre }}</span>
                </div>
            </a>
        @endforeach
    </div>
</section>
        <!-- Blog -->
        <section class="BlogInicio">
            <div class="BlogInicio-header">
                <h2>Blog</h2>
            </div>

            <div class="BlogInicio-grid">
                @foreach($blogArticles as $article)
                <a href="{{ route('blog.show_article', $article->slug) }}" class="BlogInicio-card">
                    <div class="BlogInicio-image-container">
                        <img src="{{ $article->imagen ? asset('storage/' . $article->imagen) : asset('images/default-blog.png') }}"
                            alt="{{ $article->titulo }}"
                            class="BlogInicio-image">
                        @if($article->destacado)
                        <div class="BlogInicio-destacado">Destacado</div>
                        @endif
                    </div>
                    <div class="BlogInicio-content">
                        <span class="BlogInicio-date">
                            {{ $article->fecha_publicacion ? \Carbon\Carbon::parse($article->fecha_publicacion)->format('d F, Y') : '' }}
                        </span>
                        <h3 class="BlogInicio-title">{{ $article->titulo }}</h3>
                        @if($article->extracto)
                        <p class="BlogInicio-excerpt">{{ Str::limit($article->extracto, 100) }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </section>
</main>

@endsection
@include('layouts.navigation')