<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /** Panel personal: estadísticas y herramientas del vecino que inició sesión. */
    public function index(Request $request): Response
    {
        $herramientas = Herramienta::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Dashboard', [
            'estadisticas' => [
                'total' => $herramientas->count(),
                'disponibles' => $herramientas->where('disponible', true)->count(),
                'prestadas' => $herramientas->where('disponible', false)->count(),
            ],
            'herramientas' => $herramientas
                ->map(fn (Herramienta $herramienta) => [
                    'id' => $herramienta->id,
                    'nombre' => $herramienta->nombre,
                    'categoria' => $herramienta->categoria,
                    'categoria_etiqueta' => Herramienta::CATEGORIAS[$herramienta->categoria] ?? 'Otros',
                    'barrio' => $herramienta->barrio,
                    'disponible' => $herramienta->disponible,
                ])
                ->values()
                ->all(),
        ]);
    }
}
