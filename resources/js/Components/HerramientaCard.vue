<script setup>
defineProps({
    herramienta: { type: Object, required: true },
});

defineEmits(['solicitar']);

const iconos = {
    jardin: '🌱',
    construccion: '🔨',
    hogar: '🏠',
    cocina: '🍳',
    electricidad: '⚡',
    otros: '🧰',
};
</script>

<template>
    <article class="flex flex-col rounded-xl border-t-4 border-[#d68c45] bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <span class="text-3xl" aria-hidden="true">{{ iconos[herramienta.categoria] ?? '🧰' }}</span>
            <span
                class="rounded-full px-3 py-1 text-xs font-semibold"
                :class="herramienta.disponible ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
            >
                {{ herramienta.disponible ? 'Disponible' : 'Prestada' }}
            </span>
        </div>

        <h3 class="mt-3 text-lg font-semibold text-[#1b4332]">{{ herramienta.nombre }}</h3>
        <p class="mt-1 flex-1 text-sm text-gray-600">{{ herramienta.descripcion }}</p>

        <dl class="mt-4 space-y-1 text-sm text-gray-700">
            <div class="flex justify-between gap-2">
                <dt class="text-gray-500">Categoría</dt>
                <dd>{{ herramienta.categoria_etiqueta }}</dd>
            </div>
            <div class="flex justify-between gap-2">
                <dt class="text-gray-500">Vecino</dt>
                <dd>{{ herramienta.propietario }}</dd>
            </div>
            <div class="flex justify-between gap-2">
                <dt class="text-gray-500">Zona</dt>
                <dd>{{ herramienta.barrio }} · a {{ herramienta.distancia_km }} km</dd>
            </div>
        </dl>

        <button
            type="button"
            class="mt-4 rounded-lg bg-[#1b4332] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#2d6a4f] disabled:cursor-not-allowed disabled:bg-gray-300"
            :disabled="!herramienta.disponible"
            @click="$emit('solicitar', herramienta)"
        >
            {{ herramienta.disponible ? 'Pedir prestada' : 'No disponible por ahora' }}
        </button>
    </article>
</template>
