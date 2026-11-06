<?php

use App\Http\Controllers\Api\HerramientaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/herramientas', [HerramientaController::class, 'index']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
