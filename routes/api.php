<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClimaController;
use App\Http\Controllers\Api\HerramientaController;
use App\Http\Middleware\ForzarJson;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST de ManoVecina (web services propios)
|--------------------------------------------------------------------------
| Base: /api/v1
*/

Route::prefix('v1')->middleware(ForzarJson::class)->group(function () {
    // Inicio de sesión: máximo 5 intentos por minuto
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('api.login');

    // Consulta pública: máximo 60 peticiones por minuto
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/herramientas', [HerramientaController::class, 'index'])->name('api.herramientas.index');
        Route::get('/herramientas/{herramienta}', [HerramientaController::class, 'show'])->name('api.herramientas.show');
        Route::get('/clima', ClimaController::class)->name('api.clima');
    });

    // Rutas protegidas con token (Sanctum)
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::post('/herramientas', [HerramientaController::class, 'store'])->name('api.herramientas.store');
        Route::patch('/herramientas/{herramienta}/disponibilidad', [HerramientaController::class, 'disponibilidad'])
            ->name('api.herramientas.disponibilidad');
    });
});
