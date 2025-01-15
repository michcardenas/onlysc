@extends('layouts.app_login')

@section('content')
<div class="container">
    <h1 style="color: #ff0f58;">Agregar Nueva Ciudad</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('ciudades.store') }}" method="POST">
        @csrf

        <!-- Nombre de la Ciudad -->
        <div class="form-group">
            <label style="color: white;" for="nombre">Nombre de la Ciudad</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre') }}" 
                required>
        </div>

        <!-- URL de la Ciudad -->
        <div class="form-group">
            <label style="color: white;" for="url">URL de la Ciudad</label>
            <div style="display: flex; align-items: center;">
                <span style="color: white;">https://onlyescorts.cl/</span>
                <input 
                    type="text" 
                    name="url" 
                    id="url" 
                    class="form-control" 
                    style="margin-left: 10px; width: auto;" 
                    value="{{ old('url', '') }}" 
                    placeholder="Añadir el resto de la URL" 
                    required>
            </div>
        </div>

        <!-- Campo Select para la Zona -->
        <div class="form-group">
            <label style="color: white;" for="zona">Zona</label>
            <select 
                name="zona" 
                id="zona" 
                class="form-control" 
                required>
                <option value="" disabled selected>Selecciona una zona</option>
                <option value="Zona Norte" {{ old('zona') == 'Zona Norte' ? 'selected' : '' }}>Zona Norte</option>
                <option value="Zona Centro" {{ old('zona') == 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                <option value="Zona Sur" {{ old('zona') == 'Zona Sur' ? 'selected' : '' }}>Zona Sur</option>
            </select>
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
            Guardar Ciudad
        </button>
        <a href="{{ route('ciudades.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
