@extends('layouts.app_login')

@section('content')
<div class="container">
    <h1 style="color: #ff0f58;">Agregar Nuevo Sector</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('sectores.store') }}" method="POST">
        @csrf

        <!-- Nombre del Sector -->
        <div class="form-group">
            <label style="color: white;" for="nombre">Nombre del Sector</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control" 
                value="{{ old('nombre') }}" 
                required>
        </div>

        <!-- URL del Sector -->
        <div class="form-group">
            <label style="color: white;" for="url">URL del Sector</label>
            <div style="display: flex; align-items: center;">
                <span style="color: white;">https://onlyescorts.cl/ciudad/</span>
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

        <!-- Botones -->
        <button 
            type="submit" 
            class="btn" 
            style="background-color: #ff0f58; color: white; margin-top: 10px;">
            Guardar Sector
        </button>
        <a href="{{ route('sectores.indexsector') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection