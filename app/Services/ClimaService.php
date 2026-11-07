<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** Consume el servicio web de terceros Open-Meteo para conocer el clima actual. */
class ClimaService
{
    private const URL = 'https://api.open-meteo.com/v1/forecast';

    /** Devuelve el clima actual en un punto o null si el servicio no responde. */
    public function actual(float $latitud, float $longitud): ?array
    {
        $clave = sprintf('clima:%.2f:%.2f', $latitud, $longitud);

        // Se guarda 15 minutos en caché para no abusar del servicio externo
        return Cache::remember($clave, now()->addMinutes(15), fn () => $this->consultar($latitud, $longitud));
    }

    private function consultar(float $latitud, float $longitud): ?array
    {
        try {
            $respuesta = Http::timeout(5)->acceptJson()->get(self::URL, [
                'latitude' => $latitud,
                'longitude' => $longitud,
                'current' => 'temperature_2m,precipitation,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ]);
        } catch (ConnectionException) {
            return null;
        }

        $actual = $respuesta->successful() ? $respuesta->json('current') : null;

        if (! is_array($actual)) {
            return null;
        }

        $codigo = (int) ($actual['weather_code'] ?? 0);
        $lluvia = (float) ($actual['precipitation'] ?? 0);
        $viento = (float) ($actual['wind_speed_10m'] ?? 0);
        $malTiempo = $lluvia > 0 || $codigo >= 51 || $viento >= 40;

        return [
            'temperatura' => round((float) ($actual['temperature_2m'] ?? 0), 1),
            'precipitacion_mm' => round($lluvia, 1),
            'viento_kmh' => round($viento, 1),
            'descripcion' => $this->describir($codigo),
            'recomendacion' => $malTiempo
                ? 'Hay lluvia o viento fuerte: conviene dejar para otro día el uso de herramientas eléctricas al aire libre.'
                : 'Buen momento para usar herramientas de jardín y de exterior.',
            'fuente' => 'Open-Meteo (open-meteo.com)',
        ];
    }

    /** Traduce el código meteorológico WMO a una descripción en español. */
    private function describir(int $codigo): string
    {
        return match (true) {
            $codigo === 0 => 'Despejado',
            $codigo <= 2 => 'Parcialmente nublado',
            $codigo === 3 => 'Nublado',
            in_array($codigo, [45, 48], true) => 'Niebla',
            $codigo >= 51 && $codigo <= 57 => 'Llovizna',
            $codigo >= 61 && $codigo <= 67 => 'Lluvia',
            $codigo >= 80 && $codigo <= 82 => 'Chubascos',
            ($codigo >= 71 && $codigo <= 77) || $codigo === 85 || $codigo === 86 => 'Nieve',
            $codigo >= 95 => 'Tormenta',
            default => 'Sin datos',
        };
    }
}
