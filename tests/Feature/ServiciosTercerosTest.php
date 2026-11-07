<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    // Cada prueba parte sin caché y ninguna puede llamar a internet de verdad
    Cache::flush();
    Http::preventStrayRequests();
});

it('consulta el clima en Open-Meteo y devuelve un resumen', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => ['temperature_2m' => 24.5, 'precipitation' => 0.0, 'weather_code' => 0, 'wind_speed_10m' => 8.2],
        ]),
    ]);

    $this->getJson('/api/v1/clima')
        ->assertOk()
        ->assertJsonPath('temperatura', 24.5)
        ->assertJsonPath('descripcion', 'Despejado')
        ->assertJsonStructure(['temperatura', 'precipitacion_mm', 'viento_kmh', 'descripcion', 'recomendacion', 'fuente']);

    Http::assertSentCount(1);
});

it('recomienda no usar herramientas eléctricas cuando llueve', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => ['temperature_2m' => 18.0, 'precipitation' => 2.3, 'weather_code' => 61, 'wind_speed_10m' => 12.0],
        ]),
    ]);

    $this->getJson('/api/v1/clima')
        ->assertOk()
        ->assertJsonPath('descripcion', 'Lluvia')
        ->assertJsonPath('precipitacion_mm', 2.3);
});

it('responde 503 cuando el servicio de clima falla', function () {
    Http::fake(['api.open-meteo.com/*' => Http::response([], 500)]);

    $this->getJson('/api/v1/clima')->assertStatus(503);
});

it('valida las coordenadas del clima', function () {
    $this->getJson('/api/v1/clima?lat=999&lng=0')->assertStatus(422);
});

it('obtiene las coordenadas con Nominatim al publicar sin latitud ni longitud', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '19.4326', 'lon' => '-99.1332', 'display_name' => 'Centro, México'],
        ]),
    ]);
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/herramientas', [
        'nombre' => 'Sierra circular',
        'descripcion' => 'Con disco nuevo',
        'categoria' => 'construccion',
        'barrio' => 'Colonia Centro',
    ])
        ->assertCreated()
        ->assertJsonPath('data.latitud', 19.4326);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'nominatim.openstreetmap.org')
        && str_contains(urldecode($request->url()), 'q=Colonia Centro'));
});

it('responde 422 si Nominatim no encuentra la zona', function () {
    Http::fake(['nominatim.openstreetmap.org/*' => Http::response([])]);
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/herramientas', [
        'nombre' => 'Sierra circular',
        'descripcion' => 'Con disco nuevo',
        'categoria' => 'construccion',
        'barrio' => 'Lugar inexistente',
    ])->assertStatus(422);
});
