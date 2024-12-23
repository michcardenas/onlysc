<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ciudad;
use App\Models\UsuarioPublicate;
use App\Models\BlogArticle;
use App\Models\Posts;
use App\Models\Foro;
use App\Models\Estado;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InicioController extends Controller
{
public function show($nombreCiudad)
{
    Log::debug('Iniciando show', [
        'nombreCiudad' => $nombreCiudad,
        'request_segments' => request()->segments(),
        'request_all' => request()->all()
    ]);

    $ciudadSeleccionada = Ciudad::where('url', $nombreCiudad)->firstOrFail();
    Log::debug('Ciudad seleccionada', [
        'ciudad' => $ciudadSeleccionada->nombre,
        'url' => $ciudadSeleccionada->url
    ]);

    $ciudades = Ciudad::all();
    $now = Carbon::now();
    $currentDay = strtolower($now->locale('es')->dayName);
    $currentTime = $now->format('H:i:s');

    if (strpos($nombreCiudad, 'escorts-') !== false) {
        $parts = explode('-', $nombreCiudad);
        $nombreCiudad = end($parts);
        Log::debug('Redirigiendo escorts-', [
            'original' => $nombreCiudad,
            'nuevo' => end($parts)
        ]);
        return redirect("/escorts-{$nombreCiudad}");
    }

    // Base query para usuarios principal
    $query = UsuarioPublicate::query()
        ->whereIn('estadop', [1, 3])
        ->where('ubicacion', $ciudadSeleccionada->nombre);

    Log::debug('Query inicial', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    // Base query para usuarios principal
    $query = UsuarioPublicate::query()
        ->whereIn('estadop', [1, 3])
        ->where('ubicacion', $ciudadSeleccionada->nombre);

    // Filtros desde URL solo para query principal
    $segments = request()->segments();
    $filterIndex = array_search($nombreCiudad, $segments) + 1;
    
    if ($filterIndex < count($segments)) {
        while ($filterIndex < count($segments)) {
            switch ($segments[$filterIndex]) {
                case 'edad':
                    list($min, $max) = explode('-', $segments[$filterIndex + 1]);
                    $query->whereBetween('edad', [(int)$min, (int)$max]);
                    Log::debug('Filtro edad aplicado', ['min' => $min, 'max' => $max]);
                    $filterIndex += 2;
                    break;
                    
                case 'precio':
                    list($min, $max) = explode('-', $segments[$filterIndex + 1]);
                    $query->whereBetween('precio', [(int)$min, (int)$max]);
                    Log::debug('Filtro precio aplicado', ['min' => $min, 'max' => $max]);
                    $filterIndex += 2;
                    break;
                    
                case 'atributos':
                    $atributos = explode(',', $segments[$filterIndex + 1]);
                    if (!empty($atributos)) {
                        $query->where(function($q) use ($atributos) {
                            // Tomar los primeros 3 atributos para la búsqueda
                            $atributosLimitados = array_slice($atributos, 0, 3);
                            foreach ($atributosLimitados as $key => $atributo) {
                                if ($key === 0) {
                                    $q->where('atributos', 'like', '%' . $atributo . '%');
                                } else {
                                    $q->orWhere('atributos', 'like', '%' . $atributo . '%');
                                }
                            }
                        });
                    }
                    Log::debug('Filtro atributos aplicado', [
                        'atributos_seleccionados' => $atributos,
                        'atributos_aplicados' => array_slice($atributos, 0, 3)
                    ]);
                    $filterIndex += 2;
                    break;
                    
                case 'servicios':
                    $servicios = explode(',', $segments[$filterIndex + 1]);
                    if (!empty($servicios)) {
                        $query->where(function($q) use ($servicios) {
                            // Tomar los primeros 3 servicios para la búsqueda
                            $serviciosLimitados = array_slice($servicios, 0, 3);
                            foreach ($serviciosLimitados as $key => $servicio) {
                                if ($key === 0) {
                                    $q->where('servicios', 'like', '%' . $servicio . '%');
                                } else {
                                    $q->orWhere('servicios', 'like', '%' . $servicio . '%');
                                }
                            }
                        });
                    }
                    Log::debug('Filtro servicios aplicado', [
                        'servicios_seleccionados' => $servicios,
                        'servicios_aplicados' => array_slice($servicios, 0, 3)
                    ]);
                    $filterIndex += 2;
                    break;
                    
                default:
                    $filterIndex++;
            }
        }
    }

    // Debug de la query final
    Log::debug('Query final', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    // Consulta principal con filtros
    Log::debug('Ejecutando query principal');
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

    Log::debug('Resultados de usuarios', [
        'total' => $usuarios->total(),
        'por_pagina' => $usuarios->perPage(),
        'pagina_actual' => $usuarios->currentPage()
    ]);

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

    // Resto de consultas sin filtros
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

    // Resto de consultas existentes
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

    return view('inicio', [
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
    ]);
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
        $filterUrl = "/{$nombreCiudad}";
        
        // Construir URL con filtros
        if ($request->has(['edadMin', 'edadMax'])) {
            $filterUrl .= "/edad/{$request->edadMin}-{$request->edadMax}";
        }
        
        if ($request->has(['precioMin', 'precioMax'])) {
            $filterUrl .= "/precio/{$request->precioMin}-{$request->precioMax}";
        }
        
        if ($request->has('atributos') && !empty($request->atributos)) {
            $filterUrl .= "/atributos/" . implode(',', $request->atributos);
        }
        
        if ($request->has('servicios') && !empty($request->servicios)) {
            $filterUrl .= "/servicios/" . implode(',', $request->servicios);
        }
    
        if ($request->ajax()) {
            return response()->json([
                'redirect' => $filterUrl
            ]);
        }
    
        return redirect($filterUrl);
    }
}
