<script setup>
import { ref, defineProps } from 'vue';
const props = defineProps({
    filter: Object, // Define el tipo de dato para la prop producto como un objeto
    filteritems: Object
});
const filteritems = ref([]);
const filter = ref('');
const codigosSeleccionados = ref([]);
filteritems.value = props.filteritems;
filter.value = props.filter[0].name;
const hacerclick = async (filteritem) => {
    const index = codigosSeleccionados.value.indexOf(filteritem.id);
    if (index === -1) {
        codigosSeleccionados.value.push(filteritem.id); // Agregar el código al arreglo si no está presente
    } else {
        codigosSeleccionados.value.splice(index, 1); // Eliminar el código del arreglo si está presente
    }
    // Realizar la solicitud HTTP al controlador con los filtros seleccionados
    try {
        const response = await axios.post('/search-products', {
            filters: codigosSeleccionados.value
        });
    } catch (error) {
        console.error('Error al enviar la solicitud al controlador:', error);
    }
};
</script>
<template>
    <details class="rounded border border-gray-300 bg-white">
        <summary
            class="flex cursor-pointer items-center justify-between gap-2 p-3 text-gray-900 transition">
            <span class="text-sm font-medium"> {{  filter }} </span>

            <span class="transition group-open:-rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </span>
        </summary>

        <div class="border-gray-200 bg-white">
            <ul class="space-y-1 border-t border-gray-200 p-2">
                <ul class="space-y-1 border-gray-200 p-2">
                    <li v-for="(filteritem, index) in filteritems" :key="index">
                      <label :for="'Filter' + index" class="inline-flex items-center gap-2">
                        <input type="checkbox" :id="'Filter' + index" class="size-5 rounded border-gray-300" @change="hacerclick(filteritem, $event)" />
                        <span class="text-sm font-medium text-gray-700">{{ filteritem.name }}</span>
                      </label>
                    </li>
                </ul>
            </ul>
        </div>
    </details>
</template>
