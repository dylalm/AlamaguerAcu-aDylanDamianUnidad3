<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    estadisticas: { type: Object, required: true },
    herramientas: { type: Array, required: true },
});

const tarjetas = computed(() => [
    { etiqueta: 'Herramientas publicadas', valor: props.estadisticas.total },
    { etiqueta: 'Disponibles', valor: props.estadisticas.disponibles },
    { etiqueta: 'Prestadas', valor: props.estadisticas.prestadas },
]);

function alternar(herramienta) {
    router.patch(route('herramientas.disponibilidad', herramienta.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Mi panel" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Mi panel</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl space-y-8 px-6">
                <section>
                    <h3 class="text-2xl font-bold text-[#1b4332]">Hola, {{ $page.props.auth.user.name }}</h3>
                    <p class="mt-1 text-gray-600">Aquí ves el resumen de lo que ofreces a tus vecinos.</p>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="tarjeta in tarjetas"
                        :key="tarjeta.etiqueta"
                        class="rounded-xl border-t-4 border-[#d68c45] bg-white p-5 shadow-sm"
                    >
                        <p class="text-sm text-gray-500">{{ tarjeta.etiqueta }}</p>
                        <p class="mt-1 text-3xl font-bold text-[#1b4332]">{{ tarjeta.valor }}</p>
                    </div>

                    <div class="rounded-xl border-t-4 border-gray-300 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Puntos de confianza</p>
                        <p class="mt-1 text-3xl font-bold text-gray-400">—</p>
                        <p class="mt-1 text-xs text-gray-500">Disponible en el Sprint 5</p>
                    </div>
                </section>

                <section class="rounded-xl bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-[#1b4332]">Mis herramientas</h3>
                        <Link
                            :href="route('herramientas.index')"
                            class="rounded-lg bg-[#1b4332] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2d6a4f]"
                        >
                            Ver el catálogo
                        </Link>
                    </div>

                    <ul v-if="herramientas.length" class="mt-4 divide-y divide-gray-100">
                        <li
                            v-for="herramienta in herramientas"
                            :key="herramienta.id"
                            class="flex flex-wrap items-center justify-between gap-3 py-3"
                        >
                            <div>
                                <p class="font-semibold text-gray-900">{{ herramienta.nombre }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ herramienta.categoria_etiqueta }} · {{ herramienta.barrio }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="herramienta.disponible ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                                >
                                    {{ herramienta.disponible ? 'Disponible' : 'Prestada' }}
                                </span>
                                <button
                                    type="button"
                                    class="text-sm font-semibold text-[#1b4332] underline"
                                    @click="alternar(herramienta)"
                                >
                                    {{ herramienta.disponible ? 'Marcar como prestada' : 'Marcar como disponible' }}
                                </button>
                            </div>
                        </li>
                    </ul>

                    <div v-else class="mt-6 rounded-lg bg-[#f7f5ef] p-8 text-center">
                        <p class="font-semibold text-[#1b4332]">Aún no tienes herramientas publicadas</p>
                        <p class="mt-1 text-sm text-gray-600">
                            Publicar herramientas desde la interfaz llegará pronto. Mientras tanto, explora lo que ofrecen tus vecinos.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
