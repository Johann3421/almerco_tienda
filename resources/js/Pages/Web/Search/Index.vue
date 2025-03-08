<script setup>
import { ref } from 'vue';
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Index from '@/Layouts/Web/Search/Index.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
// Define la variable reactiva para almacenar el término de búsqueda
const filteredProducts = ref([]);
// totalproducts.value = length.props.products;
const handleSearchChange = (searchTerm) => {
  filteredProducts.value = searchTerm;
}
</script>

<template>
    <AppWebLayout title="SEKAI TECH" class="bg-fondoback" :categories="$page.props.categories">
        <Header @search-change="handleSearchChange" :categories="$page.props.categories" :images="$page.props.images" />
        <div class="mt-60">
            <h1 class="font-bold text-xl px-5">RESULTADOS DE BÚSQUEDA: </h1>
            <hr>
            <Index :products="$page.props.products" :images="$page.props.images" :brands="$page.props.brands" />
        </div>
        <!-- NAV EN FORMATO MOBIL -->
        <NavMobil :categories="$page.props.categories" :images="$page.props.images" />
      </AppWebLayout>
</template>
