@extends('layouts.app_login')

@section('content')
<header>
    <nav class="navbar-admin">
        <div class="logo-admin">
            <a href="{{ route('home') }}" class="logo-text-admin">
                <img src="{{ asset('images/logo_XL-2.png') }}" alt="Logo" class="logo1">
            </a>
        </div>
        <ul class="nav-links-admin">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>
            <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
            <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            <li><a href="{{ route('blogadmin') }}">Blog</a></li>
            <li><a href="{{ route('seo') }}">SEO</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" style="background: none; border: none; padding: 0; color: white; font: inherit; cursor: pointer; text-decoration: none;">
                        Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
        <div class="user-info-admin">
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} ({{ $usuarioAutenticado->role == 2 ? 'Administrador' : 'Administrador' }})</p>
        </div>
    </nav>
</header>


<main>
    <section>
    <h2  style="color:white;">Usuarios Publicate - Activos</h2>
    <table class="table-admin">
            <thead>
                <tr>
                    <th>Fantasia</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Edad</th>
                    <th>Categoría</th>
                    <th>Posición</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuariosActivos as $usuario)
                    <tr>
                        <td>
                            <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                                {{ $usuario->fantasia }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                                {{ $usuario->nombre }}
                            </a>
                        </td>
                        <td>{{ $usuario->ubicacion }}</td>
                        <td>{{ $usuario->edad }}</td>
                        <td>{{ ucfirst($usuario->categorias) }}</td>
                        <td>{{ $usuario->posicion ?? 'Sin posición' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section>
    <h2 style="color:white;">Usuarios Publicate - Inactivos</h2>
    <table class="table-admin">
            <thead>
                <tr>
                    <th>Fantasia</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Edad</th>
                    <th>Categoría</th>
                    <th>Posición</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuariosInactivos as $usuario)
                    <tr>
                        <td>
                            <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                                {{ $usuario->fantasia }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('usuarios_publicate.edit', ['id' => $usuario->id]) }}">
                                {{ $usuario->nombre }}
                            </a>
                        </td>
                        <td>{{ $usuario->ubicacion }}</td>
                        <td>{{ $usuario->edad }}</td>
                        <td>{{ ucfirst($usuario->categorias) }}</td>
                        <td>{{ $usuario->posicion ?? 'Sin posición' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</main>

<footer class="footer-admin">
<p>&copy; {{ date('Y') }} Only scorts chile. Todos los derechos reservados.</p>
</footer>
@endsection
