<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use App\Models\BlogArticle;
use App\Models\EscortLocation;
use App\Models\Posts;
use App\Models\Foro;
use App\Models\Servicio;
use App\Models\Atributo;
use App\Models\Sector;
use App\Models\Nacionalidad;
use App\Models\SeoTemplate;
use App\Models\Estado;
use App\Models\TYC;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use stdClass;
use Illuminate\Support\Facades\Cache;
use App\Models\MetaTag;


class InicioController extends Controller
{
    public function show($nombreCiudad, $sector = null, $filtros = null)
    {
        $seoText = null;
        try {
            // Carga inicial de ciudad y variables básicas
            $ciudadSeleccionada = Ciudad::where('url', $nombreCiudad)->firstOrFail();
            session(['ciudad_actual' => $ciudadSeleccionada->nombre]);
            $ciudades = Ciudad::all();
            $now = Carbon::now();
            $currentDay = strtolower($now->locale('es')->dayName);
            $currentTime = $now->format('H:i:s');

            // Variables para control de indexación y canonical
            $shouldIndex = true;
            $variableCount = 0;
            $canonicalUrl = url("/escorts-{$nombreCiudad}");
            $firstFilter = null;

            // Si es Santiago y tiene sector válido, añadirlo al canonical
            if ($sector && $ciudadSeleccionada->url === 'santiago' && $this->validarSector($sector)) {
                $canonicalUrl .= "/{$sector}";
            }

            // Procesar primer filtro para canonical
            if ($filtros) {
                $variableCount++;
                $firstFilter = $filtros;
                if ($variableCount === 1) {
                    $canonicalUrl .= "/{$filtros}";
                }
            }

            // Cargar datos desde la base de datos para filtros
            $servicios = Servicio::orderBy('posicion')->get();
            $atributos = Atributo::orderBy('posicion')->get();
            $nacionalidades = Nacionalidad::select('id', 'nombre', 'url')->orderBy('posicion')->get();
            $sectores = Sector::orderBy('nombre')->get();



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

            

            $categorias_especiales = ['premium', 'vip', 'de_lujo', 'de lujo', 'under'];
            
            if ($sector && str_contains($sector, '/')) {
                $partes = explode('/', $sector);
                $sector = $partes[0];
                
                // Si la segunda parte es una categoría especial
                if (in_array(strtolower($partes[1]), $categorias_especiales)) {
                    $categoria = strtolower($partes[1]);
                    if ($categoria === 'de lujo') {
                        $categoria = 'de_lujo';
                    }
                    request()->merge(['categoria' => $categoria]);
                } else {
                    $filtros = $partes[1];
                }
            }

           
// Procesamiento del sector
if ($sector) {
    // AQUÍ ES DONDE HAY QUE AGREGAR EL NUEVO CÓDIGO, justo antes del procesamiento normal del sector
    if (in_array(strtolower($sector), $categorias_especiales)) {
        $categoria = strtolower($sector);
        if ($categoria === 'de lujo') {
            $categoria = 'de_lujo';
        }
        request()->merge(['categoria' => $categoria]);
        $sector = null;  // Limpiamos el sector ya que es una categoría
    } else {
        // Primero buscamos el sector por su URL en la colección de sectores
        $sectorEncontrado = $sectores->first(function ($item) use ($sector) {
            return strtolower($item->url) === strtolower($sector);
        });

        if ($sectorEncontrado) {
            Log::info('Sector encontrado', ['id' => $sectorEncontrado->id, 'url' => $sectorEncontrado->url]);
            $query->where('sectores', $sectorEncontrado->id);  // La columna se llama 'sectores'
        } else {
            Log::info('Sector no encontrado', ['sector_buscado' => $sector]);
            // Si no se encuentra el sector, podría ser un filtro
            $filtros = $sector;
            $sector = null;
        }
    }
}
            // Procesamiento de filtros
            if ($filtros) {
                $variableCount++;
                $filtroEncontrado = false;

                // Buscar nacionalidad por URL
                $nacionalidadEncontrada = $nacionalidades->firstWhere('url', $filtros);
                if ($nacionalidadEncontrada) {
                    $query->where('nacionalidad', $nacionalidadEncontrada->id);
                    $filtroEncontrado = true;
                }
                // Procesar filtros de edad
                else if (preg_match('/^edad-(\d+)-(\d+)$/', $filtros, $matches)) {
                    $min = (int)$matches[1];
                    $max = (int)$matches[2];
                    $query->whereBetween('edad', [$min, $max]);
                    $filtroEncontrado = true;
                }
                // Procesar filtros de precio
                else if (preg_match('/^precio-(\d+)-(\d+)$/', $filtros, $matches)) {
                    $min = (int)$matches[1];
                    $max = (int)$matches[2];
                    $query->whereBetween('precio', [$min, $max]);
                    $filtroEncontrado = true;
                } // Procesar disponible
                else if ($filtros === 'disponible') {
                    $query->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                        $query->where('dia', 'LIKE', $currentDay)
                            ->where(function ($q) use ($currentTime) {
                                $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                                    ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                            });
                    });
                    $filtroEncontrado = true;
                }
                // Procesar reseña verificada
                else if ($filtros === 'resena-verificada') {
                    $query->whereHas('posts', function ($query) {
                        $query->where('id_blog', 16);
                    });
                    $filtroEncontrado = true;
                }
                // Procesar servicios y atributos
                else {
                    // Buscar en servicios
                    $servicioEncontrado = $servicios->firstWhere('url', $filtros);
                    if ($servicioEncontrado) {
                        Log::info('Servicio encontrado: ' . $servicioEncontrado->nombre);

                        $query->where(function ($subQuery) use ($servicioEncontrado) {
                            $subQuery->where(function ($q) use ($servicioEncontrado) {
                                $q->whereRaw('JSON_CONTAINS(servicios, ?, "$")', ['"' . $servicioEncontrado->id . '"'])
                                    ->orWhereRaw('JSON_CONTAINS(servicios_adicionales, ?, "$")', ['"' . $servicioEncontrado->id . '"']);
                            });
                        });

                        Log::info('SQL después del filtro', [
                            'sqlDespues' => $query->toSql(),
                            'bindingsDespues' => $query->getBindings()
                        ]);
                        $filtroEncontrado = true;
                    } else {
                        // Buscar en atributos
                        $atributoEncontrado = $atributos->firstWhere('url', $filtros);
                        if ($atributoEncontrado) {
                            Log::info('Atributo encontrado: ' . $atributoEncontrado->nombre);
                            $query->whereRaw('LOWER(atributos) LIKE ?', ['%' . $atributoEncontrado->id . '%']);
                            $filtroEncontrado = true;
                        }
                    }
                }

                // Si no se encontró ningún filtro válido, devolver 404
                if (!$filtroEncontrado) {
                    return response()->view('errors.404', [], 404);
                }
            } // Procesar categoría si existe
            if ($categoria = request()->get('categoria')) {
                $categoria = strtolower($categoria);
                $variableCount++;

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

                $query->where('categorias', $categoria);
            }

            // Procesar otros filtros desde query parameters
            if ($precio = request()->get('p')) {
                list($min, $max) = explode('-', $precio);
                $query->whereBetween('precio', [(int)$min, (int)$max]);
                $variableCount++;
            }

            if (request()->has('disponible')) {
                $query->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                    $query->where('dia', 'LIKE', $currentDay)
                        ->where(function ($q) use ($currentTime) {
                            $q->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                                ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                        });
                });
                $variableCount++;
            }

            if ($edad = request()->get('e')) {
                list($min, $max) = explode('-', $edad);
                $query->whereBetween('edad', [(int)$min, (int)$max]);
                $variableCount++;
            }

            if ($nacionalidad = request()->get('n')) {
                $nacionalidadEncontrada = $nacionalidades->firstWhere('url', $nacionalidad);
                if ($nacionalidadEncontrada) {
                    $query->where('nacionalidad', $nacionalidadEncontrada->id);
                    $variableCount++;
                }
            }

            if ($atributosRequest = request()->get('a')) { // URLs de atributos
                $atributosArray = explode(',', $atributosRequest);
                if (!empty($atributosArray)) {
                    // Debug: Verificar las URLs recibidas
                    //dump("URLs de atributos recibidas:", $atributosArray);

                    // Obtener los IDs de los atributos basados en las URLs
                    $atributosIds = DB::table('atributos')
                        ->whereIn('url', $atributosArray)
                        ->pluck('id')
                        ->toArray();

                    // Debug: Verificar los IDs obtenidos
                    // dump("IDs de atributos obtenidos:", $atributosIds);

                    if (!empty($atributosIds)) {
                        $query->where(function ($q) use ($atributosIds) {
                            foreach ($atributosIds as $atributoId) {
                                // Debug: Verificar el ID que se está usando en el filtro
                                //dump("Filtrando por atributo ID:", $atributoId);

                                // Filtrar por el ID del atributo en la columna `atributos`
                                $q->orWhereJsonContains('atributos', $atributoId);
                            }
                        });
                    }
                }
                $variableCount++;
            }

            if ($serviciosRequest = request()->get('s')) {
                $serviciosArray = explode(',', $serviciosRequest);
                if (!empty($serviciosArray)) {
                    $query->where(function ($q) use ($serviciosArray, $servicios) {
                        $serviciosLimitados = array_slice($serviciosArray, 0, 3);

                        Log::info('Buscando servicios:', [
                            'servicios_a_buscar' => $serviciosLimitados
                        ]);

                        $q->where(function ($subQuery) use ($serviciosLimitados, $servicios) {
                            foreach ($serviciosLimitados as $key => $servicioUrl) {
                                $servicio = $servicios->firstWhere('url', $servicioUrl);

                                Log::info('Servicio encontrado:', [
                                    'url' => $servicioUrl,
                                    'id' => $servicio ? $servicio->id : null
                                ]);

                                if ($servicio) {
                                    $subQuery->orWhere(function ($sq) use ($servicio) {
                                        $sq->whereRaw('JSON_CONTAINS(servicios, ?)', ['"' . $servicio->id . '"'])
                                            ->orWhereRaw('JSON_CONTAINS(servicios_adicionales, ?)', ['"' . $servicio->id . '"']);
                                    });
                                }
                            }
                        });
                    });
                }
                $variableCount++;
            }

            // Añadimos logs para ver la consulta final
            Log::info('Consulta final:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'raw_sql' => vsprintf(
                    str_replace('?', '%s', $query->toSql()),
                    array_map(function ($binding) {
                        return is_numeric($binding) ? $binding : "'{$binding}'";
                    }, $query->getBindings())
                )
            ]);

            if (request()->has('resena')) {
                $query->has('resenas');
                $variableCount++;
            }

            // Log de la consulta final
            Log::info('Consulta final', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'count' => $query->count()
            ]); // Consulta principal con filtros
            $usuarios = $query->with([
                'disponibilidad',
                'estados' => function ($query) {
                    $query->where('created_at', '>=', now()->subHours(24));
                },
                'location:id,usuario_publicate_id,direccion',
                'sector', // Agregamos la relación sector
                'posts'
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
                    'estadop',
                    'sectores', // Agregamos el campo sectores si es necesario
                    'descripcion_fotos'
                )
                ->orderBy('posicion', 'asc')
                ->paginate(40);

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
            $usuarioDestacado = UsuarioPublicate::with([
                'estados' => function ($query) {
                    $query->where('created_at', '>=', now()->subHours(24));
                },
                'location:id,usuario_publicate_id,direccion',
                'posts',
                'sector:id,nombre'  // Agregamos la relación sector
            ])
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
                    'estadop',
                    'sectores',
                    'descripcion_fotos'  // Agregamos el campo sectores
                )
                ->orderBy('updated_at', 'desc')
                ->first();
            $usuariosOnline = UsuarioPublicate::with([
                'disponibilidad' => function ($query) use ($currentDay, $currentTime) {
                    $query->where(function ($q) use ($currentDay, $currentTime) {
                        $q->where(function ($sub) use ($currentDay) {
                            $sub->where('dia', 'LIKE', $currentDay)
                                ->where('hora_desde', '00:00:00')
                                ->where('hora_hasta', '23:59:00');
                        })
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
                ->whereIn('estadop', [1, 3])
                ->where('ubicacion', $ciudadSeleccionada->nombre)
                ->whereHas('disponibilidad', function ($query) use ($currentDay, $currentTime) {
                    $query->where(function ($q) use ($currentDay, $currentTime) {
                        $q->where(function ($sub) use ($currentDay) {
                            $sub->where('dia', 'LIKE', $currentDay)
                                ->where('hora_desde', '00:00:00')
                                ->where('hora_hasta', '23:59:00');
                        })
                            ->orWhere(function ($sub) use ($currentDay, $currentTime) {
                                $sub->where('dia', 'LIKE', $currentDay)
                                    ->where(function ($timeQ) use ($currentTime) {
                                        $timeQ->whereRaw("(hora_hasta < hora_desde AND ('$currentTime' >= hora_desde OR '$currentTime' <= hora_hasta))")
                                            ->orWhereRaw("(hora_hasta >= hora_desde AND '$currentTime' BETWEEN hora_desde AND hora_hasta)");
                                    });
                            });
                    });
                })
                ->select('id', 'fantasia', 'edad', 'fotos', 'foto_positions', 'estadop','descripcion_fotos')
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
                },
                'location:id,usuario_publicate_id,direccion',
                'sector:id,nombre'  // Agregamos la relación sector
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
                    'estadop',
                    'sectores', // Agregamos el campo sectores
                    'descripcion_fotos'
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
                },
                'location:id,usuario_publicate_id,direccion',
                'sector:id,nombre'  // Agregamos la relación sector
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
                    'estadop',
                    'sectores',  // Agregamos el campo sectores
                    'descripcion_fotos'
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
                ->get(); // Procesar la ubicación para la vista

            $ubicacionesMostradas = [];

            // Procesar ubicaciones para usuarios principales
            foreach ($usuarios as $usuario) {
                if ($ciudadSeleccionada->url === 'santiago') {
                    if ($sectorValido) {
                        $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
                    } elseif ($usuario->sectores) {
                        $sectorInfo = Sector::find($usuario->sectores);
                        $ubicacionesMostradas[$usuario->id] = $sectorInfo ? $sectorInfo->nombre : 'Sector no disponible';
                    } else {
                        $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
                    }
                } else {
                    $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
                }
            }

            // Procesar ubicaciones para usuarios de primera vez y volvieron
            foreach ([$primeraVez, $volvieron] as $userGroup) {
                foreach ($userGroup as $usuario) {
                    if ($ciudadSeleccionada->url === 'santiago') {
                        if ($sectorValido) {
                            $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
                        } elseif ($usuario->sectores) {
                            $sectorInfo = Sector::find($usuario->sectores);
                            $ubicacionesMostradas[$usuario->id] = $sectorInfo ? $sectorInfo->nombre : 'Sector no disponible';
                        } else {
                            $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
                        }
                    } else {
                        $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
                    }
                }
            }

            // Procesar ubicación para usuario destacado si existe
            if ($usuarioDestacado) {
                if ($ciudadSeleccionada->url === 'santiago') {
                    if ($sectorValido) {
                        $ubicacionesMostradas[$usuarioDestacado->id] = ucwords(str_replace('-', ' ', $sectorValido));
                    } elseif ($usuarioDestacado->sectores) {
                        $sectorInfo = Sector::find($usuarioDestacado->sectores);
                        $ubicacionesMostradas[$usuarioDestacado->id] = $sectorInfo ? $sectorInfo->nombre : 'Sector no disponible';
                    } else {
                        $ubicacionesMostradas[$usuarioDestacado->id] = 'Sector no disponible';
                    }
                } else {
                    $ubicacionesMostradas[$usuarioDestacado->id] = $usuarioDestacado->ubicacion;
                }
            }

            // Generar SEO text basado en los filtros y configuración
            $seoText = $this->generateSeoText(request(), $ciudadSeleccionada, $sectorValido);


// Definir categorías especiales
$categorias_especiales = ['premium', 'vip', 'de_lujo', 'under'];

// Obtener los filtros de la URL
$pathParts = explode('/', trim(request()->path(), '/'));
$ciudadUrl = $pathParts[0] ?? '';

// Verificar segunda y tercera parte de la URL
$sectorUrl = $pathParts[1] ?? '';
$filtroUrl = $pathParts[2] ?? '';

// Si la segunda parte es una categoría especial, ajustamos la lógica
if (in_array(strtolower($sectorUrl), $categorias_especiales)) {
    $filtroUrl = $sectorUrl;
    $sectorUrl = '';
}

// Estructuras de control principales
$hasSector = false;
$hasFilter = false;
$hasAdditionalFilters = false;
$totalFilters = 0;

// Verificar si existe el sector
if ($sectorUrl) {
    $sector = Sector::where('url', $sectorUrl)->first();
    $hasSector = !empty($sector);
}

// Identificar tipo de filtro
$filterType = null;
$filterModel = null;
if ($filtroUrl) {
    // Verificar cada tipo de filtro incluyendo categorías
    if ($servicio = Servicio::where('url', $filtroUrl)->first()) {
        $filterType = 'servicio';
        $filterModel = $servicio;
    } elseif ($atributo = Atributo::where('url', $filtroUrl)->first()) {
        $filterType = 'atributo';
        $filterModel = $atributo;
    } elseif ($nacionalidad = Nacionalidad::where('url', $filtroUrl)->first()) {
        $filterType = 'nacionalidad';
        $filterModel = $nacionalidad;
    } elseif ($filtroUrl === 'resena-verificada') {
        $filterType = 'resena';
    } elseif (preg_match('/^precio-\d+-\d+$/', $filtroUrl)) {
        $filterType = 'precio';
    } elseif (preg_match('/^edad-\d+-\d+$/', $filtroUrl)) {
        $filterType = 'edad';
    } elseif (in_array($filtroUrl, $categorias_especiales)) {
        $filterType = 'categoria';
        request()->merge(['categoria' => $filtroUrl]);
    }

    $hasFilter = !empty($filterType);
}

// Contar filtros adicionales en GET
$getFilters = [
    's' => 'servicio',
    'a' => 'atributo',
    'n' => 'nacionalidad',
    'z' => 'sector',
    'categoria' => 'categoria',
    'resena-verificada' => 'resena',
    'edad' => 'edad',
    'precio' => 'precio'
];

$allGetFilters = [];

foreach ($getFilters as $param => $type) {
    if (request()->has($param)) {
        $values = explode(',', request()->get($param));
        $values = array_unique($values);

        // Si el filtro ya está en la URL, solo contar los adicionales
        if ($hasFilter && $filterType === $type) {
            $values = array_diff($values, [$filtroUrl]);
        }

        // Almacenar todos los valores para detectar duplicados
        foreach ($values as $value) {
            if (!in_array($value, $allGetFilters)) {
                $allGetFilters[] = $value;
                $totalFilters++;
            }
        }

        if (!empty($values)) {
            $hasAdditionalFilters = true;
        }
    }
}
           // Obtener los filtros de la URL
$pathParts = explode('/', trim(request()->path(), '/'));
$ciudadUrl = $pathParts[0] ?? '';

Log::info('URL inicial:', [
    'path' => request()->path(),
    'parts' => $pathParts,
    'ciudad' => $ciudadUrl
]);

// Primero verificamos si la segunda parte es un sector
$sectorUrl = $pathParts[1] ?? '';
$filtroUrl = $pathParts[2] ?? '';

// Si solo hay dos partes y la segunda NO es un sector válido, entonces es un filtro
if (count($pathParts) == 2) {
    $possibleSector = Sector::where('url', $sectorUrl)->first();
    if (!$possibleSector) {
        $filtroUrl = $sectorUrl;
        $sectorUrl = '';
    }
}

Log::info('Después de verificar sector:', [
    'sectorUrl' => $sectorUrl,
    'filtroUrl' => $filtroUrl
]);

// Estructuras de control principales
$hasSector = false;
$hasFilter = false;
$hasAdditionalFilters = false;
$totalFilters = 0;

// Verificar si existe el sector
if ($sectorUrl) {
    $sector = Sector::where('url', $sectorUrl)->first();
    $hasSector = !empty($sector);
}

// Identificar tipo de filtro
$filterType = null;
$filterModel = null;
if ($filtroUrl) {
    // Verificar cada tipo de filtro
    if ($servicio = Servicio::where('url', $filtroUrl)->first()) {
        $filterType = 'servicio';
        $filterModel = $servicio;
    } elseif ($atributo = Atributo::where('url', $filtroUrl)->first()) {
        $filterType = 'atributo';
        $filterModel = $atributo;
    } elseif ($nacionalidad = Nacionalidad::where('url', $filtroUrl)->first()) {
        $filterType = 'nacionalidad';
        $filterModel = $nacionalidad;
    } elseif ($filtroUrl === 'resena-verificada') {
        $filterType = 'resena';
    } elseif (preg_match('/^precio-\d+-\d+$/', $filtroUrl)) {
        $filterType = 'precio';
    } elseif (preg_match('/^edad-\d+-\d+$/', $filtroUrl)) {
        $filterType = 'edad';
    } elseif (in_array($filtroUrl, ['vip', 'premium', 'de_lujo', 'under'])) {
        $filterType = 'categoria';
    }

    $hasFilter = !empty($filterType);
}

Log::info('Después de identificar filtro:', [
    'filterType' => $filterType,
    'hasFilter' => $hasFilter,
    'hasSector' => $hasSector
]);

// Contar filtros adicionales en GET
$getFilters = [
    's' => 'servicio',
    'a' => 'atributo',
    'n' => 'nacionalidad',
    'z' => 'sector',
    'categoria' => 'categoria',
    'resena-verificada' => 'resena',
    'edad' => 'edad',
    'precio' => 'precio'
];

$allGetFilters = [];

foreach ($getFilters as $param => $type) {
    if (request()->has($param)) {
        $values = explode(',', request()->get($param));
        $values = array_unique($values);

        // Si el filtro ya está en la URL, solo contar los adicionales
        if ($hasFilter && $filterType === $type) {
            $values = array_diff($values, [$filtroUrl]);
        }

        // Almacenar todos los valores para detectar duplicados
        foreach ($values as $value) {
            if (!in_array($value, $allGetFilters)) {
                $allGetFilters[] = $value;
                $totalFilters++;
            }
        }

        if (!empty($values)) {
            $hasAdditionalFilters = true;
        }
    }
}

Log::info('Estado final de flags antes de procesar meta tags:', [
    'hasFilter' => $hasFilter,
    'hasSector' => $hasSector,
    'hasAdditionalFilters' => $hasAdditionalFilters,
    'totalFilters' => $totalFilters
]);

// Obtener meta tags base
$baseMetaTag = MetaTag::where('page', 'inicio-' . $ciudadSeleccionada->id)->first();
Log::info('Meta tag base:', [
    'page' => 'inicio-' . $ciudadSeleccionada->id,
    'encontrado' => !empty($baseMetaTag),
    'title' => $baseMetaTag?->meta_title
]);

$baseTitle = $baseMetaTag?->meta_title ?? 'Escorts en ' . ucfirst($ciudadSeleccionada->nombre);
$baseDescription = $baseMetaTag?->meta_description ?? 'Encuentra escorts en ' . ucfirst($ciudadSeleccionada->nombre) . ' disponibles hoy.';

// Inicializar valores
$title = $baseTitle;
$description = $baseDescription;
$metaRobots = 'index,follow';
$canonicalUrl = url($ciudadUrl);
$metaTagData = $baseMetaTag?->toArray() ?? [];
$metaTag = $baseMetaTag;

Log::info('Valores inicializados:', [
    'title' => $title,
    'description' => $description,
    'metaRobots' => $metaRobots,
    'canonicalUrl' => $canonicalUrl,
    'hasMetaTagData' => !empty($metaTagData)
]);

// Procesar según el tipo de URL
if ($hasFilter && !$hasSector) {
    Log::info('Procesando: Ciudad + Filtro');
    if ($hasAdditionalFilters) {
        Log::info('Tiene filtros adicionales - usando configuración base');
        $title = $baseTitle;
        $description = $baseDescription;
        $metaTagData = $baseMetaTag?->toArray() ?? [];
        $metaTag = $baseMetaTag;
        $metaRobots = 'noindex,follow';
        $canonicalUrl = url($ciudadUrl);
    } else {
        Log::info('Sin filtros adicionales - procesando por tipo:', ['filterType' => $filterType]);
        $metaTag = null;
        switch ($filterType) {
            case 'servicio':
                $metaTag = MetaTag::where('page', 'seo/servicios/' . $filterModel->id . '/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/servicios/' . $filterModel->id)->first();
                }
                $title = $metaTag?->meta_title ?? "Escorts con servicios como {$filterModel->nombre} en " . ucfirst($ciudadSeleccionada->nombre);
                break;

            case 'atributo':
                $metaTag = MetaTag::where('page', 'seo/atributos/' . $filterModel->id . '/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/atributos/' . $filterModel->id)->first();
                }
                $title = $metaTag?->meta_title ?? "Escorts con {$filterModel->nombre} en " . ucfirst($ciudadSeleccionada->nombre);
                break;

            case 'nacionalidad':
                $metaTag = MetaTag::where('page', 'seo/nacionalidades/' . $filterModel->id . '/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/nacionalidades/' . $filterModel->id)->first();
                }
                $title = $metaTag?->meta_title ?? "Escorts de {$filterModel->nombre} en " . ucfirst($ciudadSeleccionada->nombre);
                break;

            case 'resena':
                $metaTag = MetaTag::where('page', 'seo/resenas/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/resenas')->first();
                }
                $title = $metaTag?->meta_title ?? "Reseñas verificadas de escorts en " . ucfirst($ciudadSeleccionada->nombre);
                break;

            case 'disponibilidad':
                $metaTag = MetaTag::where('page', 'seo/disponibilidad/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/disponibilidad')->first();
                }
                $title = $metaTag?->meta_title ?? "Escorts disponibles ahora en " . ucfirst($ciudadSeleccionada->nombre);
                break;

            case 'categoria':
                Log::info('Procesando meta tags de categoría:', ['categoria' => $filtroUrl]);
                $metaTag = MetaTag::where('page', 'seo/categorias/' . $filtroUrl . '/ciudad/' . $ciudadSeleccionada->id)->first();
                if (!$metaTag) {
                    $metaTag = MetaTag::where('page', 'seo/categorias/' . $filtroUrl)->first();
                }
                $categoriaNombre = str_replace('_', ' ', ucfirst($filtroUrl));
                $title = $metaTag?->meta_title ?? "Escorts $categoriaNombre en " . ucfirst($ciudadSeleccionada->nombre);
                break;
        }

        if ($metaTag) {
            $description = $metaTag->meta_description;
            $metaTagData = $metaTag->toArray();
            $metaRobots = $metaTag->meta_robots ?? 'index,follow'; // Usamos el meta_robots del metatag si existe
        } else {
            $metaRobots = 'index,follow'; // Valor por defecto si no hay metatag
        }

        $canonicalUrl = url($ciudadUrl . '/' . $filtroUrl);
    }
} else if ($hasSector && $hasFilter) {
    Log::info('Procesando: Ciudad + Sector + Filtro', [
        'sector' => $sectorUrl,
        'filtro' => $filtroUrl,
        'filterType' => $filterType
    ]);

    // Si es una categoría, procesar de manera especial
    if ($filterType === 'categoria') {
        Log::info('Procesando meta tag de categoría con sector');
        
        // Intentar obtener meta tag específico de la ciudad
        $metaTag = MetaTag::where('page', 'seo/categorias/' . $filtroUrl . '/ciudad/' . $ciudadSeleccionada->id)->first();
        Log::info('Búsqueda meta tag específico de ciudad para categoría:', [
            'path' => 'seo/categorias/' . $filtroUrl . '/ciudad/' . $ciudadSeleccionada->id,
            'encontrado' => !empty($metaTag)
        ]);
        
        // Si no existe, usar el genérico
        if (!$metaTag) {
            $metaTag = MetaTag::where('page', 'seo/categorias/' . $filtroUrl)->first();
            Log::info('Búsqueda meta tag genérico para categoría:', [
                'path' => 'seo/categorias/' . $filtroUrl,
                'encontrado' => !empty($metaTag)
            ]);
        }
        
        if ($metaTag) {
            $title = $metaTag->meta_title;
            $description = $metaTag->meta_description;
            $metaTagData = $metaTag->toArray();
            $metaRobots = $metaTag->meta_robots ?? 'index,follow'; // Usamos el meta_robots del metatag
            Log::info('Usando meta tag encontrado para categoría');
        } else {
            $categoriaNombre = str_replace('_', ' ', ucfirst($filtroUrl));
            $title = "Escorts $categoriaNombre en " . ucfirst($sector->nombre) . " de " . ucfirst($ciudadSeleccionada->nombre);
            $description = "Descubre escorts $categoriaNombre en " . ucfirst($sector->nombre) . " de " . ucfirst($ciudadSeleccionada->nombre);
            $metaRobots = 'index,follow'; // Valor por defecto
            Log::info('Generando título por defecto para categoría con sector', ['title' => $title]);
        }
        
        $canonicalUrl = url($ciudadUrl . '/' . $sectorUrl . '/' . $filtroUrl);
    } else {
        // Para otros tipos de filtros, mantener el comportamiento actual
        Log::info('Usando configuración base para filtro no-categoría con sector');
        $title = $baseTitle;
        $description = $baseDescription;
        $metaTagData = $baseMetaTag?->toArray() ?? [];
        $metaTag = $baseMetaTag;
        $metaRobots = 'noindex,follow';
        $canonicalUrl = url($ciudadUrl);
    }
} else if ($hasSector) {
    Log::info('Procesando: Solo Ciudad + Sector');
    $metaTag = MetaTag::where('page', 'seo/sectores/' . $sector->id . '/ciudad/' . $ciudadSeleccionada->id)->first();
    if (!$metaTag) {
        $metaTag = MetaTag::where('page', 'seo/sectores/' . $sector->id)->first();
    }

    if ($metaTag) {
        $title = $metaTag->meta_title;
        $description = $metaTag->meta_description;
        $metaTagData = $metaTag->toArray();
        $metaRobots = $metaTag->meta_robots ?? 'index,follow'; // Usamos el meta_robots del metatag
    } else {
        $title = "Escorts en " . ucfirst($sector->nombre) . " de " . ucfirst($ciudadSeleccionada->nombre);
        $description = "Descubre escorts en " . ucfirst($sector->nombre) . " de " . ucfirst($ciudadSeleccionada->nombre);
        $metaRobots = 'index,follow'; // Valor por defecto
    }

    if ($hasAdditionalFilters) {
        $metaRobots = 'noindex,follow';
        $canonicalUrl = url($ciudadUrl);
    } else {
        $canonicalUrl = url($ciudadUrl . '/' . $sectorUrl);
    }
}

// Para cualquier combinación con 2 o más filtros, noindex
if ($totalFilters >= 2) {
    Log::info('Detectados 2 o más filtros - aplicando noindex', ['totalFilters' => $totalFilters]);
    $metaRobots = 'noindex,follow';
    $canonicalUrl = url($ciudadUrl);
}

Log::info('Valores finales de meta tags:', [
    'title' => $title,
    'description' => $description,
    'metaRobots' => $metaRobots,
    'canonicalUrl' => $canonicalUrl,
    'hasMetaTagData' => !empty($metaTagData),
    'keywords' => $metaTag?->meta_keywords ?? ($baseMetaTag?->meta_keywords ?? '')
]);

// Compartir datos con la vista
view()->share([
    'pageTitle' => $title,
    'metaDescription' => $description,
    'metaKeywords' => $metaTag?->meta_keywords ?? ($baseMetaTag?->meta_keywords ?? ''),
    'metaRobots' => $metaRobots,
    'canonicalUrl' => $canonicalUrl,
    'metaTagData' => $metaTagData,
]);

$breadcrumb = [];
$breadcrumb[] = [
    'text' => trim('Inicio'), // Usando trim para limpiar
    'url' => url('')
];

$breadcrumb[] = [
    'text' => trim(ucfirst($ciudadSeleccionada->nombre)), // Usando trim para limpiar
    'url' => url("escorts-{$ciudadSeleccionada->url}")
];

// Si hay sector válido (para Santiago)
if ($sector && $ciudadSeleccionada->url === 'santiago' && $this->validarSector($sector)) {
    $sectorInfo = Sector::where('url', $sector)->first();
    if ($sectorInfo) {
        $breadcrumb[] = [
            'text' => ucfirst($sectorInfo->nombre),
            'url' => url("/escorts-{$ciudadSeleccionada->url}/{$sector}")
        ];
    }
}

// Si hay filtro (pero no es un sector inválido redirigido a filtro)
if ($filtros && (!$sector || ($sector && $this->validarSector($sector)))) {
    // Determinar el texto del filtro basado en el tipo
    $filterText = '';
    
    // Buscar en nacionalidades
    $nacionalidad = $nacionalidades->firstWhere('url', $filtros);
    if ($nacionalidad) {
        $filterText = ucfirst($nacionalidad->nombre);
    }
    // Buscar en servicios
    elseif ($servicio = $servicios->firstWhere('url', $filtros)) {
        $filterText = ucfirst($servicio->nombre);
    }
    // Buscar en atributos
    elseif ($atributo = $atributos->firstWhere('url', $filtros)) {
        $filterText = ucfirst($atributo->nombre);
    }
    // Verificar otros tipos de filtros
    elseif (preg_match('/^edad-(\d+)-(\d+)$/', $filtros, $matches)) {
        $filterText = "Edad {$matches[1]}-{$matches[2]}";
    }
    elseif (preg_match('/^precio-(\d+)-(\d+)$/', $filtros, $matches)) {
        $filterText = "Precio {$matches[1]}-{$matches[2]}";
    }
    elseif ($filtros === 'disponible') {
        $filterText = "Disponible ahora";
    }
    elseif ($filtros === 'resena-verificada') {
        $filterText = "Reseña verificada";
    }
    
    if ($filterText) {
        $breadcrumb[] = [
            'text' => $filterText,
            'url' => null // El último elemento no tiene enlace
        ];
    }
}

// Compartir el breadcrumb con la vista
view()->share('breadcrumb', $breadcrumb);

            // Retornar vista con todos los datos
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
                'sectores' => $sectores,
                'nacionalidades' => $nacionalidades,
                'servicios' => $servicios,
                'atributos' => $atributos,
                'experiencias' => $experiencias,
                'ubicacionesMostradas' => $ubicacionesMostradas,
                'meta' => $metaTag,
                'now' => Carbon::now()
            ], $seoText ? [
                'seoTitle' => $seoText['title'],
                'seoDescription' => $seoText['description']
            ] : []));
        } catch (\Exception $e) {
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
                'estadop',
                'descripcion_fotos'
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

    public function showPerfil($nombre)
{
    try {
        // Verificar si la URL contiene mayúsculas
        if ($nombre !== strtolower($nombre)) {
            // Redireccionar a la versión en minúsculas
            return redirect()->to('/escorts/' . strtolower($nombre), 301);
        }

        // Extraer el ID del nombre
        $id = substr($nombre, strrpos($nombre, '-') + 1);

        // El resto del código permanece igual
        $usuarioPublicate = UsuarioPublicate::with([
            'disponibilidad',
            'estados' => function ($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            },
            'nacionalidadRelacion'
        ])
            ->leftJoin('ciudades', 'usuarios_publicate.ubicacion', '=', 'ciudades.nombre')
            ->select('usuarios_publicate.*', 'ciudades.url as ciudad_url', 'ciudades.nombre as ciudad_nombre')
            ->findOrFail($id);

        // Crear objeto meta para SEO
        $meta = new stdClass();
        $meta->meta_title = $usuarioPublicate->fantasia . ' Escort ' .
            ($usuarioPublicate->categorias ? ucfirst(strtolower($usuarioPublicate->categorias)) . ' ' : '') .
            'en ' . $usuarioPublicate->ubicacion . ' | OnlyEscorts';
        $meta->canonical_url = url("/escorts/{$nombre}");
        
        // Definir canonicalUrl para compatibilidad con el layout
        $canonicalUrl = url("/escorts/{$nombre}");

        $ciudades = Ciudad::all();
        $servicios = Servicio::orderBy('posicion')->get();
        $atributos = Atributo::orderBy('posicion')->get();
        $nacionalidades = Nacionalidad::orderBy('posicion')->get();
        $sectores = Sector::orderBy('nombre')->get();

        return view('showescort', compact(
            'usuarioPublicate', 
            'ciudades', 
            'sectores', 
            'nacionalidades', 
            'atributos', 
            'servicios',
            'meta',
            'canonicalUrl'
        ));

    } catch (\Exception $e) {
        \Log::error('Error en showPerfil: ' . $e->getMessage());
        return abort(404);
    }
}
    


    public function RTA()
    {
        $ciudades = Ciudad::all();
        $servicios = Servicio::orderBy('posicion')->get();
        $atributos = Atributo::orderBy('posicion')->get();
        $nacionalidades = Nacionalidad::orderBy('posicion')->get();
        $sectores = Sector::orderBy('nombre')->get();

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
            'sectores' => $sectores,
            'nacionalidades' => $nacionalidades,
            'servicios' => $servicios,
            'atributos' => $atributos,
            'usuarioPublicate' => $usuarioPublicate
        ]);
    }

    public function TYC()
    {
        // Obtener los valores de la base de datos
        $tyc = TYC::first();
        $ciudades = Ciudad::all();
        $servicios = Servicio::orderBy('posicion')->get();
        $atributos = Atributo::orderBy('posicion')->get();
        $nacionalidades = Nacionalidad::orderBy('posicion')->get();
        $sectores = Sector::orderBy('nombre')->get();

        // Si no hay valores en la base de datos, usar valores por defecto
        $title = $tyc ? $tyc->title : "Términos y Condiciones";
        $content = $tyc ? $tyc->content : "OnlyEscorts está calificado con la etiqueta RTA. Padres, pueden bloquear fácilmente el acceso a este sitio. Por favor, lean esta página";

        // Mantener la lógica existente
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

        // Retornar la vista con todas las variables
        return view('tyc', [
            'title' => $title,
            'content' => $content,
            'ciudades' => $ciudades,
            'sectores' => $sectores,
            'nacionalidades' => $nacionalidades,
            'servicios' => $servicios,
            'atributos' => $atributos,
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

        $path = $request->path();
        $parts = explode('/', $path);
        $lastPart = strtolower(end($parts));

        $clasificacionUrl = null;
        $singleFilter = null;
        $isNationalityFilter = false;
        $nacionalidadFromUrl = null;

        // Detectar filtros en la URL
        if (preg_match('/^edad-(\d+)-(\d+)$/', $lastPart, $matches)) {
            $request->merge(['e' => "{$matches[1]}-{$matches[2]}"]);
            $singleFilter = 'edad';
            $request->merge(['singleFilter' => 'edad']);
        } elseif (preg_match('/^precio-(\d+)-(\d+)$/', $lastPart, $matches)) {
            $request->merge(['p' => "{$matches[1]}-{$matches[2]}"]);
            $singleFilter = 'precio';
        } elseif ($lastPart === 'disponible') {
            $request->merge(['disponible' => '1']);
            $singleFilter = 'disponible';
        } elseif ($lastPart === 'resena-verificada') {
            $request->merge(['resena' => '1']);
            $singleFilter = 'resena';
        } elseif (in_array($lastPart, ['vip', 'premium', 'de_lujo', 'under'])) {
            $request->merge(['categoria' => $lastPart]);
            $singleFilter = 'categoria';
        } else {
            // Primero buscar si es un servicio
            $servicio = Servicio::where('url', $lastPart)->first();
            if ($servicio) {
                if ($request->has('s')) {
                    $servicios = explode(',', $request->get('s'));
                    $servicios[] = $servicio->url;
                    $request->merge(['s' => implode(',', array_unique($servicios))]);
                } else {
                    $request->merge(['s' => $servicio->url]);
                }
                $singleFilter = 'servicios';
            } else {
                // Buscar si es una nacionalidad
                $nacionalidad = Nacionalidad::where('url', $lastPart)->first();
                if ($nacionalidad) {
                    $isNationalityFilter = true;
                    $nacionalidadFromUrl = $nacionalidad->url;
                    $request->merge(['n' => $nacionalidadFromUrl]);
                    $singleFilter = 'nacionalidad';
                } else {
                    // Buscar si es un atributo
                    $atributo = Atributo::where('url', $lastPart)->first();
                    if ($atributo) {
                        if ($request->has('a')) {
                            $atributos = explode(',', $request->get('a'));
                            $atributos[] = $atributo->url;
                            $request->merge(['a' => implode(',', array_unique($atributos))]);
                        } else {
                            $request->merge(['a' => $atributo->url]);
                        }
                        $singleFilter = 'atributos';
                    } else {
                        // Buscar si es un sector
                        $sector = Sector::where('url', $lastPart)->first();
                        if ($sector) {
                            $sectorSeleccionado = $sector->nombre;
                            $singleFilter = 'sector';
                        }
                    }
                }
            }
        }

        $activeFilters = [
            'nacionalidad' => null,
            'edad' => null,
            'precio' => null,
            'atributos' => [],
            'servicios' => [],
            'categoria' => null,
            'disponible' => false,
            'resena' => false,
            'sector' => null
        ];

        // Procesar sector si existe
        if ($sectorSeleccionado) {
            $activeFilters['sector'] = $sectorSeleccionado;
        }

        if ($request->has('n') || $isNationalityFilter) {
            $nacionalidadUrl = $request->get('n') ?? $nacionalidadFromUrl;
            $nacionalidad = Nacionalidad::where('url', $nacionalidadUrl)->first();
            if ($nacionalidad) {
                $activeFilters['nacionalidad'] = $nacionalidad->nombre_plural;
            }
            if ($singleFilter === null) {
                $singleFilter = 'nacionalidad';
            }
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
            $atributos = explode(',', $request->get('a'));
            $activeFilters['atributos'] = array_map(function ($atributoUrl) {
                $atributo = Atributo::where('url', $atributoUrl)->first();
                return $atributo ? $atributo->nombre : ucwords(str_replace('-', ' ', $atributoUrl));
            }, $atributos);
        }

        if ($request->has('s')) {
            $servicios = explode(',', $request->get('s'));
            $activeFilters['servicios'] = array_map(function ($servicioUrl) {
                $servicio = Servicio::where('url', $servicioUrl)->first();
                return $servicio ? $servicio->nombre : ucwords(str_replace('-', ' ', $servicioUrl));
            }, $servicios);
        }

        if ($request->has('categoria')) {
            $categoria = $request->get('categoria');
            $activeFilters['categoria'] = ucwords(str_replace('_', ' ', $categoria));
        }

        $activeFilters['disponible'] = $request->has('disponible');
        $activeFilters['resena'] = $request->has('resena');

        $totalActiveFilters = 0;
        if (!empty($activeFilters['nacionalidad'])) $totalActiveFilters++;
        if (!empty($activeFilters['edad'])) $totalActiveFilters++;
        if (!empty($activeFilters['precio'])) $totalActiveFilters++;
        if (!empty($activeFilters['atributos'])) $totalActiveFilters++;
        if (!empty($activeFilters['servicios'])) $totalActiveFilters++;
        if (!empty($activeFilters['categoria'])) $totalActiveFilters++;
        if ($activeFilters['disponible'] === true) $totalActiveFilters++;
        if ($activeFilters['resena'] === true) $totalActiveFilters++;
        if (!empty($activeFilters['sector'])) $totalActiveFilters++;

        if ($totalActiveFilters === 0) {
            $templateType = 'filtro';
            $filtroType = 'ciudad';
        } elseif ($totalActiveFilters === 1) {
            $templateType = 'filtro';
            $filtroType = match ($singleFilter) {
                'nacionalidad' => 'nacionalidad',
                'edad' => 'edad',
                'precio' => 'precio',
                'disponible' => 'disponible',
                'resena' => 'resena',
                'categoria' => 'categorias',
                'servicios' => 'servicios',
                'atributos' => 'atributos',
                'sector' => 'sector',
                default => $singleFilter
            };
        } elseif ($totalActiveFilters >= 2 && $totalActiveFilters <= 4) {
            $templateType = 'multiple';
            $filtroType = null;
        } else {
            $templateType = 'complex';
            $filtroType = null;
        }

        Log::info('Template selection', [
            'tipo' => $templateType,
            'filtro' => $filtroType,
            'total_filtros' => $totalActiveFilters,
            'single_filter' => $singleFilter,
            'active_filters' => $activeFilters
        ]);

        $templateQuery = SeoTemplate::query()
            ->where(function ($query) use ($ciudadSeleccionada) {
                $query->where('ciudad_id', $ciudadSeleccionada->id)
                    ->orWhereNull('ciudad_id');
            })
            ->where('tipo', $templateType);

        if ($singleFilter === 'servicios' && $servicio = Servicio::where('url', $lastPart)->first()) {
            $templateQuery->where('filtro', 'servicios');
        } elseif ($singleFilter === 'nacionalidad' && $nacionalidad = Nacionalidad::where('url', $lastPart)->first()) {
            $templateQuery->where('filtro', 'nacionalidad');
        } elseif ($singleFilter === 'atributos' && $atributo = Atributo::where('url', $lastPart)->first()) {
            $templateQuery->where('filtro', 'atributos');
        } elseif ($singleFilter === 'sector' && $sector = Sector::where('url', $lastPart)->first()) {
            $templateQuery->where('filtro', 'ciudad');
        } else {
            $templateQuery->where('filtro', $filtroType);
        }

        $template = $templateQuery->orderBy('ciudad_id', 'desc')->first();

        if (!$template) {
            Log::warning('No template found for SEO text');
            return null;
        }

        $replacements = [
            '{ciudad}' => $ciudadSeleccionada->nombre,
            '{sector}' => $sectorSeleccionado ? ucwords($sectorSeleccionado) : '',
            '{nacionalidad}' => $activeFilters['nacionalidad'] ?? '',
            '{edad_min}' => $activeFilters['edad']['min'] ?? '18',
            '{edad_max}' => $activeFilters['edad']['max'] ?? '50',
            '{precio_min}' => $activeFilters['precio']['min'] ?? '50.000',
            '{precio_max}' => $activeFilters['precio']['max'] ?? '300.000',
            '{atributos}' => !empty($activeFilters['atributos']) ? implode(', ', array_unique($activeFilters['atributos'])) : '',
            '{servicios}' => !empty($activeFilters['servicios']) ? implode(', ', array_unique($activeFilters['servicios'])) : '',
            '{disponible}' => $activeFilters['disponible'] ? 'ahora' : '',
            '{resena}' => $activeFilters['resena'] ? 'verificada' : '',
            '{categorias}' => $activeFilters['categoria'] ?? ''
        ];

        $title = $this->generateSeoTitle($ciudadSeleccionada, $sectorSeleccionado, $activeFilters, $singleFilter);

        $description = $template->description_template;
        foreach ($replacements as $key => $value) {
            $description = str_replace($key, $value, $description);
        }

        $description = preg_replace('/\{[^}]+\}/', '', $description);
        $description = trim($description);

        Log::info('SEO text generado', [
            'title' => $title,
            'description' => $description,
            'template_type' => $templateType,
            'active_filters' => $activeFilters
        ]);

        return [
            'title' => $title,
            'description' => $description
        ];
    }

    private function generateSeoTitle($ciudad, $sector, $filters, $singleFilter = null)
    {
        $title = "Escorts";

        if ($singleFilter) {
            switch ($singleFilter) {
                case 'nacionalidad':
                    if (!empty($filters['nacionalidad'])) {
                        if ($nacionalidad = Nacionalidad::where('nombre_plural', $filters['nacionalidad'])->first()) {
                            $title = "Escorts " . $nacionalidad->nombre_plural;
                        } else {
                            $title = "Escorts " . $filters['nacionalidad'];
                        }
                    }
                    break;
                case 'servicios':
                    if (!empty($filters['servicios'])) {
                        if (count($filters['servicios']) > 1) {
                            $serviciosList = array_map(function ($servicio) {
                                $servicioObj = Servicio::where('nombre', $servicio)->first();
                                return $servicioObj ? $servicioObj->nombre : $servicio;
                            }, $filters['servicios']);
                            $title = "Escorts con servicios de " . implode(' y ', $serviciosList);
                        } else {
                            $servicio = Servicio::where('nombre', reset($filters['servicios']))->first();
                            $title = "Escorts con servicio de " . ($servicio ? $servicio->nombre : reset($filters['servicios']));
                        }
                    }
                    break;
                case 'atributos':
                    if (!empty($filters['atributos'])) {
                        if (count($filters['atributos']) > 1) {
                            $atributosList = array_map(function ($atributo) {
                                $atributoObj = Atributo::where('nombre', $atributo)->first();
                                return $atributoObj ? $atributoObj->nombre : $atributo;
                            }, $filters['atributos']);
                            $title = "Escorts con " . implode(' y ', $atributosList);
                        } else {
                            $atributo = Atributo::where('nombre', reset($filters['atributos']))->first();
                            $title = "Escorts " . ($atributo ? $atributo->nombre : reset($filters['atributos']));
                        }
                    }
                    break;
                case 'sector':
                    if ($sector) {
                        $sectorObj = Sector::where('nombre', $sector)->first();
                        $title = "Escorts en " . ($sectorObj ? $sectorObj->nombre : $sector);
                    }
                    break;
                case 'categoria':
                    if (!empty($filters['categoria'])) {
                        $title = "Escorts " . $filters['categoria'];
                    }
                    break;
                case 'precio':
                    if (!empty($filters['precio'])) {
                        $title = "Escorts desde $" . $filters['precio']['min'] . " hasta $" . $filters['precio']['max'];
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
            if (!empty($filters['nacionalidad'])) {
                if ($nacionalidad = Nacionalidad::where('nombre_plural', $filters['nacionalidad'])->first()) {
                    $title .= " " . $nacionalidad->nombre_plural;
                } else {
                    $title .= " " . $filters['nacionalidad'];
                }
            }
            if (!empty($filters['categoria'])) {
                $title .= " " . $filters['categoria'];
            }
            if (!empty($filters['servicios'])) {
                if (count($filters['servicios']) > 1) {
                    $serviciosList = array_map(function ($servicio) {
                        $servicioObj = Servicio::where('nombre', $servicio)->first();
                        return $servicioObj ? $servicioObj->nombre : $servicio;
                    }, $filters['servicios']);
                    $title .= " con servicios de " . implode(' y ', $serviciosList);
                } else {
                    $servicio = Servicio::where('nombre', reset($filters['servicios']))->first();
                    $title .= " con servicio de " . ($servicio ? $servicio->nombre : reset($filters['servicios']));
                }
            }
            if (!empty($filters['atributos'])) {
                if (count($filters['atributos']) > 1) {
                    $atributosList = array_map(function ($atributo) {
                        $atributoObj = Atributo::where('nombre', $atributo)->first();
                        return $atributoObj ? $atributoObj->nombre : $atributo;
                    }, $filters['atributos']);
                    $title .= " con " . implode(' y ', $atributosList);
                } else {
                    $atributo = Atributo::where('nombre', reset($filters['atributos']))->first();
                    $title .= " " . ($atributo ? $atributo->nombre : reset($filters['atributos']));
                }
            }
            if (!empty($filters['edad'])) {
                $title .= " de " . $filters['edad']['min'] . " a " . $filters['edad']['max'] . " años";
            }
            if (!empty($filters['precio'])) {
                $title .= " desde $" . $filters['precio']['min'] . " hasta $" . $filters['precio']['max'];
            }
            if ($filters['disponible'] === true) {
                $title .= " disponibles ahora";
            }
            if ($filters['resena'] === true) {
                $title .= " con reseñas verificadas";
            }
        }

        $title .= " en " . $ciudad->nombre;
        if ($sector) {
            $sectorObj = Sector::where('nombre', $sector)->first();
            $title .= " - " . ($sectorObj ? $sectorObj->nombre : ucwords($sector));
        }

        return $title;
    }


    protected function obtenerResenas($usuarioId)
    {
        $resenas = Posts::with(['comentarios.usuario', 'foro', 'chica'])
            ->whereHas('comentarios', function ($query) use ($usuarioId) {
                $query->where('id_usuario', $usuarioId); // Filtrar por el usuario
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $resenas;
    }
}
