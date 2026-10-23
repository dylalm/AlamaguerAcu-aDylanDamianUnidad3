<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HerramientaController extends Controller
{
    /** Catálogo de herramientas con búsqueda, filtros y orden por cercanía. */
    public function index(Request $request): Response
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
            ->get()
            ->map(fn (Herramienta $herramienta) => [
                'id' => $herramienta->id,
                'nombre' => $herramienta->nombre,
                'descripcion' => $herramienta->descripcion,
                'categoria' => $herramienta->categoria,
                'categoria_etiqueta' => Herramienta::CATEGORIAS[$herramienta->categoria] ?? 'Otros',
                'disponible' => $herramienta->disponible,
                'propietario' => $herramienta->propietario->name,
                'barrio' => $herramienta->barrio,
                'distancia_km' => $herramienta->distanciaKm($centro['latitud'], $centro['longitud']),
            ])
            ->sortBy('distancia_km')
            ->values()
            ->all();

        return Inertia::render('Herramientas/Index', [
            'herramientas' => $herramientas,
            'categorias' => collect(Herramienta::CATEGORIAS)
                ->map(fn (string $etiqueta, string $valor) => ['valor' => $valor, 'etiqueta' => $etiqueta])
                ->values()
                ->all(),
            'filtros' => [
                'buscar' => $filtros['buscar'] ?? '',
                'categoria' => $filtros['categoria'] ?? '',
                'disponibles' => $request->boolean('disponibles'),
            ],
        ]);
    }
}
