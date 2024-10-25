@extends('layouts.app_foro')

<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

@section('content')
<div class="foro-banner" style="background-image: url('{{ asset('images/'.$categoria->imagen) }}')">
   <h1>{{ $categoria->titulo }}</h1>
</div>

<div class="foro-breadcrumb">
   <a href="{{ route('inicio') }}">Inicio</a> /
   <a href="{{ route('foro') }}">Foros</a> /
   <span>{{ $categoria->titulo }}</span>
</div>

<div class="foro-description">
   <p>{{ $categoria->descripcion }}</p>
</div>
@endsection