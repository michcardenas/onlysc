@extends('layouts.app_foro')

@section('content')
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

<div class="foro-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="foro-breadcrumb-list">
            <li class="foro-breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
            <li class="foro-breadcrumb-item"><a href="{{ route('foro') }}">Foros</a></li>
            <li class="foro-breadcrumb-item active">{{ $categoria->titulo }}</li>
        </ol>
    </nav>
</div>

<div class="foro-description mb-4">
    <p>{{ $categoria->descripcion }}</p>
</div>

<!-- Contenido del Foro -->
@if(isset($categoria->foros) && count($categoria->foros) > 0)
@foreach($categoria->foros as $foro)
<!-- Tabla de Comentarios -->
<div class="foro-comments-table">
    <table>
        <thead>
            <!-- Encabezado de la tabla si es necesario -->
        </thead>
        <tbody>
            @php
            $comentarios = DB::table('comentarios as c')
            ->select('c.*', 'u.name as nombre_usuario')
            ->leftJoin('users as u', 'c.id_usuario', '=', 'u.id')
            ->where('c.id_blog', $foro->id_blog)
            ->orderBy('c.created_at', 'desc')
            ->get();
            @endphp

            @if(count($comentarios) > 0)
            @foreach($comentarios as $comentario)
            <tr class="foro-comments-row">
                <!-- Avatar del autor -->
                <td class="foro-comments-avatar-cell">
                    <div class="foro-comments-avatar">
                        {{ strtoupper(substr($comentario->nombre_usuario, 0, 1)) }}
                    </div>
                </td>

                <!-- Contenido del comentario con autor y enlace -->
                <td class="foro-comments-topic">
                    <div class="foro-comments-content">
                        <a href="{{ route('comentario.show', ['id_blog' => $comentario->id_blog, 'id' => $comentario->id]) }}">
                            {{ $comentario->comentario }}
                        </a>
                    </div>
                    <div class="foro-comments-author-date">
                        <small>{{ $comentario->nombre_usuario }} | {{ \Carbon\Carbon::parse($comentario->created_at)->format('d M Y') }}</small>
                    </div>
                </td>

                <!-- Fecha del comentario -->
                <td class="foro-comments-last">
                    <div class="foro-comments-date">
                        {{ \Carbon\Carbon::parse($comentario->created_at)->format('d M Y') }}
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="3" class="text-center">No hay comentarios aún.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endforeach
@else
<p>No hay foros en esta categoría.</p>
@endif
@endsection