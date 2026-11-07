<?php

use App\Models\Herramienta;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function datosHerramienta(array $cambios = []): array
{
    return array_merge([
        'nombre' => 'Sierra circular',
        'descripcion' => 'Con disco nuevo y guía paralela.',
        'categoria' => 'construccion',
        'barrio' => 'Col. Centro',
        'latitud' => 19.4326,
        'longitud' => -99.1332,
    ], $cambios);
}

it('permite iniciar sesión y entrega un token', function () {
    $usuario = User::factory()->create();

    $this->postJson('/api/v1/login', ['email' => $usuario->email, 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['token', 'tipo', 'usuario' => ['id', 'nombre']]);
});

it('rechaza credenciales incorrectas', function () {
    $usuario = User::factory()->create();

    $this->postJson('/api/v1/login', ['email' => $usuario->email, 'password' => 'incorrecta'])
        ->assertUnauthorized();
});

it('limita los intentos de inicio de sesión por minuto', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/login', ['email' => 'nadie@ejemplo.com', 'password' => 'mal']);
    }

    $this->postJson('/api/v1/login', ['email' => 'nadie@ejemplo.com', 'password' => 'mal'])
        ->assertStatus(429);
});

it('lista las herramientas sin necesidad de token', function () {
    Herramienta::factory()->count(3)->create();

    $this->getJson('/api/v1/herramientas')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('filtra las herramientas por categoría', function () {
    Herramienta::factory()->create(['categoria' => 'jardin']);
    Herramienta::factory()->create(['categoria' => 'cocina']);

    $this->getJson('/api/v1/herramientas?categoria=jardin')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.categoria', 'jardin');
});

it('muestra el detalle de una herramienta', function () {
    $herramienta = Herramienta::factory()->create(['nombre' => 'Taladro']);

    $this->getJson("/api/v1/herramientas/{$herramienta->id}")
        ->assertOk()
        ->assertJsonPath('data.nombre', 'Taladro');
});

it('exige token para publicar una herramienta', function () {
    $this->postJson('/api/v1/herramientas', datosHerramienta())->assertUnauthorized();
});

it('publica una herramienta a nombre del usuario del token', function () {
    $usuario = User::factory()->create();
    Sanctum::actingAs($usuario);

    $this->postJson('/api/v1/herramientas', datosHerramienta())
        ->assertCreated()
        ->assertJsonPath('data.propietario', $usuario->name);

    $this->assertDatabaseHas('herramientas', ['nombre' => 'Sierra circular', 'user_id' => $usuario->id]);
});

it('valida los datos al publicar', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/herramientas', ['categoria' => 'inventada'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['nombre', 'categoria']);
});

it('ignora un user_id enviado por el cliente (asignación masiva)', function () {
    $usuario = User::factory()->create();
    $otro = User::factory()->create();
    Sanctum::actingAs($usuario);

    $this->postJson('/api/v1/herramientas', datosHerramienta(['user_id' => $otro->id]))->assertCreated();

    $this->assertDatabaseHas('herramientas', ['nombre' => 'Sierra circular', 'user_id' => $usuario->id]);
    $this->assertDatabaseMissing('herramientas', ['nombre' => 'Sierra circular', 'user_id' => $otro->id]);
});

it('solo el dueño cambia la disponibilidad de su herramienta', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $herramienta = Herramienta::factory()->for($dueno, 'propietario')->create(['disponible' => true]);

    Sanctum::actingAs($otro);
    $this->patchJson("/api/v1/herramientas/{$herramienta->id}/disponibilidad")->assertForbidden();

    Sanctum::actingAs($dueno);
    $this->patchJson("/api/v1/herramientas/{$herramienta->id}/disponibilidad")
        ->assertOk()
        ->assertJsonPath('data.disponible', false);
});

it('cierra la sesión eliminando el token', function () {
    $usuario = User::factory()->create();
    $token = $usuario->createToken('prueba')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/logout')->assertOk();

    expect($usuario->tokens()->count())->toBe(0);
});
