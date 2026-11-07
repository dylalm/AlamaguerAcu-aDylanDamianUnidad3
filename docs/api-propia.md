# Web services propios (API REST)

API en `/api/v1`, protegida con tokens de Laravel Sanctum. Responde siempre en JSON.

| Método | Ruta | Acceso | Descripción |
|--------|------|--------|-------------|
| POST | `/api/v1/login` | Público (5 intentos por minuto) | Entrega un token Bearer |
| GET | `/api/v1/herramientas` | Público | Lista con filtros `buscar`, `categoria` y `disponibles=1` |
| GET | `/api/v1/herramientas/{id}` | Público | Detalle de una herramienta |
| POST | `/api/v1/herramientas` | Token | Publica una herramienta |
| PATCH | `/api/v1/herramientas/{id}/disponibilidad` | Token, solo el dueño | Marca como prestada o disponible |
| POST | `/api/v1/logout` | Token | Revoca el token |

## Cómo demostrarlo (capturas)

Se pueden usar Postman, Thunder Client (extensión de VS Code) o `curl.exe` en PowerShell.

1. **Consulta pública.** Abrir en el navegador `http://localhost:8000/api/v1/herramientas?categoria=jardin`.
2. **Inicio de sesión y token.**
   ```powershell
   curl.exe -s -X POST http://localhost:8000/api/v1/login -H "Content-Type: application/json" -d "{\"email\":\"lucia@manovecina.test\",\"password\":\"password\"}"
   ```
3. **Acceso sin token.** El mismo `POST /api/v1/herramientas` sin encabezado `Authorization` responde `401`.
4. **Publicar con token.**
   ```powershell
   curl.exe -s -X POST http://localhost:8000/api/v1/herramientas -H "Authorization: Bearer TU_TOKEN" -H "Content-Type: application/json" -d "{\"nombre\":\"Sierra circular\",\"descripcion\":\"Con disco nuevo\",\"categoria\":\"construccion\",\"barrio\":\"Col. Centro\",\"latitud\":19.4326,\"longitud\":-99.1332}"
   ```
5. **Validación.** Enviar la publicación con `categoria` inválida responde `422` con los errores.
6. **Autorización.** Con el token de Lucía, `PATCH` sobre la herramienta de otro vecino responde `403`.
7. **Pruebas automáticas.** `php artisan test --filter=ApiHerramientasTest`.
