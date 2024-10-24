@extends('layouts.app_login')

@section('content')
<div class="login-container">
    <!-- Logo y Título fuera de la caja -->
    <div class="logo-title-container">
        <div class="logo">
            <img class="only" src="{{ asset('images/logo_XL-2.png') }}" alt="Logo">
        </div>
        <h2 class="title">Registrarse</h2>
    </div>

    <div class="login-box">
        <!-- Mostrar el mensaje de error si las credenciales no son correctas -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <input id="name" type="text" class="form-control" name="name" placeholder=" " value="{{ old('name') }}" required autofocus>
                <label for="name" class="floating-label">Nombre <span class="required">*</span></label>
            </div>

            <div class="form-group">
                <input id="email" type="email" class="form-control" name="email" placeholder=" " value="{{ old('email') }}" required>
                <label for="email" class="floating-label">Correo Electrónico <span class="required">*</span></label>
            </div>

            <div class="form-group">
                <input id="password" type="password" class="form-control" name="password" placeholder=" " required>
                <label for="password" class="floating-label">Contraseña <span class="required">*</span></label>
            </div>

            <div class="form-group">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder=" " required>
                <label for="password-confirm" class="floating-label">Confirmar Contraseña <span class="required">*</span></label>
            </div>

            <!-- Botón de Registrarse -->
            <div class="form-group">
                <button type="submit" class="btn login-btn">Registrarse</button>
            </div>

        </form>

        <!-- Sección de "Ya tienes una cuenta" -->
        <div class="register-link">
            <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="register-now">Iniciar Sesión</a></p>
        </div>
    </div>
</div>
@endsection
