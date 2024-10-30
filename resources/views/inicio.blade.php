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
                            <h3 class="inicio-card-title">{{ $usuario->fantasia }}</h3>
                            <p class="inicio-card-location">{{ $usuario->ubicacion }}</p>
                            <p class="inicio-card-age">{{ $usuario->edad }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

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
        <div class="inicio-featured-overlay">
            <h3 class="inicio-featured-title">{{ $usuarioDestacado->fantasia }}</h3>
            <p class="inicio-featured-location">{{ $usuarioDestacado->ubicacion }}</p>
            <p class="inicio-featured-age">{{ $usuarioDestacado->edad }}</p>
        </div>
    </div>
</div>

        @endif
    </section>
</main>
@endsection



