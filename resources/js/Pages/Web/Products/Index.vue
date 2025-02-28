<script setup>
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Index from '@/Layouts/Web/Products/Index.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
import { defineProps } from 'vue';
// Define las props del componente
const props = defineProps({
    producto: Object,  // Define el tipo de la prop productos como un Array
    categories: Array,
    images: Object,
    productimages: Object,
    products: Array,
    productref: Object,
    subgroup: Object
});
</script>

<template>
    <AppWebLayout title="Web" class="bg-fondoback" :categories="categories">
        <Header :categories="categories" :images="images" />
        <div class="h-12 lg:h-44">
        </div>
        <Index :producto="producto" :images="images" :productimages="productimages" :products="products" :productref="productref" :subgroup="subgroup" :brands="$page.props.brands" :productSpecifications="$page.props.productSpecifications" />
        <!-- NAV EN FORMATO MOBIL -->
        <NavMobil :categories="categories" :images="$page.props.images" />
    </AppWebLayout>
</template>
