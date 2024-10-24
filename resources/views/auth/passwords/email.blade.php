@extends('layouts.app_login')

@section('content')
<div class="login-container">
    <!-- Logo y Título fuera de la caja -->
    <div class="logo-title-container">
        <div class="logo">
            <img class="only" src="{{ asset('images/logo_XL-2.png') }}" alt="Logo">
        </div>
        <h5 class="title">Restablecer Contraseña</h5>
    </div>

    <div class="login-box">
        <!-- Mostrar el mensaje de error si hay errores -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Formulario para enviar el enlace de restablecimiento -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <input id="email" type="email" class="form-control" name="email" placeholder=" " value="{{ old('email') }}" required autofocus>
                <label for="email" class="floating-label">Correo Electrónico <span class="required">*</span></label>
            </div>

            <!-- Botón de Enviar -->
            <div class="form-group">
                <button type="submit" class="btn login-btn">Enviar Enlace de Restablecimiento</button>
            </div>
        </form>

        <!-- Sección de "Regresar al Login" -->
        <div class="register-link">
            <p>¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="register-now">Iniciar Sesión</a></p>
        </div>
    </div>

    <!-- Mostrar el mensaje de éxito si se envió el enlace debajo del contenedor -->
    @if (session('status'))
        <div class="status-message alert alert-success" style="background-color: #ff6699; color: white; text-align: center; padding: 10px; margin-top: 20px; border-radius: 5px; font-size: 14px; max-width: 400px; margin: 20px auto;">
            Hemos enviado un enlace para restablecer tu contraseña a tu correo electrónico.
        </div>
    @endif
</div>
@endsection
