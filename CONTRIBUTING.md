# Flujo de trabajo del repositorio

Modelo: **Git Flow simplificado**, alineado con sprints de dos semanas (Scrum).

## Nombres de ramas

- `feature/<numero-issue>-<descripcion-corta>` → `feature/12-busqueda-por-cercania`
- `release/<version>`                          → `release/v1.1.0`
- `hotfix/<descripcion-corta>`                 → `hotfix/error-en-footer`

## Ciclo de una función

1. Tomar un issue del tablero del sprint.
2. `git checkout develop && git pull`
3. `git checkout -b feature/12-busqueda-por-cercania`
4. Programar con commits pequeños (convención de abajo).
5. Antes de subir: `php artisan test` y `./vendor/bin/pint`
6. `git push -u origin feature/12-busqueda-por-cercania`
7. Abrir un Pull Request hacia `develop` con la plantilla.
8. Con el workflow **CI** en verde, fusionar con *Create a merge commit* y borrar la rama.

## Cierre de sprint / versión

1. `git checkout -b release/v1.1.0 develop`
2. Actualizar `CHANGELOG.md`; solo se corrigen errores menores.
3. Pull Request hacia `main`: al fusionar se despliega en Render.
4. `git tag -a v1.1.0 -m "Versión 1.1.0"` y `git push --tags`
5. Fusionar `release/v1.1.0` de vuelta en `develop`.

## Corrección urgente (hotfix)

1. `git checkout -b hotfix/error-en-footer main`
2. Corregir, subir versión de parche (`v1.1.1`) y abrir PR hacia `main`.
3. Fusionar también en `develop`.

## Reglas específicas de Laravel

- **Nunca** se sube `.env`; las variables nuevas se agregan a `.env.example`.
- `composer.lock` y `package-lock.json` **sí** se suben, para que todos instalen las mismas versiones.
- Cada cambio en la base de datos va en una **migración nueva**; no se editan migraciones ya fusionadas en `develop`.
- No se suben `vendor/`, `node_modules/` ni `public/build/` (ya están en `.gitignore`).

## Convención de commits (Conventional Commits)

Formato: `tipo(alcance opcional): descripción en minúsculas`

| Tipo | Uso |
|------|-----|
| `feat` | Nueva funcionalidad |
| `fix` | Corrección de un error |
| `docs` | Documentación |
| `style` | Formato, sin cambios de lógica |
| `refactor` | Reestructura sin cambiar comportamiento |
| `test` | Pruebas |
| `chore` | Mantenimiento, configuración, CI, despliegue |

## Versionado

Versionado semántico `MAJOR.MINOR.PATCH`.
