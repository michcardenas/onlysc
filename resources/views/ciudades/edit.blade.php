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

        <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        <a href="{{ route('ciudades.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
