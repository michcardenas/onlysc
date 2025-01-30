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
    <h1>Editar Sector</h1>

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
    
    <form action="{{ route('sectores.update', $sector->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre del Sector</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre', $sector->nombre) }}" 
                required>
        </div>

        <div class="form-group">
            <label for="url">URL del Sector</label>
            <div class="url-container">
                <span>https://onlyescorts.cl/</span>
                <input 
                    type="text" 
                    name="url" 
                    id="url" 
                    class="form-control" 
                    style="width: auto;" 
                    value="{{ old('url', $sector->url) }}" 
                    required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        <a href="{{ route('sectores.indexsector') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection