<?php

use App\Models\Herramienta;
use Inertia\Testing\AssertableInertia as Assert;

it('muestra el catálogo de herramientas', function () {
    Herramienta::factory()->count(3)->create();

    $this->get(route('herramientas.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Herramientas/Index')
            ->has('herramientas', 3)
            ->has('categorias', 6));
});

it('filtra por categoría', function () {
    Herramienta::factory()->create(['categoria' => 'jardin']);
    Herramienta::factory()->create(['categoria' => 'cocina']);

    $this->get(route('herramientas.index', ['categoria' => 'jardin']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('herramientas', 1)
            ->where('herramientas.0.categoria', 'jardin'));
});

it('busca por nombre o descripción sin distinguir mayúsculas', function () {
    Herramienta::factory()->create(['nombre' => 'Taladro inalámbrico', 'descripcion' => 'Con dos baterías']);
    Herramienta::factory()->create(['nombre' => 'Podadora', 'descripcion' => 'Eléctrica']);

    $this->get(route('herramientas.index', ['buscar' => 'TALADRO']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('herramientas', 1)
            ->where('herramientas.0.nombre', 'Taladro inalámbrico'));
});

it('oculta las herramientas prestadas cuando se piden solo las disponibles', function () {
    Herramienta::factory()->create(['nombre' => 'Escalera']);
    Herramienta::factory()->prestada()->create(['nombre' => 'Hidrolavadora']);

    $this->get(route('herramientas.index', ['disponibles' => 1]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('herramientas', 1)
            ->where('herramientas.0.nombre', 'Escalera'));
});

it('ordena los resultados del más cercano al más lejano', function () {
    $centro = config('manovecina.centro');

    Herramienta::factory()->create([
        'nombre' => 'Lejos',
        'latitud' => $centro['latitud'] + 0.05,
        'longitud' => $centro['longitud'],
    ]);
    Herramienta::factory()->create([
        'nombre' => 'Cerca',
        'latitud' => $centro['latitud'] + 0.001,
        'longitud' => $centro['longitud'],
    ]);

    $this->get(route('herramientas.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('herramientas.0.nombre', 'Cerca')
            ->where('herramientas.1.nombre', 'Lejos'));
});

it('rechaza una categoría que no existe', function () {
    $this->get(route('herramientas.index', ['categoria' => 'inventada']))
        ->assertSessionHasErrors('categoria');
});
