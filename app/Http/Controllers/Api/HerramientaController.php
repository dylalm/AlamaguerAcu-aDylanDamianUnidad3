<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HerramientaResource;
use App\Models\Herramienta;
use App\Services\GeocodificacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class HerramientaController extends Controller
{
    /** Lista herramientas con filtros, ordenadas de la más cercana a la más lejana. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:60'],
            'categoria' => ['nullable', 'in:'.implode(',', array_keys(Herramienta::CATEGORIAS))],
            'disponibles' => ['nullable', 'boolean'],
        ]);

        $centro = config('manovecina.centro');

        $herramientas = Herramienta::query()
            ->with('propietario:id,name')
            ->buscar($filtros['buscar'] ?? null)
            ->when($filtros['categoria'] ?? null, fn ($consulta, $categoria) => $consulta->where('categoria', $categoria))
            ->when($request->boolean('disponibles'), fn ($consulta) => $consulta->where('disponible', true))
            ->limit(100)
            ->get();

        $herramientas->each(function (Herramienta $herramienta) use ($centro): void {
            $herramienta->distancia_km = $herramienta->distanciaKm($centro['latitud'], $centro['longitud']);
        });

        return HerramientaResource::collection($herramientas->sortBy('distancia_km')->values());
    }

    /** Muestra una herramienta. */
    public function show(Herramienta $herramienta): HerramientaResource
    {
        return new HerramientaResource($herramienta->load('propietario'));
    }

    /** Publica una herramienta a nombre del usuario del token. */
    public function store(Request $request, GeocodificacionService $geocodificador): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'descripcion' => ['required', 'string', 'max:500'],
            'categoria' => ['required', 'in:'.implode(',', array_keys(Herramienta::CATEGORIAS))],
            'barrio' => ['required', 'string', 'max:60'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitud'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitud'],
        ]);

        // Si el cliente no manda coordenadas, se obtienen del servicio de terceros Nominatim (OpenStreetMap)
        if (! isset($datos['latitud'])) {
            $punto = $geocodificador->buscar($datos['barrio']);

            if ($punto === null) {
                return response()->json([
                    'mensaje' => 'No se pudo ubicar la zona indicada. Envía latitud y longitud o prueba con otro nombre.',
                ], 422);
            }

            $datos['latitud'] = $punto['latitud'];
            $datos['longitud'] = $punto['longitud'];
        }

        // El dueño siempre es el usuario del token: un user_id enviado por el cliente se ignora
        $herramienta = Herramienta::create([
            ...$datos,
            'user_id' => $request->user()->id,
            'disponible' => true,
        ]);

        return (new HerramientaResource($herramienta->load('propietario')))
            ->response()
            ->setStatusCode(201);
    }

    /** Cambia la disponibilidad de una herramienta propia. */
    public function disponibilidad(Request $request, Herramienta $herramienta): HerramientaResource
    {
        Gate::authorize('update', $herramienta);

        $datos = $request->validate(['disponible' => ['nullable', 'boolean']]);

        $herramienta->update(['disponible' => $datos['disponible'] ?? ! $herramienta->disponible]);

        return new HerramientaResource($herramienta->load('propietario'));
    }
}
