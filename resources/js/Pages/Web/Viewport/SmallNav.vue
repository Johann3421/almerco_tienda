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
        <div class="relative sm:flex sm:text-xs xl:text-md items-center">
            <div class="absolute w-1 bg-orange-950 items-center h-10 sm:h-full"></div>
            <div class="text-center sm:flex sm:items-center gap-2 lg:h-12">
                <button class="bg-orange font-bold p-2 w-full sm:w-60 rounded-r-full sm:text-left lg:h-12 xl:text-lg">> {{ nombreboton }}</button>
                <p class="xl:text-lg">Los Productos que solo encontraras en Grupo Almerco</p>
            </div>
        </div>
        <Link :href="route('categoryid', { slug: slugOfCategoryWithId1 })" class="flex items-center justify-center rounded-full py-2 bg-neutral-800 text-white w-full sm:w-60 xl:w-80 sm:text-xs xl:text-lg hover:bg-naranja font-bold">
            > VER MAS CATEGORIAS
        </Link>
    </div>
</template>
