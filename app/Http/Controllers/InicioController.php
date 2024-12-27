<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use App\Models\BlogArticle;
use App\Models\Posts;
use App\Models\Foro;
use App\Models\SeoTemplate;
use App\Models\Estado;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InicioController extends Controller
{
    public function show($nombreCiudad)
    {
        $ciudadSeleccionada = Ciudad::where('url', $nombreCiudad)->firstOrFail();
        session(['ciudad_actual' => $ciudadSeleccionada->nombre]);
        $ciudades = Ciudad::all();
        $now = Carbon::now();
        $currentDay = strtolower($now->locale('es')->dayName);
        $currentTime = $now->format('H:i:s');

        // Verificar si hay un filtro simple en la URL
        $partes = explode('/', request()->path());
        $filtroSimple = count($partes) > 2 ? end($partes) : null;

        // Base query para usuarios principal
        $query = UsuarioPublicate::query()
            ->whereIn('estadop', [1, 3])
            ->where('ubicacion', $ciudadSeleccionada->nombre);

        // Si hay un filtro simple, aplicarlo primero
        if ($filtroSimple && !request()->has(['e', 'p', 'a', 's'])) {
            $filtroNormalizado = str_replace('-', ' ', $filtroSimple);
            $query->where(function ($q) use ($filtroNormalizado) {
                $q->where('atributos', 'like', '%' . $filtroNormalizado . '%')
                    ->orWhere('servicios', 'like', '%' . $filtroNormalizado . '%');
            });
        } else {
            // Procesar filtros desde query parameters
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
        }

        // Consulta principal con filtros
        $usuarios = $query->with(['disponibilidad', 'estados' => function ($query) {
            $query->where('created_at', '>=', now()->subHours(24));
        }])
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
        // Estados
        $estados = Estado::select('estados.*', 'u.foto as user_foto')  // Cambiado de 'users.foto' a 'u.foto'
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

            $seoText = $this->generateSeoText(request(), $ciudadSeleccionada);

            return view('inicio', array_merge([
                'ciudades' => $ciudades,
                'ciudadSeleccionada' => $ciudadSeleccionada,
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
                'experiencias' => $experiencias
            ], $seoText ? [
                'seoTitle' => $seoText['title'],
                'seoDescription' => $seoText['description']
            ] : []));
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

    private function generateSeoText($request, $ciudadSeleccionada)
{
    // Contar filtros activos
    $activeFilters = 0;
    if ($request->has('n')) $activeFilters++;
    if ($request->has('e')) $activeFilters++;
    if ($request->has('p')) $activeFilters++;
    if ($request->has('a')) {
        $atributos = explode(',', $request->get('a'));
        $activeFilters += count($atributos);
    }
    if ($request->has('s')) {
        $servicios = explode(',', $request->get('s'));
        $activeFilters += count($servicios);
    }

    // Si no hay filtros, retornar null
    if ($activeFilters === 0) {
        return null;
    }

    // Determinar qué tipo de template usar
    $templateType = 'single';
    if ($activeFilters > 4) {
        $templateType = 'complex';
    } elseif ($activeFilters > 1) {
        $templateType = 'multiple';
    }

    // Obtener el template correspondiente
    $template = SeoTemplate::where('tipo', $templateType)->first();
    if (!$template) {
        // Templates por defecto si no existen en la base de datos
        $defaultTemplates = [
            'single' => 'Encuentra escorts {nacionalidad} en {ciudad}. Explora nuestro catálogo de escorts seleccionadas.',
            'multiple' => 'Encuentra escorts {nacionalidad} de {edad_min} a {edad_max} años con precios desde ${precio_min} hasta ${precio_max} en {ciudad}.',
            'complex' => 'Descubre escorts {nacionalidad} en {ciudad} que cumplen con tus preferencias específicas. Contamos con una amplia selección de servicios y características como {atributos} y servicios de {servicios}.'
        ];
        
        $template = new SeoTemplate([
            'tipo' => $templateType,
            'description_template' => $defaultTemplates[$templateType]
        ]);
    }

    // Preparar las variables de reemplazo
    $replacements = [
        '{ciudad}' => $ciudadSeleccionada->nombre,
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
        $nacionalidades = [
            'argentine' => 'argentinas',
            'brazilian' => 'brasileñas',
            'chilean' => 'chilenas',
            'colombian' => 'colombianas',
            'ecuadorian' => 'ecuatorianas',
            'uruguayan' => 'uruguayas'
        ];
        $replacements['{nacionalidad}'] = isset($nacionalidades[$nacionalidad]) ? $nacionalidades[$nacionalidad] : '';
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

    // Procesar atributos
    if ($atributos = $request->get('a')) {
        $atributosArray = explode(',', $atributos);
        if (!empty($atributosArray)) {
            $replacements['{atributos}'] = strtolower(implode(', ', array_slice($atributosArray, 0, 3)));
        }
    }

    // Procesar servicios
    if ($servicios = $request->get('s')) {
        $serviciosArray = explode(',', $servicios);
        if (!empty($serviciosArray)) {
            $replacements['{servicios}'] = strtolower(implode(', ', array_slice($serviciosArray, 0, 3)));
        }
    }

    // Generar título
    $title = "Escorts en " . $ciudadSeleccionada->nombre;
    if ($nacionalidad = $request->get('n')) {
        $nacionalidades = [
            'argentine' => 'argentinas',
            'brazilian' => 'brasileñas',
            'chilean' => 'chilenas',
            'colombian' => 'colombianas',
            'ecuadorian' => 'ecuatorianas',
            'uruguayan' => 'uruguayas'
        ];
        if (isset($nacionalidades[$nacionalidad])) {
            $title = "Escorts " . $nacionalidades[$nacionalidad] . " en " . $ciudadSeleccionada->nombre;
        }
    }

    // Aplicar reemplazos a la descripción
    $description = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $template->description_template
    );

    // Limpiar el texto en caso de que haya variables no reemplazadas
    $description = preg_replace('/\{[^}]+\}/', '', $description);

    return [
        'title' => $title,
        'description' => $description
    ];
}
}
