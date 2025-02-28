<script setup>
import { ref } from 'vue';
import Index from '@/Layouts/Web/Pasarelas/Index.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
const props = defineProps({
    categories: Array,
    images: Object,
    products: Array
});
const filteredProducts = ref([]);
const handleSearchChange = (searchTerm) => {
    filteredProducts.value = searchTerm;
}
</script>

<template>
    <AppWebLayout title="Grupo Almerco" class="bg-fondoback" :categories="categories">
        <Header @search-change="handleSearchChange" :categories="categories" :images="images" />
        <div class="mt-12">
            <Index :images="images" :products="products" />
        </div>
        <!-- NAV EN FORMATO MOBIL -->
        <NavMobil :categories="categories" :images="images" />
    </AppWebLayout>
</template>
