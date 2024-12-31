<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use App\Models\BlogArticle;
use App\Models\EscortLocation;
use App\Models\Posts;
use App\Models\Foro;
use App\Models\SeoTemplate;
use App\Models\Estado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class InicioController extends Controller
{
    public function show($nombreCiudad, $sector = null, $filtros = null)
    {
        $ciudadSeleccionada = Ciudad::where('url', $nombreCiudad)->firstOrFail();
        session(['ciudad_actual' => $ciudadSeleccionada->nombre]);
        $ciudades = Ciudad::all();
        $now = Carbon::now();
        $currentDay = strtolower($now->locale('es')->dayName);
        $currentTime = $now->format('H:i:s');

        // Base query para usuarios principal
        $query = UsuarioPublicate::query()
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre);

        $sectorValido = null;
        $filtroAdicional = null;

        if ($ciudadSeleccionada->url === 'santiago' && $sector) {
            // Si el sector contiene una barra, separamos sector y filtro
            if (str_contains($sector, '/')) {
                list($sectorParte, $filtroParte) = explode('/', $sector, 2);
                $sector = $sectorParte;
                $filtroAdicional = $filtroParte;
            }

            if (!$this->isKnownFilter($sector) && $this->validarSector($sector)) {
                $sectorValido = str_replace('-', ' ', $sector);
                $query->whereHas('location', function ($q) use ($sectorValido) {
                    $q->where('direccion', 'LIKE', "%{$sectorValido}%");
                });

                // Aplicar el filtro adicional si existe
                if ($filtroAdicional) {
                    $filtroNormalizado = str_replace('-', ' ', $filtroAdicional);
                    $query->where(function ($q) use ($filtroNormalizado) {
                        $q->whereRaw('LOWER(atributos) LIKE ?', ['%' . strtolower($filtroNormalizado) . '%'])
                            ->orWhereRaw('LOWER(servicios) LIKE ?', ['%' . strtolower($filtroNormalizado) . '%']);
                    });
                }
            } else if ($this->isKnownFilter($sector)) {
                $filtros = $sector;
                $sector = null;
            } else {
                abort(404);
            }
        }

        if ($categoria = request()->get('categoria')) {
            // Filtramos SOLO por la categoría exacta que viene en el request
            $query->where('categorias', strtolower($categoria));
        } else if ($precio = request()->get('p')) {
            list($min, $max) = explode('-', $precio);
            $query->whereBetween('precio', [(int)$min, (int)$max]);
        }
        // Remove the !$filtroAdicional condition
        if ($filtros) {
            if (str_contains($filtros, 'escorts-')) {
                $nacionalidad = str_replace('escorts-', '', $filtros);
                $query->where('nacionalidad', $nacionalidad);
            } else if (!request()->hasAny(['e', 'p', 'a', 's'])) {
                $filtroNormalizado = str_replace('-', ' ', $filtros);
                $query->where(function ($q) use ($filtroNormalizado) {
                    $q->whereRaw('LOWER(atributos) LIKE ?', ['%' . strtolower($filtroNormalizado) . '%'])
                        ->orWhereRaw('LOWER(servicios) LIKE ?', ['%' . strtolower($filtroNormalizado) . '%']);
                });
            }
        }
        // Procesar filtros desde query parameters
        if (request()->has('disponible')) {
            $query->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            });
        }

        if ($edad = request()->get('e')) {
            list($min, $max) = explode('-', $edad);
            $query->whereBetween('edad', [(int)$min, (int)$max]);
        }

        if ($precio = request()->get('p')) {
            list($min, $max) = explode('-', $precio);
            $query->whereBetween('precio', [(int)$min, (int)$max]);
        }

        if ($nacionalidad = request()->get('n')) {
            $query->where('nacionalidad', $nacionalidad);
        }

        if ($atributos = request()->get('a')) {
            $atributosArray = explode(',', $atributos);
            if (!empty($atributosArray)) {
                $query->where(function ($q) use ($atributosArray) {
                    $atributosLimitados = array_slice($atributosArray, 0, 3);
                    foreach ($atributosLimitados as $key => $atributo) {
                        if ($key === 0) {
                            $q->where('atributos', 'like', '%' . $atributo . '%');
                        } else {
                            $q->orWhere('atributos', 'like', '%' . $atributo . '%');
                        }
                    }
                });
            }
        }

        if ($servicios = request()->get('s')) {
            $serviciosArray = explode(',', $servicios);
            if (!empty($serviciosArray)) {
                $query->where(function ($q) use ($serviciosArray) {
                    $serviciosLimitados = array_slice($serviciosArray, 0, 3);
                    foreach ($serviciosLimitados as $key => $servicio) {
                        if ($key === 0) {
                            $q->where('servicios', 'like', '%' . $servicio . '%');
                        } else {
                            $q->orWhere('servicios', 'like', '%' . $servicio . '%');
                        }
                    }
                });
            }
        }

        if (request()->has('resena')) {
            $query->has('resenas');
        }

        // Consulta principal con filtros
        $usuarios = $query->with([
            'disponibilidad',
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            },
            'location:id,usuario_publicate_id,direccion'
        ])
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->orderBy('posicion', 'asc')
            ->paginate(12);

        // Estados
        $estados = Estado::select('estados.*', 'u.foto as user_foto')
            ->leftJoin('users as u', 'estados.user_id', '=', 'u.id')
            ->with(['usuarioPublicate', 'vistoPor' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->whereHas('usuarioPublicate', function ($query) use ($ciudadSeleccionada) {
                $query->where('ubicacion', $ciudadSeleccionada->nombre)
                    ->whereIn('estadop', [1, 3]);
            })
            ->where('estados.created_at', '>=', now()->subHours(24))
            ->orderByRaw('CASE WHEN EXISTS (
            SELECT 1 FROM estado_visto 
            WHERE estado_visto.estado_id = estados.id 
            AND estado_visto.user_id = ?) 
            THEN 1 ELSE 0 END', [auth()->id()])
            ->orderBy('estados.created_at', 'desc')
            ->get();

        // Usuario destacado
        $usuarioDestacado = UsuarioPublicate::with(['estados' => function ($query) {
            $query->where('created_at', '>=', now()->subHours(24));
        }])
            ->where('estadop', 3)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'precio',
                'estadop'
            )
            ->first();

        // Usuarios online
        $usuariosOnline = UsuarioPublicate::with([
            'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            },
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            }
        ])
            ->where('estadop', 1)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            })
            ->select('id', 'fantasia', 'edad', 'fotos', 'foto_positions', 'estadop')
            ->take(11)
            ->get();

        // Primera vez
        $primeraVez = UsuarioPublicate::with([
            'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            }
        ])
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // Volvieron
        $volvieron = UsuarioPublicate::with([
            'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            }
        ])
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->whereRaw('DATE(updated_at) != DATE(created_at)')
            ->orderBy('updated_at', 'desc')
            ->take(2)
            ->get();

        // Blog articles
        $blogArticles = BlogArticle::where('estado', 'publicado')
            ->whereNotNull('fecha_publicacion')
            ->orderBy('fecha_publicacion', 'desc')
            ->select(
                'id',
                'titulo',
                'slug',
                'extracto',
                'imagen',
                'destacado',
                'fecha_publicacion'
            )
            ->take(4)
            ->get();

        // Experiencias
        $experiencias = Posts::select(
            'posts.id',
            'posts.titulo',
            'posts.created_at',
            'posts.visitas',
            'posts.is_fixed',
            'users.name as autor_nombre',
            'blog_articles.imagen as blog_imagen',
            'posts.id_blog'
        )
            ->leftJoin('users', 'posts.id_usuario', '=', 'users.id')
            ->leftJoin('blog_articles', 'posts.id_blog', '=', 'blog_articles.id')
            ->leftJoin('foro', 'posts.id_blog', '=', 'foro.id_blog')
            ->orderBy('posts.created_at', 'desc')
            ->take(4)
            ->get();

        // Procesar la ubicación para la vista
        $ubicacionesMostradas = [];
        foreach ($usuarios as $usuario) {
            if ($ciudadSeleccionada->url === 'santiago') {
                if ($sectorValido) {
                    // Si hay un sector explícito, usarlo directamente
                    $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
                } elseif ($usuario->location && $usuario->location->direccion) {
                    // Extraer el sector explícito de la dirección
                    $ubicacionesMostradas[$usuario->id] = $this->extraerSectorDeDireccion($usuario->location->direccion);
                } else {
                    // Fallback si no hay datos
                    $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
                }
            } else {
                $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
            }

        if ($ciudadSeleccionada->url === 'santiago') {
            if ($sectorValido) {
                // Filtrar por sector explícito
                $query->whereHas('location', function ($q) use ($sectorValido) {
                    $q->where('direccion', 'LIKE', "%{$sectorValido}%");
                });
            } elseif ($usuario->location && $usuario->location->direccion) {
                // Derivar el sector de la dirección del usuario y usarlo como filtro
                $direccionParts = explode(',', $usuario->location->direccion);
                $sectorDerivado = trim($direccionParts[0]);
                $query->whereHas('location', function ($q) use ($sectorDerivado) {
                    $q->where('direccion', 'LIKE', "%{$sectorDerivado}%");
                });
            }
        }

        if ($filtros) {
            $filtroNormalizado = str_replace('-', ' ', $filtros);
            $clasificacion = $this->clasificarFiltro($filtroNormalizado);
        
            if ($clasificacion) {
                if ($clasificacion['tipo'] === 'atributo') {
                    $query->where('atributos', 'LIKE', '%' . $clasificacion['valor'] . '%');
                } elseif ($clasificacion['tipo'] === 'servicio') {
                    $query->where('servicios', 'LIKE', '%' . $clasificacion['valor'] . '%');
                }
            }
        }        
    
        $seoText = $this->generateSeoText(request(), $ciudadSeleccionada, $sectorValido);


        return view('inicio', array_merge([
            'ciudades' => $ciudades,
            'ciudadSeleccionada' => $ciudadSeleccionada,
            'sectorSeleccionado' => $sectorValido,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
            'usuariosOnline' => $usuariosOnline,
            'totalOnline' => $usuariosOnline->count(),
            'currentTime' => $currentTime,
            'currentDay' => $currentDay,
            'estados' => $estados,
            'primeraVez' => $primeraVez,
            'blogArticles' => $blogArticles,
            'volvieron' => $volvieron,
            'experiencias' => $experiencias,
            'ubicacionesMostradas' => $ubicacionesMostradas
        ], $seoText ? [
            'seoTitle' => $seoText['title'],
            'seoDescription' => $seoText['description']
        ] : []));
    }

    }
    private function isKnownFilter($value)
    {
        $knownPrefixes = [
            'escorts-',
            'masajes-',
            'servicios-',
            'atributos-'
        ];

        foreach ($knownPrefixes as $prefix) {
            if (str_starts_with($value, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function extraerSectorDeDireccion($direccion)
{
    if (!$direccion) {
        return 'Sector no disponible';
    }

    // Dividir la dirección por comas
    $partes = array_map('trim', explode(',', $direccion));

    if (count($partes) >= 2) {
        // Tomar la segunda parte de la dirección
        $segundaParte = $partes[1];

        // Procesar para eliminar números y espacios adicionales
        $partesComuna = array_map('trim', explode(' ', $segundaParte));
        $comuna = end($partesComuna);
        $comuna = preg_replace('/^(\d+\s+)?/', '', $comuna);

        return trim($comuna);
    }

    return 'Sector no disponible';
}


    private function validarSector($sector)
    {
        // Si el sector contiene una barra, tomamos solo la primera parte
        if (str_contains($sector, '/')) {
            $sector = explode('/', $sector)[0];
        }

        // Normalizar el sector para comparar
        $sectorNormalizado = str_replace('-', ' ', strtolower($sector));

        // Obtener barrios del caché
        $barriosSantiago = Cache::get('barrios_santiago', []);

        // Verificar si el sector normalizado está en los barrios
        return in_array($sectorNormalizado, array_map('strtolower', $barriosSantiago));
    }

    private function clasificarFiltro($filtro)
{
    $servicios = [
        "Anal", "Atención a domicilio", "Atención en hoteles", "Baile Erotico", "Besos", "Cambio de rol",
        "Departamento Propio", "Disfraces", "Ducha Erotica", "Eventos y Cenas", "Eyaculación Cuerpo", 
        "Eyaculación Facial", "Hetero", "Juguetes", "Lesbico", "Lluvia dorada", "Masaje Erotico", 
        "Masaje prostatico", "Masaje Tantrico", "Masaje Thai", "Masajes con final feliz", 
        "Masajes desnudos", "Masajes Eroticos", "Masajes para hombres", "Masajes sensitivos", 
        "Masajes sexuales", "Masturbación Rusa", "Oral Americana", "Oral con preservativo", 
        "Oral sin preservativo", "Orgias", "Parejas", "Trio"
    ];

    $atributos = [
        "Busto Grande", "Busto Mediano", "Busto Pequeño", "Cara Visible", "Cola Grande", 
        "Cola Mediana", "Cola Pequeña", "Con Video", "Contextura Delgada", "Contextura Grande", 
        "Contextura Mediana", "Depilación Full", "Depto Propio", "En Promoción", "English", 
        "Escort Independiente", "Español", "Estatura Alta", "Estatura Mediana", "Estatura Pequeña", 
        "Hentai", "Morena", "Mulata", "No fuma", "Ojos Claros", "Ojos Oscuros", "Peliroja", 
        "Portugues", "Relato Erotico", "Rubia", "Tatuajes", "Trigueña"
    ];

    $filtroNormalizado = ucwords(str_replace('-', ' ', strtolower($filtro)));

    if (in_array($filtroNormalizado, $servicios)) {
        return ['tipo' => 'servicio', 'valor' => $filtroNormalizado];
    } elseif (in_array($filtroNormalizado, $atributos)) {
        return ['tipo' => 'atributo', 'valor' => $filtroNormalizado];
    }

    return null;
}


    public function showByCategory($nombreCiudad, $categoria)
    {
        // Convertir la categoría a mayúsculas
        $categoria = strtoupper($categoria);

        // Buscar la ciudad por nombre
        $ciudadSeleccionada = Ciudad::where('nombre', $nombreCiudad)->first();

        if (!$ciudadSeleccionada) {
            abort(404, 'Ciudad no encontrada');
        }

        // Validar la categoría
        $categoriasPermitidas = ['DELUXE', 'VIP', 'PREMIUM', 'MASAJES'];
        if (!in_array($categoria, $categoriasPermitidas)) {
            abort(404, 'Categoría no válida');
        }

        // Obtener todas las ciudades
        $ciudades = Ciudad::all();

        // Obtener la hora y día actuales
        $now = Carbon::now();
        $currentDay = strtolower($now->locale('es')->dayName);
        $currentTime = $now->format('H:i:s');

        // Obtener estados de las últimas 24 horas para la categoría
        $estados = Estado::with(['usuarioPublicate', 'vistoPor' => function ($query) {
            $query->where('user_id', auth()->id());
        }])
            ->whereHas('usuarioPublicate', function ($query) use ($ciudadSeleccionada, $categoria) {
                $query->where('ubicacion', $ciudadSeleccionada->nombre)
                    ->where('categorias', $categoria)
                    ->whereIn('estadop', [1, 3]);
            })
            ->where('created_at', '>=', now()->subHours(24))
            ->orderByRaw('CASE WHEN EXISTS (
                SELECT 1 FROM estado_visto 
                WHERE estado_visto.estado_id = estados.id 
                AND estado_visto.user_id = ?) 
                THEN 1 ELSE 0 END', [auth()->id()])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filtrar usuarios por ciudad y categoría
        $usuarios = UsuarioPublicate::with(['disponibilidad', 'estados' => function ($query) {
            $query->where('created_at', '>=', now()->subHours(24));
        }])
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->where('categorias', $categoria)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->orderBy('posicion', 'asc')
            ->paginate(12)
            ->appends(request()->query());

        // Usuario destacado
        $usuarioDestacado = UsuarioPublicate::with(['estados' => function ($query) {
            $query->where('created_at', '>=', now()->subHours(24));
        }])
            ->where('estadop', 3)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->where('categorias', $categoria)
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'precio',
                'estadop'
            )
            ->first();

        // Usuarios online
        $usuariosOnline = UsuarioPublicate::with([
            'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            },
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            }
        ])
            ->where('estadop', 1)
            ->where('ubicacion', $ciudadSeleccionada->nombre)
            ->where('categorias', $categoria)
            ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                $query->where('dia', 'LIKE', $currentDay)
                    ->where(function ($q) use ($currentTime) {
                        $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            })
            ->select('id', 'fantasia', 'edad', 'fotos', 'foto_positions', 'estadop')
            ->take(11)
            ->get();

        return view('inicio', [
            'ciudades' => $ciudades,
            'ciudadSeleccionada' => $ciudadSeleccionada,
            'usuarios' => $usuarios,
            'usuarioDestacado' => $usuarioDestacado,
            'usuariosOnline' => $usuariosOnline,
            'totalOnline' => $usuariosOnline->count(),
            'currentTime' => $currentTime,
            'currentDay' => $currentDay,
            'categoriaSeleccionada' => $categoria,
            'estados' => $estados
        ]);
    }

    public function marcarComoVisto(Request $request)
    {
        try {
            $request->validate([
                'estado_id' => 'required|exists:estados,id'
            ]);

            $estado = Estado::findOrFail($request->estado_id);

            // Evitar duplicados
            if (!$estado->vistoPor()->where('user_id', auth()->id())->exists()) {
                $estado->vistoPor()->attach(auth()->id(), [
                    'visto_at' => now()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al marcar el estado como visto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showPerfil($id)
    {
        $usuario = UsuarioPublicate::with([
            'disponibilidad',
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            }
        ])->findOrFail($id);

        return view('perfil', ['usuario' => $usuario]);
    }


    public function RTA()
    {
        $ciudades = Ciudad::all();

        // Obtener un usuario específico o todos los usuarios que necesites
        $usuarioPublicate = UsuarioPublicate::with([
            'disponibilidad',
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            }
        ])
            ->whereIn('estadop', [1, 3]) // Si necesitas filtrar por estado
            ->select(
                'id',
                'fantasia',
                'nombre',
                'edad',
                'ubicacion',
                'fotos',
                'foto_positions',
                'categorias',
                'posicion',
                'precio',
                'estadop'
            )
            ->first(); // o ->get() si necesitas varios usuarios

        return view('rta', [
            'ciudades' => $ciudades,
            'usuarioPublicate' => $usuarioPublicate
        ]);
    }

    public function filterUsuarios($nombreCiudad, Request $request)
    {
        $queryParams = [];

        // Construir query parameters
        if ($request->has(['edadMin', 'edadMax'])) {
            $queryParams['e'] = "{$request->edadMin}-{$request->edadMax}";
        }

        if ($request->has(['precioMin', 'precioMax'])) {
            $queryParams['p'] = "{$request->precioMin}-{$request->precioMax}";
        }

        if ($request->has('atributos') && !empty($request->atributos)) {
            $queryParams['a'] = implode(',', $request->atributos);
        }

        if ($request->has('servicios') && !empty($request->servicios)) {
            $queryParams['s'] = implode(',', $request->servicios);
        }

        // Construir la URL base
        $baseUrl = "/{$nombreCiudad}";

        // Añadir query parameters si existen
        $filterUrl = $baseUrl;
        if (!empty($queryParams)) {
            $filterUrl .= '?' . http_build_query($queryParams);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => $filterUrl
            ]);
        }

        return redirect($filterUrl);
    }

    private function generateSeoText($request, $ciudadSeleccionada, $sectorSeleccionado = null)
    {
        // Verificar URL para nacionalidad
        $path = $request->path();
        $parts = explode('/', $path);
        $lastPart = end($parts);
        $isNationalityFilter = str_starts_with($lastPart, 'escorts-');
    
        if ($isNationalityFilter) {
            $nacionalidad = str_replace('escorts-', '', $lastPart);
            $request->merge(['n' => $nacionalidad]);
        }
    
        // Contar filtros activos
        $activeFilters = 0;
        if ($request->has('n') || $isNationalityFilter) $activeFilters++;
        if ($request->has('e')) $activeFilters++;
        if ($request->has('p')) $activeFilters++;
        if ($request->has('a')) $activeFilters += count(explode(',', $request->get('a')));
        if ($request->has('s')) $activeFilters += count(explode(',', $request->get('s')));
    
        if ($activeFilters === 0 && !$sectorSeleccionado) return null;
    
        // Determinar tipo de template
        $templateType = $activeFilters > 4 ? 'complex' : ($activeFilters > 1 ? 'multiple' : 'single');
    
        // Obtener template
        $template = SeoTemplate::where('tipo', $templateType)
            ->where(function ($query) use ($ciudadSeleccionada) {
                $query->where('ciudad_id', $ciudadSeleccionada->id)
                    ->orWhereNull('ciudad_id');
            })
            ->orderBy('ciudad_id', 'desc')
            ->first();
    
        if (!$template) return null;
    
        // Array de nacionalidades
        $nacionalidades = [
            'argentina' => 'argentinas',
            'brasil' => 'brasileñas',
            'chile' => 'chilenas',
            'colombia' => 'colombianas',
            'ecuador' => 'ecuatorianas',
            'uruguay' => 'uruguayas',
            'argentinas' => 'argentinas',
            'brasilenas' => 'brasileñas',
            'chilenas' => 'chilenas',
            'colombianas' => 'colombianas',
            'ecuatorianas' => 'ecuatorianas',
            'uruguayas' => 'uruguayas'
        ];
    
        // Preparar reemplazos
        $replacements = [
            '{ciudad}' => $ciudadSeleccionada->nombre,
            '{sector}' => $sectorSeleccionado ? ucwords($sectorSeleccionado) : '',
            '{nacionalidad}' => '',
            '{edad_min}' => '18',
            '{edad_max}' => '50',
            '{precio_min}' => '50.000',
            '{precio_max}' => '300.000',
            '{atributos}' => '',
            '{servicios}' => ''
        ];
    
        // Procesar nacionalidad
        if ($nacionalidad = $request->get('n')) {
            $replacements['{nacionalidad}'] = $nacionalidades[$nacionalidad] ?? '';
        }
    
        // Procesar edad
        if ($edad = $request->get('e')) {
            list($min, $max) = explode('-', $edad);
            $replacements['{edad_min}'] = $min;
            $replacements['{edad_max}'] = $max;
        }
    
        // Procesar precio
        if ($precio = $request->get('p')) {
            list($min, $max) = explode('-', $precio);
            $replacements['{precio_min}'] = number_format($min, 0, ',', '.');
            $replacements['{precio_max}'] = number_format($max, 0, ',', '.');
        }
    
        // Procesar atributos desde los filtros
        if ($request->has('a')) {
            $atributosArray = explode(',', $request->get('a'));
            $replacements['{atributos}'] = implode(', ', array_map('ucwords', $atributosArray));
        } elseif ($lastPart) {
            $clasificacion = $this->clasificarFiltro($lastPart);
            if ($clasificacion && $clasificacion['tipo'] === 'atributo') {
                $replacements['{atributos}'] = $clasificacion['valor'];
            }
        }
    
        // Procesar servicios desde los filtros
        if ($request->has('s')) {
            $serviciosArray = explode(',', $request->get('s'));
            $replacements['{servicios}'] = implode(', ', array_map('ucwords', $serviciosArray));
        } elseif ($lastPart) {
            $clasificacion = $this->clasificarFiltro($lastPart);
            if ($clasificacion && $clasificacion['tipo'] === 'servicio') {
                $replacements['{servicios}'] = $clasificacion['valor'];
            }
        }
    
        // Generar descripción y título
        $description = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template->description_template
        );
    
        // Generar título
        $title = "Escorts en " . $ciudadSeleccionada->nombre;
        if ($sectorSeleccionado) {
            $title .= " - " . ucwords($sectorSeleccionado);
        }
        if ($nacionalidad = $request->get('n')) {
            if (isset($nacionalidades[$nacionalidad])) {
                $title = "Escorts " . $nacionalidades[$nacionalidad] . " en " . $ciudadSeleccionada->nombre;
                if ($sectorSeleccionado) {
                    $title .= " - " . ucwords($sectorSeleccionado);
                }
            }
        }
    
        // Limpiar variables no reemplazadas
        $description = preg_replace('/\{[^}]+\}/', '', $description);
    
        return [
            'title' => $title,
            'description' => $description
        ];
    }
}
