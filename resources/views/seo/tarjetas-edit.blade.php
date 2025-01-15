@extends('layouts.app_login')

@section('content')
<div class="container mt-4">
    <h2 class="text-white">Editar Tarjeta</h2>
    <form action="{{ route('tarjetas.update', $tarjeta->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="titulo" class="text-white">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ $tarjeta->titulo }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion" class="text-white">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required>{{ $tarjeta->descripcion }}</textarea>
        </div>

        <div class="form-group">
            <label for="link" class="text-white">Enlace</label>
            <input type="url" name="link" id="link" class="form-control" value="{{ $tarjeta->link }}" required>
        </div>

        <div class="form-group">
    <label for="imagen" class="text-white">Imagen</label>

    <!-- Mostrar la imagen actual si existe -->
    @if($tarjeta->imagen)
        <div class="mb-2">
            <p class="text-white">Imagen actual:</p>
            <img src="{{ asset('images/' . $tarjeta->imagen) }}" alt="Imagen actual" style="max-width: 150px; border-radius: 8px;">
        </div>
    @endif

    <!-- Campo para cargar una nueva imagen -->
    <input type="file" name="imagen" id="imagen" class="form-control">
    <small class="text-muted">Deja este campo vacío si no deseas cambiar la imagen.</small>
</div>


        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="{{ route('tarjetas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
