<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;

class HerramientaController extends Controller
{
    // Devuelve todas las herramientas en formato JSON
    public function index()
    {
        // Solo devolvemos las que están disponibles
        $herramientas = Herramienta::where('disponible', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $herramientas,
        ], 200);
    }
}
