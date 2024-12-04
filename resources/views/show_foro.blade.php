@extends('layouts.app_foro')

@section('content')
{{-- Banner del Foro --}}
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

{{-- Breadcrumb --}}
<div class="foro-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="foro-breadcrumb-list">
            <li class="foro-breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
            <li class="foro-breadcrumb-item"><a href="{{ route('foro') }}">Foros</a></li>
            <li class="foro-breadcrumb-item active">{{ $categoria->titulo }}</li>
        </ol>
    </nav>
</div>

{{-- Descripción del Foro --}}
<div class="foro-description mb-4">
    {!! $categoria->contenido !!}
</div>

@if(isset($categoria->foros) && count($categoria->foros) > 0)
@foreach($categoria->foros as $foro)
<div class="foro-comments-table">
    <table>
        <thead>
        </thead>
        <tbody>
            @php
            $posts = DB::table('posts as p')
            ->select(
                'p.*',
                'u.name as nombre_usuario',
                'u.foto as user_foto', // Añadida la foto del usuario
                'u.rol as user_rol',   // Añadido el rol del usuario
                DB::raw('(SELECT COUNT(*) FROM comentario WHERE id_post = p.id) as comentarios'),
                'p.visitas'
            )
            ->leftJoin('users as u', 'p.id_usuario', '=', 'u.id')
            ->where('p.id_blog', $foro->id_blog)
            ->orderBy('p.is_fixed', 'desc')
            ->orderBy('p.created_at', 'desc')
            ->get()
            ->map(function($post) {
                $post->usuario = new \stdClass();
                $post->usuario->name = $post->nombre_usuario ?? 'Anónimo';
                $post->usuario->foto = $post->user_foto;
                $post->usuario->rol = $post->user_rol;
                unset($post->user_foto);
                unset($post->user_rol);
                return $post;
            });
            @endphp

            @if(count($posts) > 0)
            @foreach($posts as $post)
            <tr class="foro-comments-row {{ $post->is_fixed ? 'foro-fixed-post' : '' }}">
                {{-- Celda del Avatar --}}
                <td class="foro-comments-avatar-cell" width="60">
                    <div class="foro-comments-avatar">
                        @if(isset($post->usuario) && $post->usuario->foto)
                            <img src="{{ asset('storage/' . $post->usuario->foto) }}" 
                                 alt="Avatar de {{ $post->usuario->name }}"
                                 class="foro-comments-avatar-img">
                        @else
                            <div class="foro-comments-avatar-circle">
                                {{ strtoupper(substr($post->usuario->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </td>

                {{-- Celda del Contenido Principal --}}
                <td class="foro-comments-topic">
                    <div class="foro-comments-content">
                        @if($post->is_fixed)
                        <i class="fas fa-thumbtack fixed-pin"></i>
                        @endif
                        <a href="{{ route('post.show', ['id_blog' => $post->id_blog, 'id' => $post->id]) }}">
                            {{ $post->titulo }}
                        </a>
                    </div>
                    <div class="foro-comments-author-date">
                        {{ $post->usuario->name }} | {{ \Carbon\Carbon::parse($post->created_at)->format('d M Y') }}
                    </div>
                </td>

                {{-- Celda de Estadísticas --}}
                <td class="foro-comments-stats-cell" width="150">
                    <div class="stats-container">
                        <div class="stats-row">
                            <label>Comentarios:</label>
                            <div class="value">{{ $post->comentarios }}</div>
                        </div>
                        <div class="stats-row">
                            <label>Visitas:</label>
                            <div class="value">{{ $post->visitas }}</div>
                        </div>
                    </div>
                </td>

                {{-- Celda de Usuario y Fecha --}}
                <td class="foro-comments-author-cell" width="200">
                    <div class="text-right">
                        <div class="post-date">{{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}</div>
                        <div class="user-name">{{ $post->usuario->name }}</div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="4" class="text-center py-8">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <div class="text-gray-400">
                            <i class="fas fa-comments fa-3x"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-500">No hay posts disponibles en este momento</p>
                        <p class="text-sm text-gray-400">¡Sé el primero en crear una discusión!</p>
                    </div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endforeach
@endif

@endsection