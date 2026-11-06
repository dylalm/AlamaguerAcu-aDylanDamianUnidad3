<?php

use App\Models\Herramienta;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('guarda las contraseñas cifradas', function () {
    $usuario = User::factory()->create(['password' => 'secreto-123']);

    expect($usuario->password)->not->toBe('secreto-123');
    expect(Hash::check('secreto-123', $usuario->password))->toBeTrue();
});

it('envía cabeceras de seguridad en las respuestas', function () {
    $this->get('/')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('aplica la política: solo el dueño modifica su herramienta', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $herramienta = Herramienta::factory()->for($dueno, 'propietario')->create(['disponible' => true]);

    $this->actingAs($otro)
        ->patch(route('herramientas.disponibilidad', $herramienta))
        ->assertForbidden();

    expect($herramienta->fresh()->disponible)->toBeTrue();

    $this->actingAs($dueno)
        ->patch(route('herramientas.disponibilidad', $herramienta))
        ->assertRedirect();

    expect($herramienta->fresh()->disponible)->toBeFalse();
});

it('bloquea el inicio de sesión después de varios intentos fallidos', function () {
    $usuario = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta']);
    }

    // Aun con la contraseña correcta, el acceso sigue bloqueado temporalmente
    $this->post('/login', ['email' => $usuario->email, 'password' => 'password']);

    $this->assertGuest();
});
