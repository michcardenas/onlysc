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
        $seoText = null;
        try {
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

            Log::debug('Query inicial', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'count' => $query->count()
            ]);
    
        $sectorValido = null;
        $filtroAdicional = null;
    
        // Primero procesamos categorías especiales
        $categorias_especiales = ['premium', 'vip', 'de_lujo', 'de lujo', 'under'];
        if ($sector && in_array(strtolower($sector), $categorias_especiales)) {
            $sector_categoria = strtolower($sector);
            if ($sector_categoria === 'de lujo') {
                $sector_categoria = 'de_lujo';
            }
            request()->merge(['categoria' => $sector_categoria]);
            $sector = null;
        }
    
        // Procesamiento de filtros en formato URL-friendly
        if ($sector && !$this->validarSector($sector)) {
            // El sector no es válido, podría ser un filtro
            $filtros = $sector;
            $sector = null;
        }
    
        if ($filtros) {
            // Procesar filtros de edad
            if (preg_match('/^edad-(\d+)-(\d+)$/', $filtros, $matches)) {
                $min = (int)$matches[1];
                $max = (int)$matches[2];
                $query->whereBetween('edad', [$min, $max]);
            }
            // Procesar filtros de precio
            else if (preg_match('/^precio-(\d+)-(\d+)$/', $filtros, $matches)) {
                $min = (int)$matches[1];
                $max = (int)$matches[2];
                $query->whereBetween('precio', [$min, $max]);
            }
            // Procesar nacionalidad
            else if (str_starts_with($filtros, 'escorts-')) {
                $nacionalidad = str_replace('escorts-', '', $filtros);
                $query->where('nacionalidad', $nacionalidad);
            }
            // Procesar disponible
            else if ($filtros === 'disponible') {
                $query->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                    $query->where('dia', 'LIKE', $currentDay)
                        ->where(function ($q) use ($currentTime) {
                            $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                                ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                        });
                });
            }
            // Procesar reseña verificada
            else if ($filtros === 'resena-verificada') {
                $query->has('resenas');
            }
            // Procesar servicios y atributos
            else {
                $filtroNormalizado = str_replace('-', ' ', $filtros);
                $clasificacion = $this->clasificarFiltro($filtroNormalizado);
                
                if ($clasificacion) {
                    if ($clasificacion['tipo'] === 'servicio') {
                        $query->whereRaw('LOWER(servicios) LIKE ?', ['%' . strtolower($clasificacion['valor']) . '%']);
                    } elseif ($clasificacion['tipo'] === 'atributo') {
                        $query->whereRaw('LOWER(atributos) LIKE ?', ['%' . strtolower($clasificacion['valor']) . '%']);
                    }
                }
            }
        }
    
        // Si hay un sector válido, lo procesamos
        if ($sector && $this->validarSector($sector)) {
            $sectorValido = str_replace('-', ' ', $sector);
            $query->whereHas('location', function ($q) use ($sectorValido) {
                $q->where('direccion', 'LIKE', "%{$sectorValido}%");
            });
        }
    
        if ($categoria = request()->get('categoria')) {
            $categoria = strtolower($categoria);
            
            // Normalizar categorías
            switch ($categoria) {
                case 'de lujo':
                    $categoria = 'de_lujo';
                    break;
                case 'premium':
                case 'vip':
                case 'under':
                    // Mantener el valor tal cual
                    break;
                default:
                    Log::warning('Categoría no reconocida', ['categoria' => $categoria]);
            }

            Log::info('Aplicando filtro de categoría', [
                'categoria' => $categoria,
                'query_antes' => $query->toSql(),
                'bindings_antes' => $query->getBindings()
            ]);
            
            // Aplicar el filtro de categoría
            $query->where('categorias', $categoria);

            Log::info('Después de aplicar filtro de categoría', [
                'query_final' => $query->toSql(),
                'bindings_final' => $query->getBindings(),
                'resultados' => $query->count()
            ]);
        }

        // Si NO hay filtro de categoría, entonces procesar filtros de precio normalmente
        elseif ($precio = request()->get('p')) {
            list($min, $max) = explode('-', $precio);
            $query->whereBetween('precio', [(int)$min, (int)$max]);
        }


        // Si NO hay filtro de categoría, entonces procesar filtros de precio normalmente
        elseif ($precio = request()->get('p')) {
            list($min, $max) = explode('-', $precio);
            $query->whereBetween('precio', [(int)$min, (int)$max]);
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
        // Usuarios online
$usuariosOnline = UsuarioPublicate::with([
    'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
        $query->where(function ($q) use ($currentDay, $currentTime) {
            // Handle full time availability
            $q->where(function ($sub) use ($currentDay) {
                $sub->where('dia', 'LIKE', $currentDay)
                    ->where('hora_desde', '00:00:00')
                    ->where('hora_hasta', '23:59:00');
            })
            // Handle regular time slots
            ->orWhere(function ($sub) use ($currentDay, $currentTime) {
                $sub->where('dia', 'LIKE', $currentDay)
                    ->where(function ($timeQ) use ($currentTime) {
                        $timeQ->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            });
        });
    },
    'estados' => function ($query) {
        $query->where('created_at', '>=', now()->subHours(24));
    }
])
    ->whereIn('estadop', [1, 3])  // Modificado para incluir estadop 3
    ->where('ubicacion', $ciudadSeleccionada->nombre)
    ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
        $query->where(function ($q) use ($currentDay, $currentTime) {
            // Handle full time availability
            $q->where(function ($sub) use ($currentDay) {
                $sub->where('dia', 'LIKE', $currentDay)
                    ->where('hora_desde', '00:00:00')
                    ->where('hora_hasta', '23:59:00');
            })
            // Handle regular time slots
            ->orWhere(function ($sub) use ($currentDay, $currentTime) {
                $sub->where('dia', 'LIKE', $currentDay)
                    ->where(function ($timeQ) use ($currentTime) {
                        $timeQ->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                    });
            });
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
    
        try {
            if (method_exists($this, 'generateSeoText')) {
                $seoText = $this->generateSeoText(request(), $ciudadSeleccionada, $sectorValido);
            }
        } catch (\Exception $e) {
            Log::error('Error generating SEO text', ['error' => $e->getMessage()]);
        }

    }
        }
     catch (\Exception $e) {
        Log::error('Error en show', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }


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
        ],  $seoText ? [
            'seoTitle' => $seoText['title'], 
            'seoDescription' => $seoText['description']
        ] : []));
    }

    private function isKnownFilter($value)
{
    // Si ya tiene un prefijo conocido, retornar true
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

    // Si no tiene prefijo, verificar si es un servicio o atributo válido
    $filtroNormalizado = str_replace('-', ' ', strtolower($value));
    
    // Obtener la primera parte si hay una barra
    if (str_contains($filtroNormalizado, '/')) {
        $filtroNormalizado = explode('/', $filtroNormalizado)[0];
    }

    // Convertir a formato título para comparar
    $filtroNormalizado = ucwords($filtroNormalizado);

    // Verificar si está en la lista de servicios o atributos
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

    return in_array($filtroNormalizado, $servicios) || in_array($filtroNormalizado, $atributos);
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
        // Añadir logging para debug
        Log::info('Clasificando filtro: ' . $filtro);
        
        $servicios = array_map('strtolower', [
            "Anal", "Atención a domicilio", "Atención en hoteles", "Baile Erotico", "Besos", "Cambio de rol",
            "Departamento Propio", "Disfraces", "Ducha Erotica", "Eventos y Cenas", "Eyaculación Cuerpo", 
            "Eyaculación Facial", "Hetero", "Juguetes", "Lesbico", "Lluvia dorada", "Masaje Erotico", 
            "Masaje prostatico", "Masaje Tantrico", "Masaje Thai", "Masajes con final feliz", 
            "Masajes desnudos", "Masajes Eroticos", "Masajes para hombres", "Masajes sensitivos", 
            "Masajes sexuales", "Masturbación Rusa", "Oral Americana", "Oral con preservativo", 
            "Oral sin preservativo", "Orgias", "Parejas", "Trio"
        ]);
    
        $atributos = array_map('strtolower', [
            "Busto Grande", "Busto Mediano", "Busto Pequeño", "Cara Visible", "Cola Grande", 
            "Cola Mediana", "Cola Pequeña", "Con Video", "Contextura Delgada", "Contextura Grande", 
            "Contextura Mediana", "Depilación Full", "Depto Propio", "En Promoción", "English", 
            "Escort Independiente", "Español", "Estatura Alta", "Estatura Mediana", "Estatura Pequeña", 
            "Hentai", "Morena", "Mulata", "No fuma", "Ojos Claros", "Ojos Oscuros", "Peliroja", 
            "Portugues", "Relato Erotico", "Rubia", "Tatuajes", "Trigueña"
        ]);
    
        // Normalizar el filtro a minúsculas para la comparación
        $filtroNormalizado = strtolower($filtro);
        Log::info('Filtro normalizado: ' . $filtroNormalizado);
    
        if (in_array($filtroNormalizado, $servicios)) {
            $valorOriginal = ucwords(str_replace('-', ' ', $filtro));
            Log::info('Encontrado como servicio: ' . $valorOriginal);
            return ['tipo' => 'servicio', 'valor' => $valorOriginal];
        } 
        
        if (in_array($filtroNormalizado, $atributos)) {
            $valorOriginal = ucwords(str_replace('-', ' ', $filtro));
            Log::info('Encontrado como atributo: ' . $valorOriginal);
            return ['tipo' => 'atributo', 'valor' => $valorOriginal];
        }
    
        Log::info('No se encontró coincidencia para el filtro');
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
        Log::info('Starting generateSeoText');
        
        // Array de nacionalidades (mantener el existente)
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
        
        // Procesar la URL para detectar filtros en la ruta
        $path = $request->path();
        $parts = explode('/', $path);
        $lastPart = strtolower(end($parts));
        
        // Detectar filtros individuales en la URL
        $singleFilter = null;
        
        // Detectar filtro de nacionalidad en la URL
        $isNationalityFilter = false;
        $nacionalidadFromUrl = null;
        
        if (str_starts_with($lastPart, 'escorts-')) {
            $posibleNacionalidad = str_replace('escorts-', '', $lastPart);
            if (isset($nacionalidades[$posibleNacionalidad])) {
                $isNationalityFilter = true;
                $nacionalidadFromUrl = $posibleNacionalidad;
                $request->merge(['n' => $nacionalidadFromUrl]);
                $singleFilter = 'nacionalidad';
            }
        }
        // Detectar otros filtros individuales
        elseif (str_starts_with($lastPart, 'edad-')) {
            $edadValues = str_replace('edad-', '', $lastPart);
            $request->merge(['e' => $edadValues]);
            $singleFilter = 'edad';
        }
        elseif (str_starts_with($lastPart, 'disponible')) {
            $request->merge(['disponible' => '1']);
            $singleFilter = 'disponible';
        }
        elseif (str_starts_with($lastPart, 'resena-verificada')) {
            $request->merge(['resena' => '1']);
            $singleFilter = 'resena';
        }
        elseif (in_array($lastPart, ['vip', 'premium', 'de_lujo', 'under'])) {
            $request->merge(['categoria' => $lastPart]);
            $singleFilter = 'categoria';
        }
    
        // Detectar otros filtros en la URL (servicios y atributos)
        $clasificacionUrl = null;
        if (!$isNationalityFilter && !$singleFilter) {
            $clasificacionUrl = $this->clasificarFiltro($lastPart);
            if ($clasificacionUrl) {
                if ($clasificacionUrl['tipo'] === 'servicio') {
                    // Para filtro único de servicio, usar el valor normalizado directamente
                    $request->merge(['s' => $clasificacionUrl['valor']]);
                    $singleFilter = 'servicios';
                } elseif ($clasificacionUrl['tipo'] === 'atributo') {
                    // Para filtro único de atributo, usar el valor normalizado directamente
                    $request->merge(['a' => $clasificacionUrl['valor']]);
                    $singleFilter = 'atributos';
                }
            }
        }
        
        Log::info('Detected single filter', [
            'filter_type' => $singleFilter,
            'last_part' => $lastPart,
            'clasificacion' => $clasificacionUrl
        ]);
    
        // Estructura para almacenar información de filtros activos
        $activeFilters = [
            'nacionalidad' => null,
            'edad' => null,
            'precio' => null,
            'atributos' => [],
            'servicios' => [],
            'categoria' => null,
            'disponible' => false,
            'resena' => false
        ];
    
        // Procesar cada tipo de filtro
        if ($request->has('n') || $isNationalityFilter) {
            $nacionalidad = $request->get('n') ?? $nacionalidadFromUrl;
            $activeFilters['nacionalidad'] = $nacionalidades[$nacionalidad] ?? null;
        }
    
        if ($request->has('e')) {
            list($min, $max) = explode('-', $request->get('e'));
            $activeFilters['edad'] = ['min' => $min, 'max' => $max];
        }
    
        if ($request->has('p')) {
            list($min, $max) = explode('-', $request->get('p'));
            $activeFilters['precio'] = [
                'min' => number_format($min, 0, ',', '.'),
                'max' => number_format($max, 0, ',', '.')
            ];
        }
    
        if ($request->has('a')) {
            $activeFilters['atributos'] = array_map('ucwords', explode(',', $request->get('a')));
        }
    
        if ($request->has('s')) {
            $activeFilters['servicios'] = array_map('ucwords', explode(',', $request->get('s')));
        }
    
        if ($request->has('categoria')) {
            $activeFilters['categoria'] = ucwords($request->get('categoria'));
        }
    
        if ($request->has('disponible')) {
            $activeFilters['disponible'] = true;
        }
    
        if ($request->has('resena')) {
            $activeFilters['resena'] = true;
        }
    
        // Procesar clasificación de URL si existe
        if ($clasificacionUrl) {
            if ($clasificacionUrl['tipo'] === 'servicio') {
                $activeFilters['servicios'][] = $clasificacionUrl['valor'];
            } elseif ($clasificacionUrl['tipo'] === 'atributo') {
                $activeFilters['atributos'][] = $clasificacionUrl['valor'];
            }
        }
    
        // Contar filtros activos totales
        $totalActiveFilters = count(array_filter($activeFilters, function($value) {
            return !empty($value) && $value !== false && 
                   (!is_array($value) || !empty(array_filter($value)));
        }));
    
        // Determinar el tipo de template basado en la cantidad de filtros
        $templateType = 'filtro';
        $filtroType = 'ciudad';
    
        // Si es un filtro único por URL, usar ese tipo específico
        if ($singleFilter) {
            $templateType = 'filtro';
            $filtroType = $singleFilter;
            Log::info('Using single filter template', [
                'type' => $filtroType,
                'template_type' => $templateType
            ]);
    
        if ($totalActiveFilters > 0) {
            if ($totalActiveFilters > 4) {
                $templateType = 'complex';
            } elseif ($totalActiveFilters > 1) {
                $templateType = 'multiple';
            } elseif ($totalActiveFilters === 1) {
                // Determinar el tipo de filtro único
                foreach ($activeFilters as $key => $value) {
                    if (!empty($value) && $value !== false) {
                        $filtroType = $key;
                        break;
                    }
                }
            }
        }
    
        // Obtener template apropiado
        $templateQuery = SeoTemplate::query()
            ->where(function ($query) use ($ciudadSeleccionada) {
                $query->where('ciudad_id', $ciudadSeleccionada->id)
                      ->orWhereNull('ciudad_id');
            })
            ->where(function ($query) use ($templateType, $filtroType) {
                if ($templateType === 'filtro') {
                    $query->where('tipo', 'filtro')
                          ->where('filtro', $filtroType);
                } else {
                    $query->where('tipo', $templateType);
                }
            })
            ->orderBy('ciudad_id', 'desc');
    
        $template = $templateQuery->first();
    
        if (!$template) {
            Log::info('No template found');
            return null;
        }
    
        // Preparar reemplazos base
        $replacements = [
            '{ciudad}' => $ciudadSeleccionada->nombre,
            '{sector}' => $sectorSeleccionado ? ucwords($sectorSeleccionado) : '',
            '{nacionalidad}' => $activeFilters['nacionalidad'] ?? '',
            '{edad_min}' => $activeFilters['edad']['min'] ?? '18',
            '{edad_max}' => $activeFilters['edad']['max'] ?? '50',
            '{precio_min}' => $activeFilters['precio']['min'] ?? '50.000',
            '{precio_max}' => $activeFilters['precio']['max'] ?? '300.000',
            '{atributos}' => implode(', ', array_unique($activeFilters['atributos'])),
            '{servicios}' => implode(', ', array_unique($activeFilters['servicios'])),
            '{disponible}' => $activeFilters['disponible'] ? 'ahora' : '',
            '{resena}' => $activeFilters['resena'] ? 'verificada' : '',
            '{categorias}' => $activeFilters['categoria'] ?? ''
        ];
    
        // Generar título SEO
        $title = $this->generateSeoTitle($ciudadSeleccionada, $sectorSeleccionado, $activeFilters, $singleFilter, $clasificacionUrl);
    
        // Generar descripción
        $description = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template->description_template
        );
    
        // Limpiar variables no reemplazadas
        $description = preg_replace('/\{[^}]+\}/', '', $description);
    
        Log::info('Generated SEO text', [
            'title' => $title,
            'description' => $description,
            'template_type' => $templateType,
            'active_filters' => $totalActiveFilters
        ]);
    
        return [
            'title' => $title,
            'description' => $description
        ];
    }
}
    private function generateSeoTitle($ciudad, $sector, $filters, $singleFilter = null, $clasificacionUrl = null)
    {
        $title = "Escorts";
        
        // Si es un filtro único por URL, darle prioridad
        if ($singleFilter) {
            switch ($singleFilter) {
                case 'nacionalidad':
                    if (!empty($filters['nacionalidad'])) {
                        $title = "Escorts " . $filters['nacionalidad'];
                    }
                    break;
                case 'servicios':
                    if ($clasificacionUrl && $clasificacionUrl['tipo'] === 'servicio') {
                        $title = "Escorts con servicio de " . ucwords($clasificacionUrl['valor']);
                    } elseif (!empty($filters['servicios'])) {
                        $title = "Escorts con servicio de " . reset($filters['servicios']);
                    }
                    break;
                case 'atributos':
                    if ($clasificacionUrl && $clasificacionUrl['tipo'] === 'atributo') {
                        $title = "Escorts " . ucwords($clasificacionUrl['valor']);
                    } elseif (!empty($filters['atributos'])) {
                        $title = "Escorts " . reset($filters['atributos']);
                    }
                    break;
                case 'categoria':
                    if (!empty($filters['categoria'])) {
                        $title = "Escorts " . $filters['categoria'];
                    }
                    break;
                case 'edad':
                    if (!empty($filters['edad'])) {
                        $title = "Escorts de " . $filters['edad']['min'] . " a " . $filters['edad']['max'] . " años";
                    }
                    break;
                case 'disponible':
                    $title = "Escorts disponibles ahora";
                    break;
                case 'resena':
                    $title = "Escorts con reseñas verificadas";
                    break;
            }
        } else {
            // Lógica existente para múltiples filtros
            if (!empty($filters['nacionalidad'])) {
                $title .= " " . $filters['nacionalidad'];
            }
            
            if (!empty($filters['categoria'])) {
                $title .= " " . $filters['categoria'];
            }
            
            if (!empty($filters['servicios'])) {
                $title .= " con servicio de " . reset($filters['servicios']);
            }
            
            if (!empty($filters['atributos'])) {
                $title .= " " . reset($filters['atributos']);
            }
        }
        
        // Agregar ciudad y sector
        $title .= " en " . $ciudad->nombre;
        if ($sector) {
            $title .= " - " . ucwords($sector);
        }
        
        return $title;
    }

}
