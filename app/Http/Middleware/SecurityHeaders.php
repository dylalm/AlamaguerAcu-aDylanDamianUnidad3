<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /** Agrega cabeceras HTTP de seguridad a todas las respuestas. */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Evita que el navegador "adivine" el tipo de archivo
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Evita que otro sitio muestre esta página dentro de un iframe (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Limita la información que se envía a otros sitios al seguir un enlace
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Restringe el acceso a funciones del dispositivo
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // Obliga a usar HTTPS en producción
        if ($request->isSecure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
