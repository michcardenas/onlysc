@extends('layouts.app_foro')

@section('content')
<div class="foro-banner" style="background-image: url('{{ asset('images/default.jpg') }}')">
   <h1>{{ $categoria->titulo }}</h1>
</div>

<div class="foro-breadcrumb">
   <a href="{{ route('inicio') }}">Inicio</a> /
   <a href="{{ route('foro') }}">Foros</a> /
   <span>{{ $categoria->titulo }}</span>
</div>

<div class="foro-description mb-4">
   <p>{{ $categoria->descripcion }}</p>
</div>

<!-- Lista de foros de esta categoría -->
@if(isset($categoria->foros) && count($categoria->foros) > 0)
    @foreach($categoria->foros as $foro)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">{{ $foro->titulo }}</h3>
                <small>Por: {{ $foro->nombre_usuario }}</small>
            </div>

            <div class="card-body">
                @if($foro->foto)
                    <img src="{{ asset('images/' . $foro->foto) }}" class="img-fluid mb-3" alt="Imagen del foro">
                @endif

                <p>{{ $foro->contenido }}</p>

                <!-- Sección de comentarios -->
                <div class="comentarios-section mt-4">
                    <h4>Comentarios</h4>
                    
                    @php
                        $comentarios = DB::table('comentarios as c')
                            ->select('c.*', 'u.name as nombre_usuario')
                            ->leftJoin('users as u', 'c.id_usuario', '=', 'u.id')
                            ->where('c.id_blog', $foro->id_blog)
                            ->orderBy('c.created_at', 'desc')
                            ->get();
                    @endphp

                    <!-- Lista de comentarios -->
                    @if(count($comentarios) > 0)
                        @foreach($comentarios as $comentario)
                            <div class="comentario mb-3">
                                <p class="mb-1">{{ $comentario->comentario }}</p>
                                <small class="text-muted">
                                    Por: {{ $comentario->nombre_usuario }} - 
                                    {{ \Carbon\Carbon::parse($comentario->created_at)->diffForHumans() }}
                                </small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No hay comentarios aún.</p>
                    @endif

                    <!-- Formulario de comentarios -->
                    @auth
                        <form action="{{ route('comentarios.store') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="id_blog" value="{{ $foro->id_blog }}">
                            
                            <div class="form-group">
                                <textarea 
                                    name="comentario" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Escribe un comentario..."
                                    required
                                ></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-2">
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
        </div>
    @endforeach
@else
    <div class="alert alert-info">
        No hay foros disponibles en esta categoría.
    </div>
@endif
@endsection