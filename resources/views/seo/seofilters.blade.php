@extends('layouts.app_login')

@section('content')
<style>
.container-fluid {
    width: 100%;
    margin: 0 auto;
    padding: 2rem;
}

.card.custom-card {
    min-width: 1200px;
    background-color: rgba(26, 26, 26, 0.95);
    margin: 20px auto;
}

.nav-link, .custom-button, a {
    color: #ffffff ;
    transition: all 0.3s ease;
}


.nav-link:hover {
 color: #ffffff;
}

.custom-button {
    background-color: #e00037;
    border: none;
}

.custom-button:hover {
    background-color: #ff1a1a;
    color:#ffffff;
}

@media (max-width: 992px) {
    .card.custom-card {
        min-width: 100%;
    }
}
</style>
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


<div class="card custom-card">
   <div class="card-header">
       <ul class="nav nav-tabs">
           <li class="nav-item">
               <a class="nav-link active" data-bs-toggle="tab" href="#sectores">Sectores</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#nacionalidades">Nacionalidades</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#resenas">Reseñas</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#categorias">Categorías</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#servicios">Servicios</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#atributos">Atributos</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#edad">Edad</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" data-bs-toggle="tab" href="#precio">Precio</a>
           </li>
       </ul>
   </div>

   <div class="card-body">
       <div class="tab-content">
           @php
           $currentCity = Request::segment(1);
           $sectorSeleccionado = Request::segment(2);
           $page = $sectorSeleccionado ? "$currentCity/$sectorSeleccionado" : $currentCity;
           $tipos = [
               'sectores' => ['title' => 'Sectores', 'url' => $currentCity],
               'nacionalidades' => ['title' => 'Nacionalidades', 'url' => "$currentCity/escorts-nacionalidad"],
               'resenas' => ['title' => 'Reseñas', 'url' => "$currentCity/escorts-con-resenas"],
               'categorias' => ['title' => 'Categorías', 'url' => "$currentCity/escorts-categoria"],
               'servicios' => ['title' => 'Servicios', 'url' => "$currentCity/servicios"],
               'atributos' => ['title' => 'Atributos', 'url' => "$currentCity/atributos"],
               'edad' => ['title' => 'Edad', 'url' => "$currentCity/edad"],
               'precio' => ['title' => 'Precio', 'url' => "$currentCity/precio"]
           ];
           @endphp

           @foreach($tipos as $key => $grupo)
               <div id="{{ $key }}" class="tab-pane fade {{ $loop->first ? 'show active' : '' }}">
                   <form action="{{ route('meta-tags.update', $grupo['url']) }}" method="POST">
                       @csrf
                       @method('PUT')
                       <div class="form-group">
                           <label class="text-white">Meta Title</label>
                           <input type="text" name="meta_title" class="form-control" 
                               value="{{ $metaTags->where('tipo', $key)->first()?->meta_title }}" required>
                       </div>
                       <div class="form-group mt-3">
                           <label class="text-white">Meta Description</label>
                           <textarea name="meta_description" class="form-control" rows="3" 
                               required>{{ $metaTags->where('tipo', $key)->first()?->meta_description }}</textarea>
                       </div>
                       <input type="hidden" name="meta_robots" value="index, follow">
                       <input type="hidden" name="tipo" value="{{ $key }}">
                       <button type="submit" class="btn custom-button mt-3">Guardar</button>
                   </form>
               </div>
           @endforeach
       </div>
   </div>
</div>
@endsection