@extends('layouts.app_login')

@section('content')
<div class="container">
    <h1 style="color: #ff0f58;">Agregar Nuevo Servicio</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('servicios.store') }}" method="POST">
        @csrf

        <!-- Nombre del Servicio -->
        <div class="form-group">
            <label style="color: white;" for="nombre">Nombre del Servicio</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre') }}" 
                required>
        </div>

        <div class="form-group">
            <label style="color: white;" for="url">URL del Servicio</label>
            <div style="display: flex; align-items: center;">
                <span style="color: white;">https://onlyescorts.cl/ciudad/</span>
                <input
                    type="text"
                    name="url"
                    id="url"
                    class="form-control"
                    style="margin-left: 10px; width: auto;"
                    value="{{ old('url', $servicio->url ?? '') }}"
                    required>
            </div>
        </div>


        <!-- Campo Numérico para la Posición -->
        <div class="form-group">
            <label style="color: white;" for="posicion">Posición</label>
            <input 
                type="number" 
                name="posicion" 
                id="posicion" 
                class="form-control" 
                value="{{ old('posicion') }}" 
                placeholder="Ejemplo: 1, 2, 3..." 
                required>
        </div>

        <!-- Botones -->
        <button 
            type="submit" 
            class="btn" 
            style="background-color: #ff0f58; color: white; margin-top: 10px;">
            Guardar Servicio
        </button>
        <a href="{{ route('servicios.indexservicio') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection