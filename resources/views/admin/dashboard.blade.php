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
            {{-- Inicio disponible para todos los roles --}}
            <li><a href="{{ route('home') }}">Inicio</a></li>

            {{-- Perfil disponible para todos los roles --}}
            <li><a href="{{ route('admin.profile') }}">Perfil</a></li>

            @if($usuarioAutenticado->rol == 1)
                {{-- Menú completo solo para rol 1 --}}
                <li><a href="{{ route('panel_control') }}">Chicas</a></li>
                <li><a href="{{ route('admin.perfiles') }}">Perfiles</a></li>
                <li><a href="{{ route('publicate.form') }}">Publicar</a></li>
                <li><a href="{{ route('foroadmin') }}">Foro</a></li>
                <li><a href="{{ route('blogadmin') }}">Blog</a></li>
                <li><a href="{{ route('seo') }}">SEO</a></li>
            @endif

            @if($usuarioAutenticado->rol == 3)
                {{-- Foro solo disponible para rol 3 --}}
                <li><a href="{{ route('foroadmin') }}">Foro</a></li>
            @endif

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
            <p style="color:white;">Bienvenido, {{ $usuarioAutenticado->name }} 
                @if($usuarioAutenticado->rol == 1)
                    (Administrador)
                @elseif($usuarioAutenticado->rol == 2)
                    (Chica)
                @elseif($usuarioAutenticado->rol == 3)
                    (Usuario)
                @endif
            </p>
        </div>
    </nav>
</header>


<main>
    <section>
    <h2  style="color:white;">Usuarios Publicate - Activos</h2>
    <main>
    <div class="city-filter" style="margin-bottom: 20px;">
        <select id="citySelect" class="form-control">
            <option value="todas">Todas las ciudades</option>
            @foreach($ciudades as $ciudad)
                <option value="{{ $ciudad }}">{{ $ciudad }}</option>
            @endforeach
        </select>
    </div>

    <section>
        <h2 style="color:white;">Usuarios Publicate - Activos</h2>
        <div id="tablaActivos">
            @include('admin.partials.tabla-usuarios', ['usuarios' => $usuariosActivos])
        </div>
    </section>

    <section>
        <h2 style="color:white;">Usuarios Publicate - Inactivos</h2>
        <div id="tablaInactivos">
            @include('admin.partials.tabla-usuarios', ['usuarios' => $usuariosInactivos])
        </div>
    </section>
</main>
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
<script>
document.getElementById('citySelect').addEventListener('change', function() {
    const ciudad = this.value;
    
    // Mostrar indicador de carga si lo deseas
    // document.getElementById('loading').style.display = 'block';
    
    fetch(`/admin/users-by-city?ciudad=${encodeURIComponent(ciudad)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('tablaActivos').innerHTML = data.activos;
        document.getElementById('tablaInactivos').innerHTML = data.inactivos;
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endsection
