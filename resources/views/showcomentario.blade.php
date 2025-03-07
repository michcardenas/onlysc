@extends('layouts.app_foro')
@section('content')

@if(isset($foro))
<div class="foro-banner">
  <div class="banner-img" style="background-image: url('{{ $foro->foto ? asset('storage/' . $foro->foto) : asset('storage/foros/default.jpg') }}')"></div>
  <div class="foro-banner-content">
      <div class="foro-texto_banner">
          <h1><span class="700">{{ $foro->titulo ?? 'Foro' }}</span></h1>
      </div>
  </div>
</div>
@endif

@if(isset($comentarios) && $comentarios->count() > 0)
<div class="foro-comentarios-lista">
  @foreach($comentarios as $comentario)
  <div class="foro-comentario-container">
      <div class="foro-comentario-card">
          <div class="foro-comentario-avatar">
              @if(isset($comentario->usuario) && !empty($comentario->usuario->foto))
              <img src="{{ asset('storage/' . $comentario->usuario->foto) }}"
                  alt="Avatar de {{ $comentario->usuario->name }}"
                  class="foro-comentario-avatar-circle">
              @else
              <div class="foro-comentario-avatar-circle">
                  {{ isset($comentario->usuario->name) ? strtoupper(substr($comentario->usuario->name, 0, 1)) : 'A' }}
              </div>
              @endif
              <div class="foro-comentario-user-info">
                  <strong>{{ $comentario->usuario->name ?? 'Anónimo' }}</strong>
                  <p class="foro-comentario-role">
                      @if(isset($comentario->usuario->rol))
                          {{ $comentario->usuario->rol == 1 ? 'Administrador' : 
                             ($comentario->usuario->rol == 2 ? 'Chica' : 
                             ($comentario->usuario->rol == 3 ? 'Usuario' : 'Miembro del foro')) }}
                      @else
                          Miembro del foro
                      @endif
                  </p>
              </div>
          </div>
          <div class="foro-comentario-content-wrapper">
    <div class="foro-comentario-header">
        <span class="foro-comentario-date">
            {{ \Carbon\Carbon::parse($comentario->created_at)->format('d/m/Y H:i') }}
        </span>
        @if($comentario->id_blog == 16 && $loop->first)
            @php
                $post = App\Models\Posts::with('chica')->find($comentario->id_post);
            @endphp
            @if($post && $post->chica)
                <div class="chica-url">
                    <a href="{{ route('perfil.show', ['nombre' => $post->chica->fantasia . '-' . $post->chica->id]) }}" class="chica-link">
                        Perfil de {{ $post->chica->fantasia }}
                    </a>
                </div>
            @endif
        @endif
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

@if(isset($post))
<div class="foro-comentario-container">
  <div class="foro-comentario-form">
      <h4 class="foro-comentario-form-title">Dejar un comentario</h4>
      @auth
      <form action="{{ route('comentario.store') }}" method="POST">
          @csrf
          <input type="hidden" name="id_blog" value="{{ $post->id_blog }}">
          <input type="hidden" name="id_post" value="{{ $post->id }}">
          <div class="foro-comentario-form-group">
              <label for="comentario">Comentario</label>
              <textarea name="comentario" id="comentario" 
                  class="foro-comentario-textarea @error('comentario') is-invalid @enderror"
                  rows="3" placeholder="Escribe un comentario..." required></textarea>
              @error('comentario')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <button type="submit" class="foro-comentario-btn">Enviar comentario</button>
      </form>
      @else
      <p class="mt-3">
          <a href="{{ route('login') }}">Inicia sesión</a> para dejar un comentario.
      </p>
      @endauth
  </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
  {{ session('success') }}
</div>
@endif

@endsection
@include('layouts.navigation')