@extends('layouts.app_login')

@section('content')
<div class="container">
    <h1 style="color: #ff0f58;">Editar Nacionalidad</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Cambiar la acción para enviar el ID -->
    <form action="{{ route('nacionalidades.update', $nacionalidad->id) }}" method="POST">
        @csrf
        @method('PUT')  <!-- Este método es importante para la actualización -->

        <!-- Nombre de la Nacionalidad -->
        <div class="form-group">
            <label style="color: white;" for="nombre">Nombre de la Nacionalidad</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre', $nacionalidad->nombre) }}" 
                required>
        </div>

        <!-- URL -->
        <div class="form-group">
            <label style="color: white;" for="url">URL</label>
            <input 
                type="text" 
                name="url" 
                id="url" 
                class="form-control" 
                value="{{ old('url', $nacionalidad->url) }}" 
                placeholder="Ejemplo: nombre-nacionalidad" 
                required>
        </div>

        <!-- Campo Numérico para la Posición -->
        <div class="form-group">
            <label style="color: white;" for="posicion">Posición</label>
            <input 
                type="number" 
                name="posicion" 
                id="posicion" 
                class="form-control" 
                value="{{ old('posicion', $nacionalidad->posicion) }}" 
                placeholder="Ejemplo: 1, 2, 3..." 
                required>
        </div>

        <!-- Botones -->
        <button 
            type="submit" 
            class="btn" 
            style="background-color: #ff0f58; color: white; margin-top: 10px;">
            Guardar Nacionalidad
        </button>
        <a href="{{ route('nacionalidades.indexnacionalidad') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
