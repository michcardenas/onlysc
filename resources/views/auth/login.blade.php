@extends('layouts.app_login')

@section('content')
<div class="login-container">
    <!-- Logo y Título fuera de la caja -->
    <div class="logo">
        <img class="only" src="{{ asset('images/logo_XL-2.png') }}" alt="Logo">
    </div>
    <h2 class="title">Mi cuenta</h2>

    <div class="login-box">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <input id="email" type="email" class="form-control" name="email" placeholder=" " value="{{ old('email') }}" required autofocus>
                <label for="email" class="floating-label">Nombre de usuario <span class="required">*</span></label>
            </div>

            <div class="form-group">
                <input id="password" type="password" class="form-control" name="password" placeholder=" " required>
                <label for="password" class="floating-label">Contraseña <span class="required">*</span></label>
            </div>






            <a href="{{ route('password.request') }}" class="forgot-link">Olvidaste tu contraseña</a>

            <!-- Botón de Acceder -->
            <div class="form-group">
                <button type="submit" class="btn login-btn">Acceder</button>
            </div>

            <!-- Recordarme -->
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> Recordarme
                    </label>
                </div>
            </div>
        </form>

        <!-- Sección de "Registrarse ahora" -->
        <div class="register-link">
            <p>¿Primera vez en Only Escorts? <a href="{{ route('register') }}" class="register-now">Registrarse ahora</a></p>
        </div>
    </div>
</div>
@endsection
