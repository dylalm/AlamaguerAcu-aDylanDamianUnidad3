<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** Consume el servicio web de terceros Nominatim (OpenStreetMap) para convertir una zona en coordenadas. */
class GeocodificacionService
{
    private const URL = 'https://nominatim.openstreetmap.org/search';

    /** Devuelve latitud, longitud y nombre del lugar, o null si no se encuentra. */
    public function buscar(string $consulta): ?array
    {
        $clave = 'geocodificacion:'.md5(mb_strtolower(trim($consulta)));

        // Las búsquedas se guardan un día: la política de Nominatim pide no repetir consultas
        return Cache::remember($clave, now()->addDay(), fn () => $this->consultar($consulta));
    }

    private function consultar(string $consulta): ?array
    {
        try {
            $respuesta = Http::timeout(5)
                ->withUserAgent(config('app.name', 'ManoVecina').'/1.0 (proyecto academico)')
                ->acceptJson()
                ->get(self::URL, [
                    'q' => $consulta,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'countrycodes' => config('manovecina.pais', 'mx'),
                ]);
        } catch (ConnectionException) {
            return null;
        }

        $lista = $respuesta->successful() ? $respuesta->json() : null;
        $resultado = is_array($lista) ? ($lista[0] ?? null) : null;

        if (! is_array($resultado) || ! isset($resultado['lat'], $resultado['lon'])) {
            return null;
        }

        return [
            'latitud' => (float) $resultado['lat'],
            'longitud' => (float) $resultado['lon'],
            'nombre' => $resultado['display_name'] ?? $consulta,
        ];
    }
}
