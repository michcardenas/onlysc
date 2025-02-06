@extends('layouts.app_login') 

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Descripciones de Fotos de {{ $usuario->nombre }}</h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.fotos.updateDescripcion', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($fotos as $index => $foto)
                <div class="col mb-4">
                    <div class="card h-100">
                        @php
                            $fotoPath = asset("storage/chicas/{$usuario->id}/{$foto}");
                            $posicion = 'center'; // Si necesitas una posición personalizada
                        @endphp

                        <div class="card-img-top"
                             style="background-image: url('{{ $fotoPath }}');
                                    background-position: {{ $posicion }};
                                    background-size: cover;
                                    width: 100%;
                                    height: 200px;">
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Foto {{ $index + 1 }}</h5>
                            <div class="form-group">
                                <label for="descripcion-{{ $index }}">Descripción</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="descripcion-{{ $index }}"
                                    name="descripciones[]"
                                    placeholder="Ingresa la descripción de esta foto"
                                    value="{{ $descripcionFotos[$foto] ?? '' }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">
            Guardar Descripciones
        </button>
    </form>
</div>
@endsection