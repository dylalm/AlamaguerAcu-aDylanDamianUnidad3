<?php

use App\Models\Herramienta;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('redirige al inicio de sesión a quien no ha entrado', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('muestra solo las herramientas y estadísticas del vecino', function () {
    $vecino = User::factory()->create();

    Herramienta::factory()->count(2)->for($vecino, 'propietario')->create();
    Herramienta::factory()->prestada()->for($vecino, 'propietario')->create();
    Herramienta::factory()->create(); // pertenece a otro vecino

    $this->actingAs($vecino)->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('herramientas', 3)
            ->where('estadisticas.total', 3)
            ->where('estadisticas.disponibles', 2)
            ->where('estadisticas.prestadas', 1));
});

it('permite alternar la disponibilidad de una herramienta propia', function () {
    $vecino = User::factory()->create();
    $herramienta = Herramienta::factory()->for($vecino, 'propietario')->create(['disponible' => true]);

    $this->actingAs($vecino)
        ->patch(route('herramientas.disponibilidad', $herramienta))
        ->assertRedirect();

    expect($herramienta->fresh()->disponible)->toBeFalse();
});

it('no permite cambiar la disponibilidad de la herramienta de otro vecino', function () {
    $vecino = User::factory()->create();
    $ajena = Herramienta::factory()->create(['disponible' => true]);

    $this->actingAs($vecino)
        ->patch(route('herramientas.disponibilidad', $ajena))
        ->assertForbidden();

    expect($ajena->fresh()->disponible)->toBeTrue();
});
