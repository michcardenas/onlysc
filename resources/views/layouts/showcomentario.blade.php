@extends('layouts.app_foro')

@section('content')
<!-- Banner de la categoría -->
<div class="foro-banner">
    <div class="banner-img" style="background-image: url('{{ $categoria->foto ? asset('storage/' . $categoria->foto) : asset('storage/foros/default.jpg') }}')">
    </div>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            <h1>
                <span class="700">{{ $categoria->titulo }}</span>
            </h1>
        </div>
    </div>
</div>

<!-- Contenido del comentario -->
<div class="foro-comentario-container">
    <div class="foro-comentario-card">
        <div class="foro-comentario-avatar">
            <div class="foro-comentario-avatar-circle">
                {{ strtoupper(substr($comentario->nombre_usuario, 0, 1)) }}
            </div>
            <div class="foro-comentario-user-info">
                <strong>{{ $comentario->nombre_usuario }}</strong>
                <p class="foro-comentario-role">Miembro del foro</p>
            </div>
        </div>
        
        <div class="foro-comentario-content-wrapper">
            <div class="foro-comentario-header">
                <span class="foro-comentario-date">{{ \Carbon\Carbon::parse($comentario->created_at)->format('F d, Y') }}</span>
                <span class="foro-comentario-link">
                    Ver escort: <a href="https://onlyescorts.cl/escorts/tahia/" target="_blank">https://onlyescorts.cl/escorts/tahia/</a>
                </span>
            </div>
            
            <div class="foro-comentario-content">
                {{ $comentario->comentario }}
            </div>
        </div>
    </div>
</div>


<div class="foro-comentario-container">
    <div class="foro-comentario-form">
        <h4 class="foro-comentario-form-title">Dejar un comentario</h4>
        @auth
            <form action="{{ route('comentarios.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_blog" value="{{ $comentario->id_blog }}">
                
                <div class="foro-comentario-form-group">
                    <label for="comentario">Comentario</label>
                    <textarea 
                        name="comentario" 
                        id="comentario"
                        class="foro-comentario-textarea" 
                        rows="3" 
                        placeholder="Escribe un comentario..."
                        required
                    ></textarea>
                </div>
                
                <button type="submit" class="foro-comentario-btn">
                    Enviar
                </button>
            </form>
        @else
            <p class="mt-3">
                <a href="{{ route('login') }}">Inicia sesión</a> para dejar un comentario.
            </p>
        @endauth
    </div>
</div>
@endsection
