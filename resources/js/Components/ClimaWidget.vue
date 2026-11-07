<script setup>
import { onMounted, ref } from 'vue';

const clima = ref(null);
const error = ref(false);
const cargando = ref(true);

// Llama a nuestra propia API, que a su vez consulta el servicio de terceros (Open-Meteo)
onMounted(async () => {
    try {
        const respuesta = await fetch('/api/v1/clima', { headers: { Accept: 'application/json' } });

        if (!respuesta.ok) {
            throw new Error('Sin datos de clima');
        }

        clima.value = await respuesta.json();
    } catch (e) {
        error.value = true;
    } finally {
        cargando.value = false;
    }
});
</script>

<template>
    <div class="rounded-xl bg-white p-4 shadow-sm" aria-live="polite">
        <p v-if="cargando" class="text-sm text-gray-500">Consultando el clima…</p>
        <p v-else-if="error" class="text-sm text-gray-500">El clima no está disponible por ahora.</p>
        <div v-else class="flex flex-wrap items-baseline gap-x-6 gap-y-1">
            <p class="text-2xl font-bold text-[#1b4332]">{{ clima.temperatura }} °C</p>
            <p class="text-sm text-gray-700">
                {{ clima.descripcion }} · viento {{ clima.viento_kmh }} km/h · lluvia {{ clima.precipitacion_mm }} mm
            </p>
            <p class="w-full text-sm text-[#7a4a12]">{{ clima.recomendacion }}</p>
            <p class="w-full text-xs text-gray-400">Datos: {{ clima.fuente }}</p>
        </div>
    </div>
</template>
