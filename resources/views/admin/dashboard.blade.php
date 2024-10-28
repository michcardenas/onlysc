@extends('layouts.app_login')

@section('content')
<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin"><img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">

            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foro') }}">Foro</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
        </ul>
        <div class="user-info-admin">
            <p>Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 1 ? 'Administrador' : 'Usuario' }})</p>
        </div>
    </nav>
</header>

<main>
    <section>
        <h2>Usuarios Publicate</h2>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Fantasia</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Edad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->fantasia }}</td>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->ubicacion }}</td>
                        <td>{{ $usuario->edad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>
@endsection