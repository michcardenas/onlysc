@extends('layouts.app')

@php
$pageTitle = $usuarioPublicate->fantasia . ' Escort ' .
($usuarioPublicate->categorias ? ucfirst(strtolower($usuarioPublicate->categorias)) . ' ' : '') .
'en ' . $usuarioPublicate->ubicacion . ' | OnlyEscorts';
@endphp

@section('content')

<div class="escortperfil-container">
    <header class="escortperfil-banner">
        <div class="escortperfil-swiper">
            <div class="escortperfil-swiper-wrapper swiper-wrapper">
                @php
                $fotos = json_decode($usuarioPublicate->fotos, true) ?? [];
                $blockedImages = json_decode($usuarioPublicate->blocked_images, true) ?? [];

                // Solo aplicamos el reemplazo si el usuario NO está autenticado
                if (!Auth::check()) {
                foreach ($fotos as $key => $foto) {
                if (is_array($blockedImages) && in_array($foto, $blockedImages)) {
                $fotos[$key] = 'Exclusivo-para-miembros.png';
                }
                }
                }
                @endphp

                @foreach($fotos as $foto)
                <div class="escortperfil-swiper-slide swiper-slide">
                    @if($foto === 'Exclusivo-para-miembros.png')
                    <img src="{{ asset('storage/chicas/Exclusivo-para-miembros.png') }}"
                        alt="Contenido exclusivo"
                        class="escortperfil-banner-img"
                        onclick="openEscortModal(this.src)">
                    @else
                    <img src="{{ asset("storage/chicas/{$usuarioPublicate->id}/{$foto}") }}"
                        alt="Foto de {{ $usuarioPublicate->fantasia }}"
                        class="escortperfil-banner-img"
                        onclick="openEscortModal(this.src)">
                    @endif
                </div>
                @endforeach

            </div>

            <!-- Botones de navegación -->
            <div class="escortperfil-swiper-button-prev carousel-control prev">&lt;</div>
            <div class="escortperfil-swiper-button-next carousel-control next">&gt;</div>
            <!-- Paginación -->
            <div class="escortperfil-swiper-pagination carousel-indicators"></div>
        </div>

        <h1 class="escortperfil-nombre">
            {{ strtoupper($usuarioPublicate->fantasia) }}
        </h1>

        <h1 class="escortperfil-nombrechikito">
        {{ strtoupper(str_replace('_', ' ', $usuarioPublicate->categorias)) }}
        </h1>
    </header>



    <div class="escortperfil-modal-backdrop" id="escortperfilModalBackdrop" onclick="closeEscortModal()"></div>
    <div class="escortperfil-modal" id="escortperfilImageModal">
        <button class="escortperfil-modal-close" onclick="closeEscortModal()">×</button>
        <img class="escortperfil-modal-image" id="escortperfilModalImage" src="" alt="Imagen ampliada">
    </div>

    <div class="escortperfil-info-bar">
        <div class="escortperfil-info-item">
            <img src="{{ asset('images/pais.svg') }}" alt="Nacionalidad" class="escortperfil-info-icon">
            <div class="escortperfil-info-text">
                <span class="escortperfil-info-label">Nacionalidad</span>
                <span class="escortperfil-info-value">{{ $usuarioPublicate->nacionalidad ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="escortperfil-info-item">
            <img src="{{ asset('images/calendar.svg') }}" alt="Edad" class="escortperfil-info-icon">
            <div class="escortperfil-info-text">
                <span class="escortperfil-info-label">Edad</span>
                <span class="escortperfil-info-value">{{ $usuarioPublicate->edad }}</span>
            </div>
        </div>
        <div class="escortperfil-info-item">

    <img src="{{ asset('images/precio.svg') }}" alt="Precio" class="escortperfil-info-icon">
    <div class="escortperfil-info-text">
        <span class="escortperfil-info-label">Precio</span>
        @if($usuarioPublicate->precio)
            <span class="escortperfil-info-value">${{ number_format($usuario->precio, 0, ',', '.') }}</span>
        @else
            <a href="https://wa.me/{{ $usuarioPublicate->telefono }}?text=Hola%20{{ $usuarioPublicate->fantasia }}!%20Vi%20tu%20anuncio%20en%20OnlyEscorts%20y%20me%20gustar%C3%ADa%20saber%20m%C3%A1s%20sobre%20tus%20servicios.%20%C2%BFC%C3%B3mo%20est%C3%A1s?" 
               target="_blank" 
               class="escortperfil-info-value text-decoration-none">
                Consultar
            </a>
        @endif
    </div>
</div>

        <div class="escortperfil-info-item">
            <img src="{{ asset('images/ubicacion.svg') }}" alt="Ubicación" class="escortperfil-info-icon">
            <div class="escortperfil-info-text">
                <span class="escortperfil-info-label">Ubicación</span>
                <span class="escortperfil-info-value">{{ $usuarioPublicate->ubicacion }}</span>
            </div>
        </div>
    </div>


    <button class="favorite-button {{ $usuarioPublicate->isFavoritedByUser(auth()->id()) ? 'active' : '' }}"
        data-id="{{ $usuarioPublicate->id }}">
        <i class="far fa-heart"></i>
        <span>AÑADIR A<br>FAVORITOS</span>
    </button>

    <!-- Botón que abre el modal -->
    <button class="share-button" onclick="openShareModal()">
        <div class="icon-wrapper">
            <img src="/images/share-arrows-svgrepo-com.svg" alt="Share icon" class="share-icon">
        </div>
        <div class="text-wrapper">
            COMPARTIR<br>PERFIL
        </div>
    </button>


    <!-- Modal de compartir -->
    <div id="shareModal" class="share-modal">
        <div class="share-modal-content">
            <div class="share-modal-header">
                <h3>Compartir</h3>
                <button onclick="closeShareModal()" class="close-button">&times;</button>
            </div>

            <div class="share-buttons">
                <a href="#" onclick="shareOnFacebook()" class="share-icon facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="#" onclick="shareOnX()" class="share-icon x">
                    <img src="{{ asset('images/x.svg') }}" alt="Compartir en X" class="social-icon-img">
                </a>

                <a href="#" onclick="shareOnTelegram()" class="share-icon telegram">
                    <i class="fab fa-telegram"></i>
                </a>
            </div>

            <div class="share-url">
                <input type="text" id="shareUrl" readonly>
                <button onclick="copyUrl()" class="copy-button">COPIAR</button>
            </div>
        </div>
    </div>



    <!-- Breadcrumb -->
    <div class="escortperfil-breadcrumb">
        <a href="/">Inicio</a>
        <span class="separator">/</span>
        <a href="{{ url('/escorts-' . $usuarioPublicate->ciudad_url) }}">Escorts en {{ $usuarioPublicate->ciudad_nombre }}</a>
        <span class="separator">/</span>
        <span>{{ $usuarioPublicate->fantasia }}</span>
    </div>

    <div class="escortperfil-side-section">
        <div class="escortperfil-actions">
            <a href="https://wa.me/{{ $usuarioPublicate->telefono }}?text=Hola%20{{ $usuarioPublicate->fantasia }}!%20Vi%20tu%20anuncio%20en%20OnlyEscorts%20y%20me%20gustar%C3%ADa%20saber%20m%C3%A1s%20sobre%20tus%20servicios.%20%C2%BFC%C3%B3mo%20est%C3%A1s?" class="escortperfil-btn disponible" target="_blank">
                <i class="fab fa-whatsapp"></i> WHATSAPP
            </a>
            <button class="escortperfil-btn contactar">CONTACTAR</button>
        </div>

        <aside class="escortperfil-schedule">
            <h2 class="escortperfil-section-title">Horario</h2>
            <div class="escortperfil-schedule-list">
                @php
                $dias = [
                'Lunes' => 'LUN',
                'Martes' => 'MAR',
                'Miércoles' => 'MIE',
                'Jueves' => 'JUE',
                'Viernes' => 'VIE',
                'Sábado' => 'SAB',
                'Domingo' => 'DOM'
                ];

                $disponibilidad = \App\Models\Disponibilidad::where('publicate_id', $usuarioPublicate->id)
                ->where('estado', 'activo')
                ->get();

                $disponibilidadMap = [];
                foreach($disponibilidad as $disp) {
                $horarioDesde = \Carbon\Carbon::parse($disp->hora_desde)->format('H:i');
                $horarioHasta = \Carbon\Carbon::parse($disp->hora_hasta)->format('H:i');

                $disponibilidadMap[$disp->dia] = [
                'hora_desde' => $horarioDesde,
                'hora_hasta' => $horarioHasta,
                'is_full_time' => ($horarioDesde === '00:00' && $horarioHasta === '23:59')
                ];
                }
                @endphp

                @foreach($dias as $diaCompleto => $diaAbrev)
                <div class="escortperfil-schedule-item {{ strtolower($diaCompleto) == strtolower(\Carbon\Carbon::now()->locale('es')->dayName) ? 'current-day' : '' }}">
                    <span class="day-badge">{{ $diaAbrev }}</span>
                    <span class="schedule-time">
                        @if(isset($disponibilidadMap[$diaCompleto]))
                        @if($disponibilidadMap[$diaCompleto]['is_full_time'])
                        Full Time
                        @else
                        {{ $disponibilidadMap[$diaCompleto]['hora_desde'] }} - {{ $disponibilidadMap[$diaCompleto]['hora_hasta'] }} hs
                        @endif
                        @else
                        No disponible
                        @endif
                    </span>
                </div>
                @endforeach
            </div>

            <div class="escortperfil-map-section">
                <h2 class="escortperfil-section-title">Ubicación</h2>
                <div id="escort-map" class="escort-map"></div>
            </div>

        </aside>
        <div class="escortperfil-content">
            <div class="escortperfil-section">
                <h2 class="escortperfil-section-title">Sobre mí</h2>
                <p class="escortperfil-description-text">{{ $usuarioPublicate->cuentanos }}</p>
            </div>

            <!-- Estas dos secciones deberían estar agrupadas -->
            <div>
                <div class="escortperfil-section">
                    <h2 class="escortperfil-section-title">Atributos</h2>
                    <div class="escortperfil-attributes-list">
                        @php
                        $atributos = json_decode($usuarioPublicate->atributos, true);
                        $atributos = is_array($atributos) ? $atributos : [];
                        @endphp
                        @foreach($atributos as $atributo)
                        <span class="escortperfil-attribute-item">{{ $atributo }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="escortperfil-section">
                    <h2 class="escortperfil-section-title">Datos de ubicación</h2>
                    <div class="escortperfil-description">
                        <h3 class="escortperfil-subtitle">Ubicaciones donde atiendo</h3>
                        @if(!empty($usuarioPublicate->u1))
                        <div class="escortperfil-locations-list">
                            <div class="escortperfil-location-item">
                                {{ $usuarioPublicate->u1 }}
                            </div>
                        </div>
                        @else
                        <p class="text-center">No hay ubicaciones disponibles</p>
                        @endif

                        <h3 class="escortperfil-subtitle">Servicios del sitio</h3>
                        @if(!empty($usuarioPublicate->u2))
                        <div class="escortperfil-locations-list">
                            <div class="escortperfil-location-item">
                                {{ $usuarioPublicate->u2 }}
                            </div>
                        </div>
                        @else
                        <p class="text-center">No hay servicios disponibles</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Sección de Servicios -->
        <div class="escortperfil-section">
            <h2 class="escortperfil-section-title">Servicios</h2>
            <div class="escortperfil-services-list">
                @php
                $serviciosRaw = $usuarioPublicate->servicios;

                // Limpiamos todas las barras invertidas y comillas extras
                $serviciosClean = str_replace('\\', '', $serviciosRaw);
                $serviciosClean = trim($serviciosClean, '"');

                // Convertimos a array
                $servicios = json_decode($serviciosClean, true);

                // Si aún es null, intentamos una última limpieza
                if (!is_array($servicios)) {
                $serviciosClean = preg_replace('/["\\\]+/', '', $serviciosRaw);
                $serviciosClean = trim($serviciosClean, '[]');
                $servicios = explode(',', $serviciosClean);
                }

                // Limpiamos cada elemento del array y corregimos caracteres especiales
                $servicios = array_map(function($item) {
                $item = trim(str_replace(['\\', '"', '[', ']'], '', $item));
                $item = htmlspecialchars_decode($item, ENT_QUOTES);

                // Correcciones específicas para caracteres mal codificados
                $replacements = [
                'u00f3' => 'ó',
                'u00e9' => 'é',
                'u00e1' => 'á',
                'u00ed' => 'í',
                'u00fa' => 'ú',
                'u00f1' => 'ñ',
                'Erotico' => 'Erótico',
                'Tantrico' => 'Tántrico',
                'prostatico' => 'prostático',
                'Lesbico' => 'Lésbico',
                'Orgias' => 'Orgías',
                'Trio' => 'Trío'
                ];

                return str_replace(array_keys($replacements), array_values($replacements), $item);
                }, $servicios);

                // Eliminamos elementos vacíos
                $servicios = array_filter($servicios, function($item) {
                return !empty($item);
                });
                @endphp
                @foreach($servicios as $servicio)
                <span class="escortperfil-service-item">{{ $servicio }}</span>
                @endforeach
            </div>
        </div>

        <!-- Sección de Servicios Adicionales -->
        <div class="escortperfil-section">
            <h2 class="escortperfil-section-title">Servicios Adicionales</h2>
            <div class="escortperfil-services-list">
                @php
                $serviciosAdicionalesRaw = $usuarioPublicate->servicios_adicionales;

                // Aplicamos el mismo proceso de limpieza
                $serviciosAdicionalesClean = str_replace('\\', '', $serviciosAdicionalesRaw);
                $serviciosAdicionalesClean = trim($serviciosAdicionalesClean, '"');

                $serviciosAdicionales = json_decode($serviciosAdicionalesClean, true);

                if (!is_array($serviciosAdicionales)) {
                $serviciosAdicionalesClean = preg_replace('/["\\\]+/', '', $serviciosAdicionalesRaw);
                $serviciosAdicionalesClean = trim($serviciosAdicionalesClean, '[]');
                $serviciosAdicionales = explode(',', $serviciosAdicionalesClean);
                }

                // Aplicamos las mismas correcciones de caracteres especiales
                $serviciosAdicionales = array_map(function($item) {
                $item = trim(str_replace(['\\', '"', '[', ']'], '', $item));
                $item = htmlspecialchars_decode($item, ENT_QUOTES);

                $replacements = [
                'u00f3' => 'ó',
                'u00e9' => 'é',
                'u00e1' => 'á',
                'u00ed' => 'í',
                'u00fa' => 'ú',
                'u00f1' => 'ñ',
                'Erotico' => 'Erótico',
                'Tantrico' => 'Tántrico',
                'prostatico' => 'prostático',
                'Lesbico' => 'Lésbico',
                'Orgias' => 'Orgías',
                'Trio' => 'Trío'
                ];

                return str_replace(array_keys($replacements), array_values($replacements), $item);
                }, $serviciosAdicionales);

                $serviciosAdicionales = array_filter($serviciosAdicionales, function($item) {
                return !empty($item);
                });
                @endphp
                @foreach($serviciosAdicionales as $servicioAdicional)
                <span class="escortperfil-service-item">{{ $servicioAdicional }}</span>
                @endforeach
            </div>
        </div>

        <div class="escortperfil-section">
            <h2 class="escortperfil-section-title">Videos</h2>
            @if(!empty($videos = json_decode($usuarioPublicate->videos)))
            <div class="escortperfil-videos-list">
                <div class="escortperfil-video-item">
                    <video class="escortperfil-video" controls preload="auto"
                        src="{{ asset('storage/chicas/'.$usuarioPublicate->id.'/videos/'.($videos[0] ?? '')) }}">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
            </div>

            <div class="swiper-pagination">
                @foreach($videos as $index => $video)
                <button class="swiper-pagination-bullet {{ $index === 0 ? 'swiper-pagination-bullet-active' : '' }}"
                    data-video="{{ asset('storage/chicas/'.$usuarioPublicate->id.'/videos/'.$video) }}">
                </button>
                @endforeach
            </div>
            @else
            <p class="text-center">No hay videos disponibles</p>
            @endif
        </div>

          <!-- Sección de Experiencias -->
                  <!-- Sección de Experiencias -->
        <div class="escortperfil-section">
            <h2 class="escortperfil-section-title">Experiencias</h2>
            @php
            $experiencias = \App\Models\Posts::where('id_blog', 16)
                ->where('chica_id', $usuarioPublicate->id)
                ->orderBy('created_at', 'desc')
                ->get();
            @endphp
            
            @if($experiencias->count() > 0)
                <div class="escortperfil-experiences-table">
                    <table>
                        <tbody>
                            @foreach($experiencias as $experiencia)
                            <tr class="escortperfil-experience-row">
                                <td class="escortperfil-experience-avatar-cell">
                                    @if($experiencia->usuario && $experiencia->usuario->foto)
                                        <img src="{{ asset('storage/' . $experiencia->usuario->foto) }}" 
                                             alt="Avatar de {{ $experiencia->usuario->name }}"
                                             class="escortperfil-experience-avatar-img">
                                    @else
                                        <div class="escortperfil-experience-avatar-default">
                                            {{ $experiencia->usuario ? strtoupper(substr($experiencia->usuario->name, 0, 1)) : 'A' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="escortperfil-experience-content-cell">
                                    <div class="escortperfil-experience-author">
                                        {{ $experiencia->usuario->name ?? 'Anónimo' }}
                                    </div>
                                    <a href="{{ route('post.show', ['id_blog' => $experiencia->id_blog, 'id' => $experiencia->id]) }}" 
                                       class="escortperfil-experience-title-link">
                                        <h3 class="escortperfil-experience-title">{{ $experiencia->titulo }}</h3>
                                    </a>
                                    <p class="escortperfil-experience-content">{{ $experiencia->contenido }}</p>
                                </td>
                                <td class="escortperfil-experience-date-cell">
                                    {{ \Carbon\Carbon::parse($experiencia->created_at)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">No hay experiencias disponibles</p>
            @endif
        </div>

    </div>
    @endsection
    @include('layouts.navigation')