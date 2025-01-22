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

<main class="main-admin">
    <section class="form-section">
        @if(session()->has('admin_original_id'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Estás navegando como otro usuario. 
            <a href="{{ route('admin.return') }}" class="alert-link">Volver a tu cuenta de administrador</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Perfiles Rol 2 -->
        <div class="section-content mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Perfiles de Chicas (Rol 2)</h2>
            </div>

            <div class="table-admin">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de Registro</th>
                            <th>Estado Publicate</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perfilesRol2 as $perfil)
                        <tr>
                            <td>{{ $perfil->id }}</td>
                            <td>{{ $perfil->name }}</td>
                            <td>{{ $perfil->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($perfil->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $perfil->publicate_estado ? 'bg-success' : 'bg-danger' }}">
                                    {{ $perfil->publicate_estado ? 'Activo' : 'No Activo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.login.as.user', $perfil->id) }}" class="file-upload-button" style="color: white" title="Editar como usuario">
                                        <i class="fas fa-user-edit"></i> Editar como usuario
                                    </a>
                                    <button onclick="confirmarEliminarPerfil({{ $perfil->id }})" class="btn btn-danger" title="Eliminar Perfil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-center">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                    <p class="h5 text-muted mb-3">No hay perfiles de rol 2 disponibles</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-container">
                    {{ $perfilesRol2->links('layouts.pagination') }}
                </div>
            </div>
        </div>

        <!-- Perfiles Rol 3 -->
        <div class="section-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Perfiles de Usuarios (Rol 3)</h2>
            </div>

            <div class="table-admin">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de Registro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perfilesRol3 as $perfil)
                        <tr>
                            <td>{{ $perfil->id }}</td>
                            <td>{{ $perfil->name }}</td>
                            <td>{{ $perfil->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($perfil->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.login.as.user', $perfil->id) }}" class="file-upload-button" style="color: white" title="Editar como usuario">
                                        <i class="fas fa-user-edit"></i> Editar como usuario
                                    </a>
                                    <button onclick="confirmarEliminarPerfil({{ $perfil->id }})" class="btn btn-danger btn-sm" title="Eliminar Perfil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-center">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                    <p class="h5 text-muted mb-3">No hay perfiles de rol 3 disponibles</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-container">
                    {{ $perfilesRol3->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer-admin">
    <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
</footer>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection