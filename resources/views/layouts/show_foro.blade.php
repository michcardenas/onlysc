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
    {!! $categoria->contenido !!}
</div>

<!-- Contenido del Foro -->
@if(isset($categoria->foros) && count($categoria->foros) > 0)
    @foreach($categoria->foros as $foro)
    <!-- Tabla de Posts -->
    <div class="foro-comments-table">
        <table>
            <thead>
                <!-- Encabezado de la tabla si es necesario -->
            </thead>
            <tbody>
                @php
                $posts = DB::table('posts as p')
                    ->select('p.*', 'u.name as nombre_usuario')
                    ->leftJoin('users as u', 'p.id_usuario', '=', 'u.id')
                    ->where('p.id_blog', $foro->id_blog)
                    ->orderBy('p.created_at', 'desc')
                    ->get();
                @endphp

                @if(count($posts) > 0)
                    @foreach($posts as $post)
                    <tr class="foro-comments-row">
                        <td class="foro-comments-avatar-cell">
                            <div class="foro-comments-avatar">
                                {{ strtoupper(substr($post->nombre_usuario, 0, 1)) }}
                            </div>
                        </td>

                        <td class="foro-comments-topic">
                            <div class="foro-comments-content">
                                <a href="{{ route('post.show', ['id_blog' => $post->id_blog, 'id' => $post->id]) }}">
                                    {{ $post->titulo }}
                                </a>
                            </div>
                            <div class="foro-comments-author-date">
                                <small>{{ $post->nombre_usuario }} | {{ \Carbon\Carbon::parse($post->created_at)->format('d M Y') }}</small>
                            </div>
                        </td>

                        <td class="foro-comments-last">
                            <div class="foro-comments-date">
                                {{ \Carbon\Carbon::parse($post->created_at)->format('d M Y') }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center py-8">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-500">No hay posts disponibles en este momento</p>
                                <p class="text-sm text-gray-400">¡Sé el primero en crear una discusión!</p>
                                @auth
                                    <a href="{{ route('foroadmin.createpost', ['id_blog' => $foro->id_blog]) }}" 
                                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Crear nuevo post
                                    </a>
                                @endauth
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endforeach
@else
    <div class="text-center py-8">
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <p class="text-lg font-medium text-gray-500">No hay foros en esta categoría</p>
            <p class="text-sm text-gray-400">Los foros se añadirán próximamente</p>
        </div>
    </div>
@endif
@endsection