<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Las pruebas no dependen de que los assets de Vite estén compilados
        $this->withoutVite();

        // Inertia busca las páginas Vue en js/Pages (en minúsculas o mayúsculas, según el sistema de archivos)
        config(['inertia.testing.page_paths' => [resource_path('js/Pages'), resource_path('js/pages')]]);
    }
}
