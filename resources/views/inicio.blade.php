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

<!-- Sección de Historias -->
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
                    data-estados='{{ json_encode($estadosUsuario) }}'>
                    <div class="historia-circle {{ $todasVistas ? 'historia-vista todas-vistas' : ($vistoPorUsuarioActual ? 'historia-vista' : '') }}">
                        @if($primerEstado->user_foto)
                        <img src="{{ asset('storage/' . $primerEstado->user_foto) }}"
                            alt="{{ $primerEstado->usuarioPublicate->fantasia }}">
                        @else
                        <img src="{{ asset('storage/profile_photos/default-avatar.jpg') }}"
                            alt="{{ $primerEstado->usuarioPublicate->fantasia }}">
                        @endif
                    </div>
                    <span class="historia-nombre">{{ ucfirst(Str::lower($primerEstado->usuarioPublicate->fantasia)) }}</span>
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
            <button class="nav-btn prev-btn">&lt;</button>
            <button class="nav-btn next-btn">&gt;</button>
        </div>
        <div id="historia-contenido"></div>
        <div class="historia-indicators"></div>
    </div>
</div>





<main class="inicio-container">
    <div class="main-content-wrapper">
        <section class="inicio-usuarios-section">
            <div class="inicio-card-wrapper">
            @include('components.breadcrumb')
                <div class="inicio-card-container">
                    @if($usuarios->isEmpty())
                    <div class="no-results" style="width: 100%; text-align: center; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 1rem;">
                        <p style="font-size: 1.25rem; color: #666; margin-bottom: 1rem;">No encontramos resultados para tu búsqueda</p>
                        <p style="color: #888;">Intenta ajustar los filtros o realizar una nueva búsqueda</p>
                        <a href="{{ url()->current() }}" class="btn btn-primary" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #e00037; color: white; text-decoration: none; border-radius: 4px;">Ver todas</a>
                    </div>
                    @else
                    @foreach($usuarios as $usuario)
   @php
       $fotos = json_decode($usuario->fotos, true);
       $positions = json_decode($usuario->foto_positions, true) ?? [];
       $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
       $posicionFoto = in_array(($positions[$primeraFoto] ?? ''), ['left', 'right', 'center']) ? $positions[$primeraFoto] : 'center';

       $currentDay = strtolower($now->locale('es')->dayName);
       $isAvailable = false;
       foreach ($usuario->disponibilidad as $disponibilidad) {
           if (strtolower($disponibilidad->dia) === $currentDay) {
               if ($disponibilidad->hora_desde === '00:00:00' && $disponibilidad->hora_hasta === '23:59:00') {
                   $isAvailable = true;
                   break;
               }

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

       $mostrarPuntoVerde = ($usuario->estadop == 1 || $usuario->estadop == 3) && $isAvailable;
       
       if ($usuarioDestacado) {
           $fotosDestacado = json_decode($usuarioDestacado->fotos, true);
           $positionsDestacado = json_decode($usuarioDestacado->foto_positions, true) ?? [];
           $descripcionFotosDestacado = json_decode($usuarioDestacado->descripcion_fotos, true) ?? [];
           $primeraFotoDestacado = is_array($fotosDestacado) && !empty($fotosDestacado) ? $fotosDestacado[0] : null;
           $posicionFotoDestacado = in_array(($positionsDestacado[$primeraFotoDestacado] ?? ''), ['left', 'right', 'center']) ? $positionsDestacado[$primeraFotoDestacado] : 'center';
           $descripcionFotoDestacado = $descripcionFotosDestacado[$primeraFotoDestacado] ?? 'Foto de ' . $usuarioDestacado->fantasia;
       }
       
       $descripcionFotos = json_decode($usuario->descripcion_fotos, true) ?? [];
   @endphp

                    <a href="{{ route('perfil.show', ['nombre' => $usuario->fantasia . '-' . $usuario->id]) }}" class="inicio-card">
                        <div class="inicio-card-category">{{ strtoupper(str_replace('_', ' ', $usuario->categorias)) }}</div>
                        <div class="inicio-card-image">
                        <div class="inicio-image"
                            style="background-image: url('{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}');
                                    background-position: {{ $posicionFoto }} center;">
                            <img src="{{ $primeraFoto ? asset("storage/chicas/{$usuario->id}/{$primeraFoto}") : asset("images/default-avatar.png") }}"
                            alt="{{ $descripcionFotos[$primeraFoto] ?? 'Foto de ' . $usuario->fantasia }}"
 
                                style="visibility: hidden; height: 0;">
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
                                    <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon2">
                                    @if($ciudadSeleccionada->url === 'santiago')
                                    @if($usuario->sector)
                                    {{ $usuario->sector->nombre }}
                                    @else
                                    {{ $ubicacionesMostradas[$usuario->id] ?? 'Sector no disponible' }}
                                    @endif
                                    @else
                                    {{ $usuario->ubicacion }}
                                    @endif
                                    <span class="inicio-card-price">
                                        {{ $usuario->precio > 0 ? '$' . number_format($usuario->precio, 0, ',', '.') : 'Consultar' }}
                                    </span>
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

            // Lógica de disponibilidad para usuario destacado
            $isAvailableDestacado = false;
            foreach ($usuarioDestacado->disponibilidad as $disponibilidad) {
            if (strtolower($disponibilidad->dia) === $currentDay) {
            // Check for full time availability
            if (trim($disponibilidad->hora_desde) === '00:00:00' && trim($disponibilidad->hora_hasta) === '23:59:00') {
            $isAvailableDestacado = true;
            break;
            }

            // Regular time slot check
            $horaDesde = Carbon\Carbon::parse($disponibilidad->hora_desde);
            $horaHasta = Carbon\Carbon::parse($disponibilidad->hora_hasta);

            if ($horaHasta->lessThan($horaDesde)) {
            if ($now->greaterThanOrEqualTo($horaDesde) || $now->lessThanOrEqualTo($horaHasta)) {
            $isAvailableDestacado = true;
            break;
            }
            } else {
            if ($now->between($horaDesde, $horaHasta)) {
            $isAvailableDestacado = true;
            break;
            }
            }
            }
            }

            $mostrarPuntoVerdeDestacado = ($usuarioDestacado->estadop == 1 || $usuarioDestacado->estadop == 3) && $isAvailableDestacado;
            @endphp

            <a href="{{ route('perfil.show', ['nombre' => $usuarioDestacado->fantasia . '-' . $usuarioDestacado->id]) }}" class="inicio-featured-card">
                <div class="inicio-featured-label">CHICA DEL MES</div>
                <div class="inicio-featured-image"
                    style="background-image: url('{{ $primeraFotoDestacado ? asset("storage/chicas/{$usuarioDestacado->id}/{$primeraFotoDestacado}") : asset("images/default-avatar.png") }}');
               background-position: {{ $posicionFotoDestacado }} center;">
               <img src="{{ $primeraFotoDestacado ? asset("storage/chicas/{$usuarioDestacado->id}/{$primeraFotoDestacado}") : asset("images/default-avatar.png") }}"
               alt="{{ isset($descripcionFotoDestacado) ? $descripcionFotoDestacado : 'Foto de escort' }}"
                    style="visibility: hidden; height: 0;">
                        
                    <div class="inicio-featured-overlay">
                        <h3 class="inicio-featured-title">
                            {{ $usuarioDestacado->fantasia }}
                            @if($mostrarPuntoVerdeDestacado)
                            <span class="online-dot"></span>
                            @endif
                            <span class="inicio-featured-age">{{ $usuarioDestacado->edad }}</span>
                        </h3>
                        <div class="location-price">
                            <span class="inicio-featured-location">
                                <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon2" aria-hidden="true"></i>
                                @if($ciudadSeleccionada->url === 'santiago')
                                @if($usuarioDestacado->sector)
                                {{ $usuarioDestacado->sector->nombre }}
                                @else
                                {{ $ubicacionesMostradas[$usuarioDestacado->id] ?? 'Sector no disponible' }}
                                @endif
                                @else
                                {{ $usuarioDestacado->ubicacion }}
                                @endif
                            </span>
                            <span class="inicio-featured-price">
                                {{ $usuarioDestacado->precio ? '$' . number_format($usuarioDestacado->precio, 0, ',', '.') : 'Consultar' }}
                            </span>
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
                            <a href="{{ route('perfil.show', ['nombre' => $usuario->fantasia . '-' . $usuario->id]) }}" class="online-profile-button">Ver perfil</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        @if(!$usuarios->isEmpty())
        <div class="pagination-container">
            {{ $usuarios->links('layouts.pagination') }}
        </div>
        @endif
    </div>


    <div class="sections-container">
        <!-- Volvieron Section -->
        <section class="volvieronyprimera-section">
            <div class="swiper-container2">
                <div class="category-header">
                    <h2>Escorts de Regreso</h2>
                </div>
                <div class="swiper-wrapper" style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                    @foreach($volvieron as $usuario)
                    @php
                    $now = now();

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

                    $mostrarPuntoVerde = ($usuario->estadop == 1 || $usuario->estadop == 3) && $isAvailable;
                  

                    @endphp
                    <a href="{{ route('perfil.show', ['nombre' => $usuario->fantasia . '-' . $usuario->id]) }}" class="swiper-slide2" style="flex: 0 0 auto; margin-right: 0;">
                        <div class="volvieronyprimera-card">
                            <div class="watermark-container">
                                <div class="watermark"></div>
                            </div>

                            <div class="volvieronyprimera-vip-tag">{{ strtoupper(str_replace('_', ' ', $usuario->categorias)) }}</div>
                            @php
                            $fotos = json_decode($usuario->fotos, true);
                            $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                            $descripcionFotos = json_decode($usuario->descripcion_fotos, true) ?? [];
    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;

    $descripcionFoto = $descripcionFotos[$primeraFoto] ?? 'Foto de ' . $usuario->fantasia;
                            
                            @endphp
                            <img class="volvieronyprimera-image"
                                src="{{ $primeraFoto ? (file_exists(storage_path('app/public/chicas/' . $usuario->id . '/thumb_' . $primeraFoto)) ? 
       asset('storage/chicas/' . $usuario->id . '/thumb_' . $primeraFoto) : 
       asset('storage/chicas/' . $usuario->id . '/' . $primeraFoto)) : 
       asset('images/default-avatar.png') }}"alt="{{ $descripcionFoto }}" 
                                />
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
                                            <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon2">
                                            <span class="volvieronyprimera-location">
                                            @if($ciudadSeleccionada->url === 'santiago')
    @if($usuario->sector)
        {{ $usuario->sector->nombre }}
    @else
        {{ $ubicacionesMostradas[$usuario->id] ?? 'Sector no disponible' }}
    @endif
@else
    {{ $usuario->ubicacion }}
@endif
                                            </span>
                                        </div>
                                        <span class="volvieronyprimera-price">{{ $usuario->precio > 0 ? '$' . number_format($usuario->precio, 0, ',', '.') : 'Consultar' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="swiper-pagination2"></div>
            </div>
        </section>

        <!-- Primera vez Section -->
        <section class="volvieronyprimera-section">
            <div class="swiper-container2">
                <div class="category-header">
                    <h2>Escorts Nuevas</h2>
                </div>
                <div class="swiper-wrapper" style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                    @foreach($primeraVez as $usuario)
                    @php
                    $now = now();

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

                    $mostrarPuntoVerde = ($usuario->estadop == 1 || $usuario->estadop == 3) && $isAvailable;
                    $descripcionFotos = json_decode($usuario->descripcion_fotos, true) ?? [];
    $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;

    $descripcionFoto = $descripcionFotos[$primeraFoto] ?? 'Foto de ' . $usuario->fantasia;
                    @endphp
                    <a href="{{ route('perfil.show', ['nombre' => $usuario->fantasia . '-' . $usuario->id]) }}" class="swiper-slide2" style="flex: 0 0 auto; margin-right: 0;">
                        <div class="volvieronyprimera-card">
                            <div class="watermark-container">
                                <div class="watermark"></div>
                            </div>
                            <div class="volvieronyprimera-vip-tag">{{ strtoupper(str_replace('_', ' ', $usuario->categorias)) }}</div>
                            @php
                            $fotos = json_decode($usuario->fotos, true);
                            $primeraFoto = is_array($fotos) && !empty($fotos) ? $fotos[0] : null;
                            @endphp
                            <img class="volvieronyprimera-image"
                                src="{{ $primeraFoto ? (file_exists(storage_path('app/public/chicas/' . $usuario->id . '/thumb_' . $primeraFoto)) ? 
       asset('storage/chicas/' . $usuario->id . '/thumb_' . $primeraFoto) : 
       asset('storage/chicas/' . $usuario->id . '/' . $primeraFoto)) : 
       asset('images/default-avatar.png') }}"alt="{{ $descripcionFoto }}" 
                                 />
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
                                            <img src="{{ asset('images/location.svg') }}" alt="location-icon" class="location-icon2">
                                            <span class="volvieronyprimera-location">
                                                @if($ciudadSeleccionada->url === 'santiago')
                                                {{ $ubicacionesMostradas[$usuario->id] ?? 'Sector no disponible' }}
                                                @else
                                                {{ $usuario->ubicacion }}
                                                @endif
                                            </span>
                                        </div>
                                        <span class="volvieronyprimera-price">{{ $usuario->precio > 0 ? '$' . number_format($usuario->precio, 0, ',', '.') : 'Consultar' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="swiper-pagination2"></div>
            </div>


        </section>

        <!-- Últimas experiencias Section -->
        <section class="UEInicio">
            <div class="swiper-container2">
                <div class="category-header">
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
                <div class="swiper-pagination2"></div>
            </div>
        </section>

        <!-- Blog Section -->
        <section class="BlogInicio">
            <div class="swiper-blog">
                <div class="category-header">
                <h2>Blog 
                @if(isset($ciudadSeleccionada))
                    <span class="ciudad-title">de {{ $ciudadSeleccionada->nombre }}</span>
                @endif
            </h2>
                </div>
                <div class="BlogInicio-grid swiper-wrapper">
                    @foreach($blogArticles as $article)
                    <div class="BlogInicio-item swiper-slide">
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
                                @if($article->contenido)
                                <p class="BlogInicio-excerpt">{{ Str::limit(strip_tags($article->contenido), 100) }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <!-- Solo mostrar en móvil -->
                <div class="swiper-pagination mobile-only"></div>
                <div class="swiper-button-next mobile-only"></div>
                <div class="swiper-button-prev mobile-only"></div>
            </div>
        </section>





</main>
@if(isset($seoTitle) && isset($seoDescription))
<div class="seo-section">
<h2 class="seo-title">
    {{ strip_tags(html_entity_decode($seoTitle)) }}
</h2>

    <div class="seo-description">{!! $seoDescription !!}</div>
</div>
@endif
@endsection
@include('layouts.navigation')