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
use App\Models\MetaTag;


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
                // Si solo hay un filtro, añadirlo al canonical
                if ($variableCount === 1) {
                    $canonicalUrl .= "/{$filtros}";
                }
            }

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
                $variableCount++;
                $sector = null;
            }

            // Separar sector y filtro si vienen juntos
            if ($sector && str_contains($sector, '/')) {
                $partes = explode('/', $sector);
                $sector = $partes[0];
                $filtros = $partes[1] ?? null;

                Log::info('Separando sector y filtro', [
                    'sector_original' => $sector,
                    'sector_limpio' => $partes[0],
                    'filtro' => $partes[1] ?? null
                ]);
            }

            // Procesamiento del sector si existe y es válido
            if ($sector && $this->validarSector($sector)) {
                $sectorValido = str_replace('-', ' ', $sector);
                Log::info('Buscando sector', ['sector' => $sectorValido]);

                $query->whereHas('location', function ($q) use ($sectorValido) {
                    // Buscar el sector en la dirección
                    $q->where('direccion', 'LIKE', "%{$sectorValido}%")
                        ->orWhere('direccion', 'LIKE', "%, {$sectorValido},%");
                });
            }

            // Si hay un sector que no es válido, podría ser un filtro
            if ($sector && !$this->validarSector($sector) && !in_array(strtolower($sector), $categorias_especiales)) {
                $filtros = $sector;
                $sector = null;
            }

            // Procesamiento de filtros
            if ($filtros) {
                $variableCount++;

                $filtroNormalizado = str_replace('-', ' ', $filtros);
                $nacionalidadMap = [
                    'colombiana' => 'colombia',
                    'argentina' => 'argentina',
                    'brasilena' => 'brasil',
                    'chilena' => 'chile',
                    'ecuatoriana' => 'ecuador',
                    'uruguaya' => 'uruguay',
                    'venezolana' => 'venezuela',
                    'paraguaya' => 'paraguay',
                    'peruana' => 'peru'
                ];

                if (array_key_exists($filtroNormalizado, $nacionalidadMap)) {
                    Log::info('Aplicando filtro de nacionalidad', [
                        'filtro' => $filtroNormalizado,
                        'nacionalidad' => $nacionalidadMap[$filtroNormalizado]
                    ]);
                    $query->where('nacionalidad', $nacionalidadMap[$filtroNormalizado]);
                }
                // Procesar filtros de edad
                else if (preg_match('/^edad-(\d+)-(\d+)$/', $filtros, $matches)) {
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
                    Log::info('Procesando filtro: ' . $filtroNormalizado);

                    $filtroNormalizado = $this->normalizarString($filtroNormalizado);
                    $filtroNormalizado = normalizer_normalize(mb_strtolower($filtroNormalizado, 'UTF-8'));

                    // Lista de servicios y atributos exactos
                    $servicios = [
                        "Anal",
                        "Atencion a domicilio",
                        "Atencion en hoteles",
                        "Baile erotico",
                        "Besos",
                        "Cambio de rol",
                        "Departamento propio",
                        "Disfraces",
                        "Ducha erotica",
                        "Eventos y cenas",
                        "Eyaculacion cuerpo",
                        "Eyaculacion facial",
                        "Hetero",
                        "Juguetes",
                        "Lesbico",
                        "Lluvia dorada",
                        "Masaje erotico",
                        "Masaje prostatico",
                        "Masaje tantrico",
                        "Masaje thai",
                        "Masajes con final feliz",
                        "Masajes desnudos",
                        "Masajes eroticos",
                        "Masajes para hombres",
                        "Masajes sensitivos",
                        "Masajes sexuales",
                        "Masturbacion rusa",
                        "Oral americana",
                        "Oral con preservativo",
                        "Oral sin preservativo",
                        "Orgias",
                        "Parejas",
                        "Trio"
                    ];

                    $atributos = [
                        "Busto grande",
                        "Busto mediano",
                        "Busto pequeño",
                        "Cara visible",
                        "Cola grande",
                        "Cola mediana",
                        "Cola pequeña",
                        "Con video",
                        "Contextura delgada",
                        "Contextura grande",
                        "Contextura mediana",
                        "Depilacion full",
                        "Depto propio",
                        "En promocion",
                        "English",
                        "Escort independiente",
                        "Español",
                        "Estatura alta",
                        "Estatura mediana",
                        "Estatura pequeña",
                        "Hentai",
                        "Morena",
                        "Mulata",
                        "No fuma",
                        "Ojos claros",
                        "Ojos oscuros",
                        "Peliroja",
                        "Portugues",
                        "Relato erotico",
                        "Rubia",
                        "Tatuajes",
                        "Trigueña"
                    ];

                    // Normalizar los arrays
                    $serviciosNormalizados = array_map(function ($servicio) {
                        return normalizer_normalize(mb_strtolower($servicio, 'UTF-8'));
                    }, $servicios);

                    $atributosNormalizados = array_map(function ($atributo) {
                        return normalizer_normalize(mb_strtolower($atributo, 'UTF-8'));
                    }, $atributos);

                    // Buscar coincidencia en servicios
                    $servicioEncontrado = null;
                    $servicioIndex = array_search($filtroNormalizado, $serviciosNormalizados);
                    if ($servicioIndex !== false) {
                        $servicioEncontrado = $servicios[$servicioIndex];
                        Log::info('Servicio encontrado: ' . $servicioEncontrado);
                    }

                    // Buscar coincidencia en atributos si no se encontró en servicios
                    $atributoEncontrado = null;
                    if ($servicioEncontrado === null) {
                        $atributoIndex = array_search($filtroNormalizado, $atributosNormalizados);
                        if ($atributoIndex !== false) {
                            $atributoEncontrado = $atributos[$atributoIndex];
                            Log::info('Atributo encontrado: ' . $atributoEncontrado);
                        }
                    }

                    // Aplicar el filtro encontrado
                    if ($servicioEncontrado) {
                        Log::info('Aplicando filtro de servicio', [
                            'servicio' => $servicioEncontrado,
                            'sqlAntes' => $query->toSql(),
                            'bindingsAntes' => $query->getBindings()
                        ]);

                        $query->where(function ($subQuery) use ($servicioEncontrado) {
                            $subQuery->where(function ($q) use ($servicioEncontrado) {
                                $q->whereRaw('JSON_CONTAINS(LOWER(servicios), ?, "$")', ['"' . mb_strtolower($servicioEncontrado, 'UTF-8') . '"'])
                                    ->orWhereRaw('JSON_CONTAINS(LOWER(servicios_adicionales), ?, "$")', ['"' . mb_strtolower($servicioEncontrado, 'UTF-8') . '"']);
                            });
                        });

                        Log::info('SQL después del filtro', [
                            'sqlDespues' => $query->toSql(),
                            'bindingsDespues' => $query->getBindings()
                        ]);
                    } elseif ($atributoEncontrado) {
                        $query->whereRaw('LOWER(atributos) LIKE ?', ['%' . mb_strtolower($atributoEncontrado, 'UTF-8') . '%']);
                    } else {
                        Log::warning('No se encontró coincidencia para el filtro: ' . $filtroNormalizado);
                    }
                }
            }

            // Procesar categoría si existe
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
                $query->where('nacionalidad', $nacionalidad);
                $variableCount++;
            }

            if ($atributos = request()->get('a')) {
                $atributosArray = explode(',', $atributos);
                if (!empty($atributosArray)) {
                    $query->where(function ($q) use ($atributosArray) {
                        $atributosLimitados = array_slice($atributosArray, 0, 3);
                        foreach ($atributosLimitados as $atributo) {
                            $q->whereRaw('LOWER(atributos) LIKE ?', ['%' . mb_strtolower($atributo) . '%']);
                        }
                    });
                }
                $variableCount++;
            }

            if ($servicios = request()->get('s')) {
                $serviciosArray = explode(',', $servicios);
                if (!empty($serviciosArray)) {
                    $query->where(function ($q) use ($serviciosArray) {
                        $serviciosLimitados = array_slice($serviciosArray, 0, 3);
                        foreach ($serviciosLimitados as $servicio) {
                            $q->where(function ($subQuery) use ($servicio) {
                                $termBusqueda = mb_strtolower($servicio);
                                $subQuery->whereRaw('LOWER(servicios) LIKE ?', ['%' . $termBusqueda . '%'])
                                    ->orWhereRaw('LOWER(servicios_adicionales) LIKE ?', ['%' . $termBusqueda . '%']);
                            });
                        }
                    });
                }
                $variableCount++;
            }

            if (request()->has('resena')) {
                $query->has('resenas');
                $variableCount++;
            }

            // Log de la consulta final
            Log::info('Consulta final', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'count' => $query->count()
            ]);

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

            // Usuario destacado - Independiente de los filtros
            // Usuario destacado - Solo filtro de ciudad
            $usuarioDestacado = UsuarioPublicate::with([
                'estados' => function ($query) {
                    $query->where('created_at', '>=', now()->subHours(24));
                },
                'location:id,usuario_publicate_id,direccion'
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
                    'estadop'
                )
                ->orderBy('updated_at', 'desc')
                ->first();

            // Usuarios online
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

// Procesar ubicaciones para usuarios principales
foreach ($usuarios as $usuario) {
    if ($ciudadSeleccionada->url === 'santiago') {
        if ($sectorValido) {
            $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
        } elseif ($usuario->location && $usuario->location->direccion) {
            $ubicacionesMostradas[$usuario->id] = $this->extraerSectorDeDireccion($usuario->location->direccion);
        } else {
            $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
        }
    } else {
        $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
    }
}

// Procesar ubicaciones para usuarios online
foreach ($usuariosOnline as $usuario) {
    if ($ciudadSeleccionada->url === 'santiago') {
        if ($sectorValido) {
            $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
        } elseif ($usuario->location && $usuario->location->direccion) {
            $ubicacionesMostradas[$usuario->id] = $this->extraerSectorDeDireccion($usuario->location->direccion);
        } else {
            $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
        }
    } else {
        $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
    }
}

// Procesar ubicaciones para primera vez
foreach ($primeraVez as $usuario) {
    if ($ciudadSeleccionada->url === 'santiago') {
        if ($sectorValido) {
            $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
        } elseif ($usuario->location && $usuario->location->direccion) {
            $ubicacionesMostradas[$usuario->id] = $this->extraerSectorDeDireccion($usuario->location->direccion);
        } else {
            $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
        }
    } else {
        $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
    }
}

// Procesar ubicaciones para los que volvieron
foreach ($volvieron as $usuario) {
    if ($ciudadSeleccionada->url === 'santiago') {
        if ($sectorValido) {
            $ubicacionesMostradas[$usuario->id] = ucwords(str_replace('-', ' ', $sectorValido));
        } elseif ($usuario->location && $usuario->location->direccion) {
            $ubicacionesMostradas[$usuario->id] = $this->extraerSectorDeDireccion($usuario->location->direccion);
        } else {
            $ubicacionesMostradas[$usuario->id] = 'Sector no disponible';
        }
    } else {
        $ubicacionesMostradas[$usuario->id] = $usuario->ubicacion;
    }
}

// Procesar ubicación para usuario destacado si existe
if ($usuarioDestacado) {
    if ($ciudadSeleccionada->url === 'santiago') {
        if ($sectorValido) {
            $ubicacionesMostradas[$usuarioDestacado->id] = ucwords(str_replace('-', ' ', $sectorValido));
        } elseif ($usuarioDestacado->location && $usuarioDestacado->location->direccion) {
            $ubicacionesMostradas[$usuarioDestacado->id] = $this->extraerSectorDeDireccion($usuarioDestacado->location->direccion);
        } else {
            $ubicacionesMostradas[$usuarioDestacado->id] = 'Sector no disponible';
        }
    } else {
        $ubicacionesMostradas[$usuarioDestacado->id] = $usuarioDestacado->ubicacion;
    }
}
            // Construcción del título
if (request()->segment(1) === 'escorts' && request()->segment(2)) {
    // Perfil individual 
    $title = ucwords(strtolower($usuario->fantasia));
    
    // Determinar si es masajista o escort
    $esMasajista = in_array('Masajes eroticos', $usuario->servicios) || 
                   in_array('Masajes sensitivos', $usuario->servicios) ||
                   in_array('Masajes con final feliz', $usuario->servicios);
    
    if ($esMasajista) {
        $title .= " Masajista";
    } else {
        $title .= " Escort";
        
        // Agregar categoría solo si es escort
        if (!empty($usuario->categorias)) {
            $categoriaDisplay = match (strtolower($usuario->categorias)) {
                'vip' => 'VIP',
                'premium' => 'Premium',
                'de_lujo' => 'de Lujo',
                'under' => 'Under',
                default => ucwords($usuario->categorias)
            };
            $title .= " " . $categoriaDisplay;
        }
    }
    
    // Agregar ubicación
    if ($usuario->ubicacion === 'Santiago') {
        $sector = $usuario->location ? 
                 $this->extraerSectorDeDireccion($usuario->location->direccion) : 
                 'Santiago';
        $title .= " en " . $sector;
    } else {
        $title .= " en " . $usuario->ubicacion;
    }
    
    $title .= " | OnlyEscorts";
 
 } else {
    // Página de listado
    $title = "Escorts ";
 
    // Procesar categoría primero
    if ($categoria = request()->get('categoria')) {
        $categoriaDisplay = match (strtolower($categoria)) {
            'vip' => 'VIP',
            'premium' => 'Premium',
            'de_lujo' => 'de Lujo',
            'under' => 'Under',
            default => ucwords(str_replace('_', ' ', $categoria))
        };
        $title .= $categoriaDisplay . " ";
    }
 
    // Agregar ciudad y sector
    $title .= "en {$ciudadSeleccionada->nombre}";
    if ($sector && $this->validarSector($sector)) {
        $title .= ", zona " . ucwords(str_replace('-', ' ', $sector));
    }
 
    // Procesar filtros específicos
    $filtrosProcesados = [];
 
    // Nacionalidad
    if ($nacionalidad = request()->get('n')) {
        $nacionalidadDisplay = match ($nacionalidad) {
            'colombia' => 'colombianas',
            'argentina' => 'argentinas',
            'brasil' => 'brasileñas',
            'chile' => 'chilenas',
            'ecuador' => 'ecuatorianas',
            'uruguay' => 'uruguayas',
            'venezuela' => 'venezolanas',
            'paraguay' => 'paraguayas',
            'peru' => 'peruanas',
            default => $nacionalidad . 's'
        };
        $filtrosProcesados[] = $nacionalidadDisplay;
    }
 
    // Edad
    if ($edad = request()->get('e')) {
        list($min, $max) = explode('-', $edad);
        $filtrosProcesados[] = "de {$min} a {$max} años";
    }
 
    // Precio
    if ($precio = request()->get('p')) {
        list($min, $max) = explode('-', $precio);
        $filtrosProcesados[] = "tarifas $" . number_format($min, 0, ',', '.') . " a $" . number_format($max, 0, ',', '.');
    }
 
    // Disponibilidad
    if (request()->has('disponible')) {
        $filtrosProcesados[] = "disponibles ahora";
    }
 
    // Atributos
    if ($atributos = request()->get('a')) {
        $atributosArray = explode(',', $atributos);
        foreach ($atributosArray as $atributo) {
            $atributoDisplay = match (strtolower($atributo)) {
                'busto grande' => 'tetona',
                'cola grande' => 'culona',
                'contextura delgada' => 'delgada',
                'contextura mediana' => 'mediana',
                'contextura grande' => 'gordita',
                'estatura alta' => 'alta',
                'estatura pequeña' => 'petite',
                default => $atributo
            };
            $filtrosProcesados[] = $atributoDisplay;
        }
    }
 
    // Servicios
    if ($servicios = request()->get('s')) {
        $serviciosArray = explode(',', $servicios);
        foreach ($serviciosArray as $servicio) {
            $servicioDisplay = str_replace(['masajes ', 'masaje '], '', strtolower($servicio));
            $filtrosProcesados[] = $servicioDisplay;
        }
    }
 
    // Reseñas verificadas
    if (request()->has('resena')) {
        $filtrosProcesados[] = "con reseñas verificadas";
    }
 
    // Agregar filtros al título
    if (!empty($filtrosProcesados)) {
        $title .= ", " . implode(', ', array_slice($filtrosProcesados, 0, 3));
        
        if (count($filtrosProcesados) > 3) {
            $title .= " y más filtros";
        }
    }
 
    // Filtros desde URL
    if ($filtros) {
        $filtroDisplay = match (true) {
            str_contains($filtros, 'edad-') => "de " . str_replace('edad-', '', $filtros) . " años",
            str_contains($filtros, 'precio-') => "tarifas " . str_replace('precio-', '$', $filtros),
            $filtros === 'disponible' => "disponibles ahora",
            $filtros === 'resena-verificada' => "con reseñas verificadas",
            default => ucwords(str_replace('-', ' ', $filtros))
        };
        
        if (!str_contains($title, $filtroDisplay)) {
            $title .= ", " . $filtroDisplay;
        }
    }
 
    // Sufijo para listados
    $title .= " | OnlyEscorts";
 }
            // Compartir con la vista
            view()->share(['pageTitle' => $title]);

            // Determinar indexación y canonical basado en las reglas
            if ($ciudadSeleccionada->url === 'santiago') {
                if ($sector && $this->validarSector($sector)) {
                    // Santiago + comuna + 1 variable → Se indexa
                    $shouldIndex = ($variableCount <= 1);

                    if ($variableCount > 1) {
                        // Si hay más de un filtro, el canonical apunta a la versión con el primer filtro
                        $canonicalUrl = url("/escorts-santiago/{$sector}/{$filtros}");
                    }
                } else {
                    // Santiago sin comuna → máximo 1 variable
                    $shouldIndex = ($variableCount <= 1);
                    if ($variableCount > 1) {
                        $canonicalUrl = url("/escorts-santiago/{$filtros}");
                    }
                }
            } else {
                // Otras ciudades → máximo 1 variable
                $shouldIndex = ($variableCount <= 1);
                if ($variableCount > 1) {
                    $canonicalUrl = url("/escorts-{$nombreCiudad}/{$filtros}");
                }
            }

            // Compartir el canonical URL y meta robots con la vista
            view()->share([
                'canonicalUrl' => $canonicalUrl,
                'metaRobots' => $shouldIndex ? 'index,follow' : 'noindex,follow'
            ]);

            // Generar SEO Text si existe el método
            try {
                if (method_exists($this, 'generateSeoText')) {
                    $seoText = $this->generateSeoText(request(), $ciudadSeleccionada, $sectorValido);
                }
            } catch (\Exception $e) {
                Log::error('Error generating SEO text', ['error' => $e->getMessage()]);
            }
            $metaTag = MetaTag::where('page', 'inicio-' . $ciudadSeleccionada->id)->first();

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
                'ubicacionesMostradas' => $ubicacionesMostradas,
                'meta' => $metaTag,
                'now' => \Carbon\Carbon::now()
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

    // Función auxiliar para normalizar strings de manera consistente
    private function normalizarString($string)
    {
        // Primero convertir a minúsculas
        $string = mb_strtolower($string);

        // Reemplazos específicos para caracteres especiales
        $reemplazos = [
            'ñ' => 'n',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u'
        ];

        return str_replace(array_keys($reemplazos), array_values($reemplazos), $string);
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
            "Anal",
            "Atención a domicilio",
            "Atención en hoteles",
            "Baile Erotico",
            "Besos",
            "Cambio de rol",
            "Departamento Propio",
            "Disfraces",
            "Ducha Erotica",
            "Eventos y Cenas",
            "Eyaculación Cuerpo",
            "Eyaculación Facial",
            "Hetero",
            "Juguetes",
            "Lesbico",
            "Lluvia dorada",
            "Masaje Erotico",
            "Masaje prostatico",
            "Masaje Tantrico",
            "Masaje Thai",
            "Masajes con final feliz",
            "Masajes desnudos",
            "Masajes Eroticos",
            "Masajes para hombres",
            "Masajes sensitivos",
            "Masajes sexuales",
            "Masturbación Rusa",
            "Oral Americana",
            "Oral con preservativo",
            "Oral sin preservativo",
            "Orgias",
            "Parejas",
            "Trio"
        ];

        $atributos = [
            "Busto Grande",
            "Busto Mediano",
            "Busto Pequeño",
            "Cara Visible",
            "Cola Grande",
            "Cola Mediana",
            "Cola Pequeña",
            "Con Video",
            "Contextura Delgada",
            "Contextura Grande",
            "Contextura Mediana",
            "Depilación Full",
            "Depto Propio",
            "En Promoción",
            "English",
            "Escort Independiente",
            "Español",
            "Estatura Alta",
            "Estatura Mediana",
            "Estatura Pequeña",
            "Hentai",
            "Morena",
            "Mulata",
            "No fuma",
            "Ojos Claros",
            "Ojos Oscuros",
            "Peliroja",
            "Portugues",
            "Relato Erotico",
            "Rubia",
            "Tatuajes",
            "Trigueña"
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
        Log::info('Clasificando filtro: ' . $filtro);
    
        $servicios = ["Anal","Atencion a domicilio","Atencion en hoteles","Baile erotico","Besos","Cambio de rol","Departamento propio","Disfraces","Ducha erotica","Eventos y cenas","Eyaculacion cuerpo","Eyaculacion facial","Hetero","Juguetes","Lesbico","Lluvia dorada","Masaje erotico","Masaje prostatico","Masaje tantrico","Masaje thai","Masajes con final feliz","Masajes desnudos","Masajes eroticos","Masajes para hombres","Masajes sensitivos","Masajes sexuales","Masturbacion rusa","Oral americana","Oral con preservativo","Oral sin preservativo","Orgias","Parejas","Trio"];
    
        $atributos = ["Busto grande","Busto mediano","Busto pequeño","Cara visible","Cola grande","Cola mediana","Cola pequeña","Con video","Contextura delgada","Contextura grande","Contextura mediana","Depilacion full","Depto propio","En promocion","English","Escort independiente","Español","Estatura alta","Estatura mediana","Estatura pequeña","Hentai","Morena","Mulata","No fuma","Ojos claros","Ojos oscuros","Peliroja","Portugues","Relato erotico","Rubia","Tatuajes","Trigueña"];
    
        // Normalizar el filtro reemplazando TODOS los guiones
        $filtroNormalizado = strtolower(preg_replace('/-+/', ' ', $filtro));
        $serviciosNormalizados = array_map('strtolower', $servicios);
        $atributosNormalizados = array_map('strtolower', $atributos);
    
        Log::info('Filtro normalizado: ' . $filtroNormalizado);
        
        $index = array_search($filtroNormalizado, $serviciosNormalizados);
        if ($index !== false) {
            Log::info('Servicio encontrado: ' . $servicios[$index]);
            return ['tipo' => 'servicio', 'valor' => $servicios[$index]];
        }
    
        $index = array_search($filtroNormalizado, $atributosNormalizados);
        if ($index !== false) {
            Log::info('Atributo encontrado: ' . $atributos[$index]);
            return ['tipo' => 'atributo', 'valor' => $atributos[$index]];
        }
    
        Log::info('No se encontró coincidencia');
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

    public function showPerfil($nombre)
    {
        try {
            $id = substr($nombre, strrpos($nombre, '-') + 1);
            
            $usuarioPublicate = UsuarioPublicate::with([
                'disponibilidad',
                'estados' => function ($query) {
                    $query->where('created_at', '>=', now()->subHours(24));
                }
            ])->findOrFail($id);
    
            // Agregamos la consulta de ciudades
            $ciudades = Ciudad::all(); // Asegúrate de tener el modelo importado: use App\Models\Ciudad;
    
            return view('showescort', compact('usuarioPublicate', 'ciudades'));
            
        } catch (\Exception $e) {
            \Log::error('Error en showPerfil: ' . $e->getMessage());
            return abort(404);
        }
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
    
        $nacionalidades = [
            'argentina' => 'argentinas',
            'brasil' => 'brasileñas',
            'chile' => 'chilenas',
            'colombia' => 'colombianas',
            'ecuador' => 'ecuatorianas',
            'uruguay' => 'uruguayas',
            'venezuela' => 'venezolanas',
            'paraguay' => 'paraguayas',
            'peru' => 'peruanas',
            'argentinas' => 'argentinas',
            'brasilenas' => 'brasileñas',
            'chilenas' => 'chilenas',
            'colombianas' => 'colombianas',
            'ecuatorianas' => 'ecuatorianas',
            'uruguayas' => 'uruguayas',
            'venezolanas' => 'venezolanas',
            'paraguayas' => 'paraguayas',
            'peruanas' => 'peruanas'
        ];
    
        $path = $request->path();
        $parts = explode('/', $path);
        $lastPart = strtolower(end($parts));
    
        $clasificacionUrl = null;
        $singleFilter = null;
        $isNationalityFilter = false;
        $nacionalidadFromUrl = null;
    
        // Detectar servicios en la URL
        if (str_contains($lastPart, 'anal')) {
            $singleFilter = 'servicios';
            if ($request->has('s')) {
                $servicios = explode(',', $request->get('s'));
                array_unshift($servicios, 'Anal');
                $request->merge(['s' => implode(',', array_unique($servicios))]);
            } else {
                $request->merge(['s' => 'Anal']);
            }
        }
        elseif (str_starts_with($lastPart, 'escorts-')) {
            $posibleNacionalidad = str_replace('escorts-', '', $lastPart);
            if (isset($nacionalidades[$posibleNacionalidad])) {
                $isNationalityFilter = true;
                $nacionalidadFromUrl = $posibleNacionalidad;
                $request->merge(['n' => $nacionalidadFromUrl]);
                $singleFilter = 'nacionalidad';
                $variableCount++;
            }
        }
        elseif (preg_match('/^edad-(\d+)-(\d+)$/', $lastPart, $matches)) {
            $request->merge(['e' => "{$matches[1]}-{$matches[2]}"]);
            $singleFilter = 'edad';
            $request->merge(['singleFilter' => 'edad']);
        }
        elseif (preg_match('/^precio-(\d+)-(\d+)$/', $lastPart, $matches)) {
            $request->merge(['p' => "{$matches[1]}-{$matches[2]}"]);
            $singleFilter = 'precio';
        }
        elseif ($lastPart === 'disponible') {
            $request->merge(['disponible' => '1']);
            $singleFilter = 'disponible';
        }
        elseif ($lastPart === 'resena-verificada') {
            $request->merge(['resena' => '1']);
            $singleFilter = 'resena';
        }
        elseif (in_array($lastPart, ['vip', 'premium', 'de_lujo', 'under'])) {
            $request->merge(['categoria' => $lastPart]);
            $singleFilter = 'categoria';
        }
    
        if (!$isNationalityFilter && !$singleFilter) {
            $filtroNormalizado = str_replace('-', ' ', $lastPart);
            $nacionalidadMap = [
                'colombiana' => 'colombia',
                'argentina' => 'argentina',
                'brasilena' => 'brasil',
                'chilena' => 'chile',
                'ecuatoriana' => 'ecuador',
                'uruguaya' => 'uruguay',
                'venezolana' => 'venezuela',
                'paraguaya' => 'paraguay',
                'peruana' => 'peru'
            ];
    
            if (array_key_exists($filtroNormalizado, $nacionalidadMap)) {
                $isNationalityFilter = true;
                $nacionalidadFromUrl = $nacionalidadMap[$filtroNormalizado];
                $request->merge(['n' => $nacionalidadFromUrl]);
                $singleFilter = 'nacionalidad';
            } else {
                $clasificacionUrl = $this->clasificarFiltro($lastPart);
                if ($clasificacionUrl) {
                    if ($clasificacionUrl['tipo'] === 'servicio') {
                        if ($request->has('s')) {
                            $servicios = explode(',', $request->get('s'));
                            $servicios[] = $clasificacionUrl['valor'];
                            $request->merge(['s' => implode(',', array_unique($servicios))]);
                        } else {
                            $request->merge(['s' => $clasificacionUrl['valor']]);
                        }
                        $singleFilter = 'servicios';
                    } elseif ($clasificacionUrl['tipo'] === 'atributo') {
                        if ($request->has('a')) {
                            $atributos = explode(',', $request->get('a'));
                            $atributos[] = $clasificacionUrl['valor'];
                            $request->merge(['a' => implode(',', array_unique($atributos))]);
                        } else {
                            $request->merge(['a' => $clasificacionUrl['valor']]);
                        }
                        $singleFilter = 'atributos';
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
            'resena' => false
        ];
    
        if ($request->has('n') || $isNationalityFilter) {
            $nacionalidad = $request->get('n') ?? $nacionalidadFromUrl;
            $activeFilters['nacionalidad'] = $nacionalidades[$nacionalidad] ?? null;
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
            $activeFilters['atributos'] = array_map(function($atributo) {
                return ucwords(str_replace('-', ' ', $atributo));
            }, $atributos);
        }
    
        if ($request->has('s')) {
            $servicios = explode(',', $request->get('s'));
            $activeFilters['servicios'] = array_map(function($servicio) {
                return ucwords(str_replace('-', ' ', $servicio));
            }, $servicios);
        }
    
        if ($request->has('categoria')) {
            $categoria = $request->get('categoria');
            $activeFilters['categoria'] = ucwords(str_replace('_', ' ', $categoria));
        }
    
        $activeFilters['disponible'] = $request->has('disponible');
        $activeFilters['resena'] = $request->has('resena');
    
        if ($clasificacionUrl) {
            if ($clasificacionUrl['tipo'] === 'servicio') {
                $activeFilters['servicios'][] = ucwords($clasificacionUrl['valor']);
            } elseif ($clasificacionUrl['tipo'] === 'atributo') {
                $activeFilters['atributos'][] = ucwords($clasificacionUrl['valor']);
            }
        }
    
        $totalActiveFilters = 0;
        if (!empty($activeFilters['nacionalidad'])) $totalActiveFilters++;
        if (!empty($activeFilters['edad'])) $totalActiveFilters++;
        if (!empty($activeFilters['precio'])) $totalActiveFilters++;
        if (!empty($activeFilters['atributos'])) $totalActiveFilters++;
        if (!empty($activeFilters['servicios'])) $totalActiveFilters++;
        if (!empty($activeFilters['categoria'])) $totalActiveFilters++;
        if ($activeFilters['disponible'] === true) $totalActiveFilters++;
        if ($activeFilters['resena'] === true) $totalActiveFilters++;
    
        if ($totalActiveFilters === 0) {
            $templateType = 'filtro';
            $filtroType = 'ciudad';
        }
        elseif ($totalActiveFilters === 1) {
            $templateType = 'filtro';
            $filtroType = match($singleFilter) {
                'nacionalidad' => 'nacionalidad',
                'edad' => 'edad', 
                'precio' => 'precio',
                'disponible' => 'disponible',
                'resena' => 'resena',
                'categoria' => 'categorias',
                'servicios' => 'servicios',
                'atributos' => 'atributos',
                default => $singleFilter
            };
        }
        elseif ($totalActiveFilters >= 2 && $totalActiveFilters <= 4) {
            $templateType = 'multiple';
            $filtroType = null;
        }
        else {
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
    
        if ($filtroType !== null) {
            $templateQuery->where('filtro', $filtroType);
        } else {
            $templateQuery->whereNull('filtro');
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
    
        $title = $this->generateSeoTitle($ciudadSeleccionada, $sectorSeleccionado, $activeFilters, $singleFilter, $clasificacionUrl);
    
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
    

    private function generateSeoTitle($ciudad, $sector, $filters, $singleFilter = null, $clasificacionUrl = null)
    {
        $title = "Escorts";
    
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
                        if (count($filters['servicios']) > 1) {
                            $title = "Escorts con servicios de " . implode(' y ', $filters['servicios']);
                        } else {
                            $title = "Escorts con servicio de " . reset($filters['servicios']);
                        }
                    }
                    break;
                case 'atributos':
                    if ($clasificacionUrl && $clasificacionUrl['tipo'] === 'atributo') {
                        $title = "Escorts " . ucwords($clasificacionUrl['valor']);
                    } elseif (!empty($filters['atributos'])) {
                        if (count($filters['atributos']) > 1) {
                            $title = "Escorts con " . implode(' y ', $filters['atributos']);
                        } else {
                            $title = "Escorts " . reset($filters['atributos']);
                        }
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
                $title .= " " . $filters['nacionalidad'];
            }
            if (!empty($filters['categoria'])) {
                $title .= " " . $filters['categoria'];
            }
            if (!empty($filters['servicios'])) {
                if (count($filters['servicios']) > 1) {
                    $title .= " con servicios de " . implode(' y ', $filters['servicios']);
                } else {
                    $title .= " con servicio de " . reset($filters['servicios']);
                }
            }
            if (!empty($filters['atributos'])) {
                if (count($filters['atributos']) > 1) {
                    $title .= " con " . implode(' y ', $filters['atributos']);
                } else {
                    $title .= " " . reset($filters['atributos']);
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
            $title .= " - " . ucwords($sector);
        }
    
        return $title;
    }


}