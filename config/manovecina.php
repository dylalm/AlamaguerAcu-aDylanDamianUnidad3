<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Centro de referencia
    |--------------------------------------------------------------------------
    |
    | Punto desde el cual se calcula la distancia a cada herramienta. Por ahora
    | es un valor fijo de ejemplo; más adelante saldrá de la ubicación
    | aproximada del vecino que está navegando.
    |
    */

    'centro' => [
        'latitud' => (float) env('MANOVECINA_CENTRO_LAT', 19.4326),
        'longitud' => (float) env('MANOVECINA_CENTRO_LNG', -99.1332),
    ],

];
