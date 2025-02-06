@extends('layouts.app')
@section('content')

<style>
    .footer {
    color: #ffffff;
    text-align: center;
    /* padding: 10px 0; */
    font-size: 14px;
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 100;
    background-color: #1a1a1a;
    font-family: 'Montserrat', sans-serif;
}
</style>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow" style="font-family: 'Montserrat', sans-serif;">
        <h1 class="text-3xl font-bold mb-4 text-center" style="font-family: 'Montserrat', sans-serif;">RTA</h1>
        
        <p class="text-lg mb-4 text-gray-700" style="font-family: 'Montserrat', sans-serif;">
            OnlyEscorts está calificado con la etiqueta RTA. Padres, pueden bloquear fácilmente el acceso a este sitio. 
            Por favor, lean esta página <a href="http://www.rtalabel.org/index.php?content=parents" 
            class="text-blue-600 hover:text-blue-800 underline" target="_blank">
            http://www.rtalabel.org/index.php?content=parents</a> para más información.
        </p>
    </div>
</div>

{{-- Asegúrate de que Poppins esté cargada --}}
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
@endsection
@include('layouts.navigation')