@extends('layouts.app_foro')
@section('content')
<!-- Banner del foro -->
@if(isset($foro))
<div class="foro-banner">
    <div class="banner-img" style="background-image: url('{{ $foro->foto ? asset('storage/' . $foro->foto) : asset('storage/foros/default.jpg') }}')">
    </div>
    <div class="foro-banner-content">
        <div class="foro-texto_banner">
            <h1>
                <span class="700">{{ $foro->titulo ?? 'Foro' }}</span>
            </h1>
        </div>
    </div>
</div>
@endif

<!-- Lista de comentarios existentes -->
@if(isset($comentarios) && $comentarios->count() > 0)
<div class="foro-comentarios-lista">
    @foreach($comentarios as $comentario)
    <div class="foro-comentario-container">
        <div class="foro-comentario-card">
            <div class="foro-comentario-avatar">
                <div class="foro-comentario-avatar-circle">
                    {{ strtoupper(substr($comentario->usuario->name ?? 'A', 0, 1)) }}
                </div>
                <div class="foro-comentario-user-info">
                    <strong>{{ $comentario->usuario->name ?? 'Anónimo' }}</strong>
                    <p class="foro-comentario-role">Miembro del foro</p>
                </div>
            </div>

            <div class="foro-comentario-content-wrapper">
                <div class="foro-comentario-header">
                    <span class="foro-comentario-date">
                        {{ \Carbon\Carbon::parse($comentario->created_at)->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div class="foro-comentario-content">
                    {{ $comentario->comentario }}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="foro-sin-comentarios">
    <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
</div>
@endif


<!-- Formulario para nuevo comentario -->
<div class="foro-comentario-container">
    <div class="foro-comentario-form">
        <h4 class="foro-comentario-form-title">Dejar un comentario</h4>
        @auth
        <form action="{{ route('comentario.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_blog" value="{{ $foro->id ?? '' }}">

            <div class="foro-comentario-form-group">
                <label for="comentario">Comentario</label>
                <textarea
                    name="comentario"
                    id="comentario"
                    class="foro-comentario-textarea @error('comentario') is-invalid @enderror"
                    rows="3"
                    placeholder="Escribe un comentario..."
                    required></textarea>
                @error('comentario')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="foro-comentario-btn">
                Enviar comentario
            </button>
        </form>
        @else
        <p class="mt-3">
            <a href="{{ route('login') }}">Inicia sesión</a> para dejar un comentario.
        </p>
        @endauth
    </div>
</div>


@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
@endsection