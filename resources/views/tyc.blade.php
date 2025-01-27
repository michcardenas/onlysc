@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow" style="font-family: 'Montserrat', sans-serif;">
        <h1 class="text-3xl font-bold mb-4 text-center" style="font-family: 'Montserrat', sans-serif;">
            {{ $title }}
        </h1>
        <p class="text-lg mb-4 text-gray-700" style="font-family: 'Montserrat', sans-serif;">
            {!! nl2br(strip_tags($content)) !!}
        </p>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
@endsection

@include('layouts.navigation')