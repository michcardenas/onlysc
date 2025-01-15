@extends('layouts.app_login')

@section('content')
<style>
    h1 {
        color: #ff0f58; /* Color para títulos */
    }

    label {
        color: #ff0f58; /* Color para etiquetas de formulario */
    }

    body {
        color: #ff0f58; /* Color para el texto general */
    }

    .form-control {
        border: 1px solid #ff0f58; /* Borde de los inputs */
        color: #333; /* Color del texto dentro de los inputs */
    }

    .btn-primary {
        background-color: #ff0f58;
        border: none;
    }

    .btn-primary:hover {
        background-color: #e5094f;
    }

    .url-container {
        display: flex;
        align-items: center;
    }

    .url-container span {
        margin-right: 10px;
        color: #ff0f58;
    }
</style>

<div class="container">
    <h1>Editar Ciudad</h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('ciudades.update', $ciudad->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre de la Ciudad</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre', $ciudad->nombre) }}" 
                required>
        </div>

        <div class="form-group">
            <label for="url">URL de la Ciudad</label>
            <div class="url-container">
                <span>https://onlyescorts.cl/</span>
                <input 
                    type="text" 
                    name="url" 
                    id="url" 
                    class="form-control" 
                    style="width: auto;" 
                    value="{{ old('url', $ciudad->url) }}" 
                    placeholder="Ejemplo: santiago" 
                    required>
            </div>
        </div>

        <!-- Campo Select para la Zona -->
        <div class="form-group">
            <label for="zona">Zona</label>
            <select 
                name="zona" 
                id="zona" 
                class="form-control" 
                required>
                <option value="" disabled selected>Selecciona una zona</option>
                <option value="Zona Norte" {{ old('zona', $ciudad->zona) == 'Zona Norte' ? 'selected' : '' }}>Zona Norte</option>
                <option value="Zona Centro" {{ old('zona', $ciudad->zona) == 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                <option value="Zona Sur" {{ old('zona', $ciudad->zona) == 'Zona Sur' ? 'selected' : '' }}>Zona Sur</option>
            </select>
        </div>

        <!-- Campo Numérico para la Posición -->
        <div class="form-group">
            <label for="posicion">Posición</label>
            <input 
                type="number" 
                name="posicion" 
                id="posicion" 
                class="form-control" 
                value="{{ old('posicion', $ciudad->posicion) }}" 
                placeholder="Ejemplo: 1, 2, 3..." 
                required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        <a href="{{ route('ciudades.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
