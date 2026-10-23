# ManoVecina

Plataforma web vecinal para prestar herramientas e intercambiar habilidades usando
puntos de confianza en lugar de dinero.

> **Estado: proyecto en desarrollo.** Se construye por sprints de dos semanas con Scrum.
> La tabla de abajo indica qué está listo y qué falta.

**Stack:** Laravel 12 (PHP 8.3) · Inertia.js · Vue 3 · Vite · Pest · SQLite (demo) / PostgreSQL (producción)

**Sitio publicado:** https://NOMBRE-DEL-SERVICIO.onrender.com  <!-- reemplazar con la URL real -->

## Estado del proyecto

| Sprint | Función | Estado |
|--------|---------|--------|
| 0 | Repositorio, flujo de ramas, CI y configuración del entorno | ✅ Listo |
| 1 | Registro e inicio de sesión de vecinos | ✅ Listo (Laravel Breeze) |
| 2 | Catálogo de herramientas: búsqueda, filtro por categoría, solo disponibles y orden por cercanía | ✅ Pantalla lista |
| 2 | Publicar una herramienta desde la interfaz | ⏳ Pendiente |
| 3 | Reservas con calendario | ⏳ Pendiente (el botón «Pedir prestada» es un marcador) |
| 4 | Chat entre vecinos | ⏳ Pendiente |
| 5 | Puntos de confianza y reseñas | ⏳ Pendiente |
| 6 | Notificaciones y recordatorios de devolución | ⏳ Pendiente |
| 7 | Prueba piloto con vecinos y lanzamiento | ⏳ Pendiente |

### Limitaciones conocidas

- La distancia del catálogo se calcula desde un punto fijo de ejemplo (`config/manovecina.php`); más adelante saldrá de la ubicación aproximada del vecino y de PostGIS.
- Los datos del catálogo son de ejemplo (se cargan con `php artisan migrate --seed`).
- En el despliegue de demostración se usa SQLite dentro del contenedor, que se reinicia en cada despliegue.

## Pantallas disponibles

| Ruta | Pantalla |
|------|----------|
| `/` | Inicio: presentación de ManoVecina |
| `/herramientas` | Herramientas cerca de ti: catálogo con búsqueda y filtros |
| `/login`, `/register` | Inicio de sesión y registro |

## Requisitos

- Git 2.40 o superior
- PHP 8.2 o superior y Composer 2
- Node.js 20 o superior y npm

## Puesta en marcha local

```bash
git clone https://github.com/USUARIO/manovecina.git
cd manovecina
cp .env.example .env        # en PowerShell: copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
```

Después, en dos terminales:

```bash
npm run dev
php artisan serve
```

Abrir http://localhost:8000. Las cuentas de ejemplo son `lucia@manovecina.test`,
`carlos@manovecina.test`, `rosa@manovecina.test` y `mateo@manovecina.test`,
todas con contraseña `password`.

## Pruebas y estilo de código

```bash
php artisan test
./vendor/bin/pint --test
```

## Flujo de trabajo

Se usa Git Flow simplificado, alineado con los sprints:

| Rama | Propósito |
|------|-----------|
| `main` | Código en producción. Cada fusión genera un tag y un despliegue. |
| `develop` | Integración de las funciones terminadas del sprint. |
| `feature/*` | Una rama por historia de usuario. Sale de `develop` y vuelve a `develop`. |
| `release/*` | Preparación de una versión. Sale de `develop` y se fusiona en `main` y `develop`. |
| `hotfix/*` | Corrección urgente en producción. Sale de `main` y se fusiona en `main` y `develop`. |

Los commits siguen Conventional Commits y las versiones usan versionado semántico.
Los detalles están en [CONTRIBUTING.md](CONTRIBUTING.md) y el historial de versiones en
[CHANGELOG.md](CHANGELOG.md).

## Automatización y despliegue

- **CI** (`.github/workflows/ci.yml`): en cada Pull Request instala dependencias, compila los assets, revisa el estilo con Pint y ejecuta las pruebas.
- **Despliegue** (`.github/workflows/deploy.yml`): al fusionar en `main` se llama al deploy hook de Render, que construye la imagen del `Dockerfile` y publica el sitio.

## Estructura del repositorio

```
manovecina/
├── app/                       # modelos y controladores (Herramienta, HerramientaController)
├── config/manovecina.php      # centro de referencia para calcular distancias
├── database/                  # migraciones, factories y seeders
├── resources/js/
│   ├── Pages/                 # pantallas Vue (Welcome, Herramientas/Index, auth)
│   └── Components/            # componentes reutilizables (HerramientaCard)
├── routes/web.php
├── tests/Feature/             # pruebas con Pest
├── .github/                   # workflows de CI y despliegue, plantillas de PR e issues
├── docker/start.sh  Dockerfile  render.yaml
└── CONTRIBUTING.md  CHANGELOG.md
```
