<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="ManoVecina · Herramientas y habilidades entre vecinos" />

    <div class="mv">
        <header class="mv-hero">
            <nav v-if="canLogin" class="mv-nav">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')">Panel</Link>
                <template v-else>
                    <Link :href="route('login')">Iniciar sesión</Link>
                    <Link v-if="canRegister" :href="route('register')">Registrarse</Link>
                </template>
            </nav>
            <h1>ManoVecina</h1>
            <p>Presta lo que no usas, pide lo que necesitas y ayuda a tus vecinos. Sin dinero: con puntos de confianza.</p>
            <Link class="mv-btn" :href="route('herramientas.index')">Explorar herramientas</Link>
            <a class="mv-btn mv-btn-claro" href="#como-funciona">Cómo funciona</a>
        </header>

        <main class="mv-main">
            <h2>¿Qué puedes hacer?</h2>
            <div class="mv-grid">
                <div class="mv-card"><h3>Prestar herramientas</h3><p>Taladros, escaleras, podadoras: publica lo que tienes y apártalo por calendario.</p></div>
                <div class="mv-card"><h3>Intercambiar habilidades</h3><p>Desde arreglar una gotera hasta enseñar a tejer: cada vecino sabe algo útil.</p></div>
                <div class="mv-card"><h3>Ganar confianza</h3><p>Cada intercambio suma puntos y reseñas que respaldan a la comunidad.</p></div>
            </div>

            <h2 id="como-funciona">Cómo funciona</h2>
            <ol>
                <li>Regístrate con tu ubicación aproximada.</li>
                <li>Busca herramientas o habilidades cerca de ti.</li>
                <li>Reserva, coordina por chat y califica al terminar.</li>
            </ol>
        </main>

        <footer class="mv-footer">ManoVecina v1.0.0 · Laravel + Inertia + Vue · despliegue automático con GitHub Actions</footer>
    </div>
</template>

<style scoped>
.mv { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: #f7f5ef; color: #1f2a24; line-height: 1.6; min-height: 100vh; }
.mv-hero { position: relative; background: #1b4332; color: #fff; padding: 64px 24px; text-align: center; }
.mv-hero h1 { margin: 0 0 8px; font-size: 2.4rem; }
.mv-hero p { margin: 0 auto; max-width: 560px; opacity: .9; }
.mv-nav { position: absolute; top: 16px; right: 24px; display: flex; gap: 16px; }
.mv-nav a { color: #fff; text-decoration: none; font-size: .9rem; }
.mv-btn { display: inline-block; margin-top: 24px; padding: 12px 24px; background: #d68c45; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; }
.mv-btn + .mv-btn { margin-left: 12px; }
.mv-btn-claro { background: transparent; border: 2px solid #d68c45; }
.mv-main { max-width: 960px; margin: 0 auto; padding: 40px 24px; }
.mv-main h2 { color: #1b4332; text-align: center; }
.mv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 24px; }
.mv-card { background: #fff; border-radius: 12px; padding: 24px; border-top: 4px solid #d68c45; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.mv-card h3 { margin-top: 0; color: #1b4332; }
.mv-main ol { max-width: 560px; margin: 24px auto; }
.mv-footer { text-align: center; padding: 24px; font-size: .85rem; color: #5b665f; }
</style>
