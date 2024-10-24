<!-- inicio.blade.php -->

@extends('layouts.app')

@section('content')
    <h1>Ciudades</h1>
    <ul>
        @foreach($ciudades as $ciudad)
            <li>{{ $ciudad->nombre }}</li>
        @endforeach
    </ul>
@endsection
