<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    categories: Array,
    nombreboton: String
});
const nombreboton = props.nombreboton;
// Buscar la categoría con id igual a 1
const categoryWithId1 = props.categories.find(category => category.id === 1);
// Obtener el slug de la categoría si existe
const slugOfCategoryWithId1 = categoryWithId1 ? categoryWithId1.slug : null;
</script>
<template>
    <div class="grid grid-cols-1 gap-2 p-10 sm:flex sm:justify-between sm:px-16 md:px-20 lg:px-24 xl:px-36">
        <!-- Primer bloque con fondo azul sólido -->
        <div class="relative sm:flex sm:text-xs xl:text-md items-center">
            <!-- Línea vertical azul oscuro -->
            <div class="absolute w-1 bg-sky-800 items-center h-10 sm:h-full"></div>
            <div class="text-center sm:flex sm:items-center gap-2 lg:h-12">
                <!-- Botón con fondo azul sólido -->
                <button class="bg-sky-700 font-bold p-2 w-full sm:w-60 rounded-r-full sm:text-left lg:h-12 xl:text-lg text-white">
                    > {{ nombreboton }}
                </button>
                <p class="xl:text-lg">Los Productos que solo encontraras en SEKAI TECH</p>
            </div>
        </div>

        <!-- Segundo bloque con fondo azul sólido y hover azul más claro -->
        <Link :href="route('categoryid', { slug: slugOfCategoryWithId1 })" class="flex items-center justify-center rounded-full py-2 bg-sky-800 text-white w-full sm:w-60 xl:w-80 sm:text-xs xl:text-lg hover:bg-sky-700 font-bold">
            > VER MAS CATEGORIAS
        </Link>
    </div>
</template>