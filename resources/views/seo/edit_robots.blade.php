@extends('layouts.app_login')


@section('content')
<div class="container">
    <h1>Editar Robots.txt</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('seo.update_robots') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="content">Contenido de robots.txt:</label>
            <textarea name="content" id="content" rows="10" class="form-control" required>{{ old('content', $content) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
@endsection
