<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClimaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClimaController extends Controller
{
    /** Clima actual (servicio de terceros Open-Meteo) para el centro de referencia o unas coordenadas dadas. */
    public function __invoke(Request $request, ClimaService $clima): JsonResponse
    {
        $datos = $request->validate([
            'lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:lng'],
            'lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:lat'],
        ]);

        $centro = config('manovecina.centro');

        $resultado = $clima->actual(
            (float) ($datos['lat'] ?? $centro['latitud']),
            (float) ($datos['lng'] ?? $centro['longitud']),
        );

        if ($resultado === null) {
            return response()->json(['mensaje' => 'El servicio de clima no está disponible por ahora.'], 503);
        }

        return response()->json($resultado);
    }
}
