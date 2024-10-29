@extends('layouts.app_login')
@section('content')
<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p>Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 1 ? 'Administrador' : 'Usuario' }})</p>
        </div>
    </nav>
</header>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Foro</h2>

            <form action="{{ route('foroadmin.update', $foro->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="id_blog" class="block text-sm font-medium text-gray-700">ID del Blog</label>
                    <select name="id_blog" id="id_blog" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="1" {{ $foro->id_blog == 1 ? 'selected' : '' }}>Conversaciones</option>
                        <option value="2" {{ $foro->id_blog == 2 ? 'selected' : '' }}>Gentlemen</option>
                        <option value="3" {{ $foro->id_blog == 3 ? 'selected' : '' }}>Experiencias</option>
                    </select>
                    @error('id_blog')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" 
                           name="titulo" 
                           id="titulo" 
                           value="{{ old('titulo', $foro->titulo) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="subtitulo" class="block text-sm font-medium text-gray-700">Subtítulo</label>
                    <input type="text" 
                           name="subtitulo" 
                           id="subtitulo" 
                           value="{{ old('subtitulo', $foro->subtitulo) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                    @error('subtitulo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="contenido" class="block text-sm font-medium text-gray-700">Contenido</label>
                    <textarea name="contenido" 
                              id="contenido" 
                              rows="6"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              required>{{ old('contenido', $foro->contenido) }}</textarea>
                    @error('contenido')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="foto" class="block text-sm font-medium text-gray-700">Foto</label>
                    @if($foro->foto)
                        <div class="mt-2 mb-4">
                            <img src="{{ asset('storage/' . $foro->foto) }}" 
                                 alt="Foto actual" 
                                 class="w-32 h-32 object-cover rounded">
                            <p class="mt-1 text-sm text-gray-500">Foto actual</p>
                        </div>
                    @endif
                    <input type="file" 
                           name="foto" 
                           id="foto"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100">
                    @error('foto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('foroadmin') }}" 
                       class="text-gray-500 hover:text-gray-700 font-medium">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection
