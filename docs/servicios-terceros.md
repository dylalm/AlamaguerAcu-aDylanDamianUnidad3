# Web services de terceros

| Servicio | Para qué se usa | Dónde está en el código | Acceso |
|----------|-----------------|-------------------------|--------|
| **Open-Meteo** (api.open-meteo.com) | Mostrar el clima actual en el catálogo y recomendar si es buen momento para usar herramientas de exterior. | `app/Services/ClimaService.php` y `app/Http/Controllers/Api/ClimaController.php` | Gratuito para uso no comercial, sin clave de API. Se cita la fuente en pantalla. |
| **Nominatim / OpenStreetMap** (nominatim.openstreetmap.org) | Convertir el nombre de una zona en latitud y longitud al publicar una herramienta sin coordenadas. | `app/Services/GeocodificacionService.php` y `Api/HerramientaController@store` | Gratuito, con política de uso: identificar la aplicación con un `User-Agent`, máximo una consulta por segundo y no repetir búsquedas. |

Cómo se cuida el uso de los servicios externos:

- Las respuestas se guardan en caché (15 minutos el clima y un día las búsquedas de zonas).
- Hay un tiempo máximo de espera de 5 segundos y, si el servicio falla, la API responde `503` o `422` en lugar de caerse.
- El navegador nunca llama directo al servicio externo: llama a la API propia, que actúa de intermediaria.

## Cómo demostrarlo (capturas)

1. **Clima en la pantalla.** Abrir `http://localhost:8000/herramientas`: arriba del buscador aparece el cuadro con temperatura, descripción y recomendación.
2. **Clima en la API.** Abrir `http://localhost:8000/api/v1/clima` en el navegador y mostrar el JSON.
3. **Geocodificación.** Con un token (ver `docs/api-propia.md`), publicar sin coordenadas:
   ```powershell
   curl.exe -s -X POST http://localhost:8000/api/v1/herramientas -H "Authorization: Bearer TU_TOKEN" -H "Content-Type: application/json" -d "{\"nombre\":\"Sierra circular\",\"descripcion\":\"Con disco nuevo\",\"categoria\":\"construccion\",\"barrio\":\"Colonia Centro, Ciudad de Mexico\"}"
   ```
   La respuesta trae `latitud` y `longitud` calculadas por Nominatim.
4. **Pruebas automáticas.** `php artisan test --filter=ServiciosTercerosTest`.
