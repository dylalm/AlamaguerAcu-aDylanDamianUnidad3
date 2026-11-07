<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Valida las credenciales y entrega un token de acceso (Laravel Sanctum). */
    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'dispositivo' => ['nullable', 'string', 'max:60'],
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        if (! $usuario || ! Hash::check($datos['password'], $usuario->password)) {
            return response()->json(['mensaje' => 'Credenciales incorrectas.'], 401);
        }

        $token = $usuario->createToken($datos['dispositivo'] ?? 'api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'tipo' => 'Bearer',
            'usuario' => ['id' => $usuario->id, 'nombre' => $usuario->name],
        ]);
    }

    /** Revoca el token con el que se hizo la petición. */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Sesión cerrada.']);
    }
}
