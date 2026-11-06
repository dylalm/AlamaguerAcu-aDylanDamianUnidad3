# Mecanismos de seguridad

Cada mecanismo indica dónde está en el código y cómo demostrarlo con una captura de pantalla.

| # | Mecanismo | Dónde está | Cómo demostrarlo |
|---|-----------|------------|------------------|
| 1 | Contraseñas cifradas (bcrypt) | Modelo `User` (cast `hashed`) | `php artisan tinker --execute="echo App\Models\User::first()->password;"` muestra un hash y no la contraseña. |
| 2 | Autenticación y rutas protegidas | Middleware `auth` y `verified` en `routes/web.php` | Abrir `/dashboard` sin iniciar sesión redirige a `/login`. |
| 3 | Protección CSRF | Middleware de Laravel y cookie `XSRF-TOKEN` | `curl.exe -i -X POST http://localhost:8000/logout` responde `419 Page Expired`. |
| 4 | Límite de intentos de inicio de sesión | `LoginRequest` (Breeze) | Fallar 5 veces el login muestra el mensaje de "Too many login attempts". |
| 5 | Autorización con política | `app/Policies/HerramientaPolicy.php` | Un vecino que intenta cambiar la herramienta de otro recibe `403`. Se ve en la prueba `SeguridadTest`. |
| 6 | Cabeceras de seguridad | `app/Http/Middleware/SecurityHeaders.php` | `curl.exe -I http://localhost:8000/` o DevTools → Network → Response Headers. |
| 7 | Protección contra inyección SQL | Eloquent con parámetros preparados | Buscar `' OR 1=1 --` en el catálogo no devuelve datos de más. |
| 8 | Protección contra XSS | Vue escapa el HTML al mostrar texto | Publicar (con la API) una herramienta llamada `<script>alert(1)</script>`: aparece como texto y no se ejecuta. |
| 9 | Protección de asignación masiva | `$fillable` en el modelo `Herramienta` | Se demuestra en la API: un `user_id` enviado por el cliente se ignora. |
| 10 | Validación de datos | `$request->validate()` en los controladores | Una petición con datos inválidos devuelve `422` con los errores. |
| 11 | HTTPS y HSTS | Cabecera `Strict-Transport-Security` en producción | El candado del navegador en el sitio publicado. |
| 12 | Secretos fuera del repositorio | `.env` en `.gitignore`; `APP_KEY` como variable del hosting | El repositorio en GitHub no contiene `.env`. |

Para ver los mecanismos 1, 5 y 6 funcionando de forma automática:

```bash
php artisan test --filter=SeguridadTest
```
