@extends('layouts.app_login')

@section('content')
<div class="login-container">
    <!-- Logo y Título fuera de la caja -->
    <div class="logo-title-container">
        <div class="logo">
            <img class="only" src="{{ asset('images/logo_XL-2.png') }}" alt="Logo">
        </div>
        <h2 class="title">Restablecer Contraseña</h2>
    </div>

    <div class="login-box">
        <!-- Mostrar mensajes de error si existen -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder=" " value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                <label for="email" class="floating-label">Correo Electrónico <span class="required">*</span></label>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder=" " required autocomplete="new-password">
                <label for="password" class="floating-label">Nueva Contraseña <span class="required">*</span></label>

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder=" " required autocomplete="new-password">
                <label for="password-confirm" class="floating-label">Confirmar Contraseña <span class="required">*</span></label>
            </div>

            <!-- Botón de restablecer contraseña -->
            <div class="form-group">
                <button type="submit" class="btn login-btn">Restablecer Contraseña</button>
            </div>
        </form>

        <!-- Enlace para volver al login -->
        <div class="register-link">
            <p>¿Recordaste tu contraseña? <a href="{{ route('login') }}" class="register-now">Iniciar Sesión</a></p>
        </div>
    </div>
</div>
@endsection
