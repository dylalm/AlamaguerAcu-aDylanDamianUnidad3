<?php

namespace Database\Factories;

use App\Models\Herramienta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Herramienta>
 */
class HerramientaFactory extends Factory
{
    public function definition(): array
    {
        $centro = config('manovecina.centro');

        return [
            'user_id' => User::factory(),
            'nombre' => fake()->words(2, true),
            'descripcion' => fake()->sentence(),
            'categoria' => fake()->randomElement(array_keys(Herramienta::CATEGORIAS)),
            'barrio' => 'Barrio '.fake()->lastName(),
            'latitud' => $centro['latitud'] + fake()->randomFloat(4, -0.01, 0.01),
            'longitud' => $centro['longitud'] + fake()->randomFloat(4, -0.01, 0.01),
            'disponible' => true,
        ];
    }

    public function prestada(): static
    {
        return $this->state(fn () => ['disponible' => false]);
    }
}
