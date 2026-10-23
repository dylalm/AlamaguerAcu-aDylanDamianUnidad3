<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import HerramientaCard from '@/Components/HerramientaCard.vue';

const props = defineProps({
    herramientas: { type: Array, required: true },
    categorias: { type: Array, required: true },
    filtros: { type: Object, required: true },
});

const buscar = ref(props.filtros.buscar ?? '');
const categoria = ref(props.filtros.categoria ?? '');
const soloDisponibles = ref(Boolean(props.filtros.disponibles));
const mensaje = ref('');

const hayFiltros = computed(() => buscar.value !== '' || categoria.value !== '' || soloDisponibles.value);

function aplicarFiltros() {
    router.get(
        route('herramientas.index'),
        {
            buscar: buscar.value || undefined,
            categoria: categoria.value || undefined,
            disponibles: soloDisponibles.value ? 1 : undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true, only: ['herramientas', 'filtros'] },
    );
}

// Espera un momento después de escribir para no pedir datos en cada tecla
let temporizador;
watch([buscar, categoria, soloDisponibles], () => {
    clearTimeout(temporizador);
    temporizador = setTimeout(aplicarFiltros, 350);
});

function limpiarFiltros() {
    buscar.value = '';
    categoria.value = '';
    soloDisponibles.value = false;
}

function pedir(herramienta) {
    mensaje.value = `Pronto podrás pedir prestada "${herramienta.nombre}" a ${herramienta.propietario}: las reservas llegan en el Sprint 3.`;
}
</script>

<template>
    <Head title="Herramientas cerca de ti" />

    <div class="min-h-screen bg-[#f7f5ef] text-[#1f2a24]">
        <header class="bg-[#1b4332] px-6 py-4 text-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between">
                <Link href="/" class="text-xl font-bold">ManoVecina</Link>
                <Link href="/" class="text-sm opacity-90 hover:opacity-100">← Inicio</Link>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-8">
            <h1 class="text-3xl font-bold text-[#1b4332]">Herramientas cerca de ti</h1>
            <p class="mt-1 text-gray-600">Busca lo que necesitas y pídeselo prestado a un vecino. Los resultados se ordenan del más cercano al más lejano.</p>

            <form class="mt-6 grid gap-3 rounded-xl bg-white p-4 shadow-sm sm:grid-cols-[1fr_200px_auto] sm:items-center" @submit.prevent>
                <div>
                    <label for="buscar" class="sr-only">Buscar herramientas</label>
                    <input
                        id="buscar"
                        v-model="buscar"
                        type="search"
                        maxlength="60"
                        placeholder="Busca un taladro, una escalera…"
                        class="w-full rounded-lg border-gray-300 focus:border-[#1b4332] focus:ring-[#1b4332]"
                    />
                </div>

                <div>
                    <label for="categoria" class="sr-only">Categoría</label>
                    <select
                        id="categoria"
                        v-model="categoria"
                        class="w-full rounded-lg border-gray-300 focus:border-[#1b4332] focus:ring-[#1b4332]"
                    >
                        <option value="">Todas las categorías</option>
                        <option v-for="opcion in categorias" :key="opcion.valor" :value="opcion.valor">
                            {{ opcion.etiqueta }}
                        </option>
                    </select>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input
                        v-model="soloDisponibles"
                        type="checkbox"
                        class="rounded border-gray-300 text-[#1b4332] focus:ring-[#1b4332]"
                    />
                    Solo disponibles
                </label>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm text-gray-600">
                <p aria-live="polite">
                    {{ herramientas.length }}
                    {{ herramientas.length === 1 ? 'herramienta encontrada' : 'herramientas encontradas' }}
                </p>
                <button
                    v-if="hayFiltros"
                    type="button"
                    class="font-semibold text-[#1b4332] underline"
                    @click="limpiarFiltros"
                >
                    Limpiar filtros
                </button>
            </div>

            <div
                v-if="mensaje"
                role="status"
                class="mt-4 rounded-lg bg-[#d68c45]/15 px-4 py-3 text-sm text-[#7a4a12]"
            >
                {{ mensaje }}
            </div>

            <div v-if="herramientas.length" class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <HerramientaCard
                    v-for="herramienta in herramientas"
                    :key="herramienta.id"
                    :herramienta="herramienta"
                    @solicitar="pedir"
                />
            </div>

            <div v-else class="mt-8 rounded-xl bg-white p-10 text-center shadow-sm">
                <p class="text-lg font-semibold text-[#1b4332]">No encontramos herramientas con esos filtros</p>
                <p class="mt-1 text-sm text-gray-600">Prueba con otra palabra o quita alguna categoría.</p>
            </div>
        </main>
    </div>
</template>
