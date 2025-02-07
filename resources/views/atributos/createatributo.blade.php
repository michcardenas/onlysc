@extends('layouts.app_login')

@section('content')
<div class="container">
    <h1 style="color: #ff0f58;">Agregar Nuevo Atributo</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('atributos.store') }}" method="POST">
        @csrf

        <!-- Nombre del Atributo -->
        <div class="form-group">
            <label style="color: white;" for="nombre">Nombre del Atributo</label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control"
                value="{{ old('nombre') }}"
                required>
        </div>

        <!-- Después del campo nombre -->
        <div class="form-group">
            <label style="color: white;" for="url">URL del Atributo</label>
            <div style="display: flex; align-items: center;">
                <span style="color: white;">https://onlyescorts.cl/ciudad/</span>
                <input
                    type="text"
                    name="url"
                    id="url"
                    class="form-control"
                    style="margin-left: 10px; width: auto;"
                    value="{{ old('url', $atributo->url ?? '') }}"
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
            Guardar Atributo
        </button>
        <a href="{{ route('atributos.indexatributo') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection