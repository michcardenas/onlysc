@extends('layouts.app_login')

@section('content')
<style>
    h1 {
        color: #ff0f58;
    }

    label {
        color: #ff0f58;
    }

    body {
        color: #ff0f58;
    }

    .form-control {
        border: 1px solid #ff0f58;
        color: #333;
    }

    .btn-primary {
        background-color: #ff0f58;
        border: none;
    }

    .btn-primary:hover {
        background-color: #e5094f;
    }
</style>

<div class="container">
    <h1>Editar Servicio</h1>

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

    <form action="{{ route('seo.servicios.update', $servicio->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre del Servicio</label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control"
                value="{{ old('nombre', $servicio->nombre) }}"
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

        <div class="form-group">
            <label for="posicion">Posición</label>
            <input
                type="number"
                name="posicion"
                id="posicion"
                class="form-control"
                value="{{ old('posicion', $servicio->posicion) }}"
                placeholder="Ejemplo: 1, 2, 3..."
                required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        <a href="{{ route('servicios.indexservicio') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection