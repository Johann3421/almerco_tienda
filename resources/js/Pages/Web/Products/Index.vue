<script setup>
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Index from '@/Layouts/Web/Products/Index.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
import { defineProps, computed } from 'vue';

// Definir props
const props = defineProps({
    producto: Object,
    categories: Array,
    images: Object,
    productimages: Object,
    products: Array,
    productref: Object,
    subgroup: Object
});

// ❌ Vue NO interpreta {{ }} en <noscript>, así que usamos computed
const noscriptContent = computed(() => `
    <h1>${props.producto?.name || 'Descubre nuestros productos'}</h1>
    <p>${props.producto?.description || 'Este producto es una excelente opción para ti.'}</p>

    <h2>Características Principales</h2>
    <ul>
        ${(props.producto?.features || []).map(feature => `<li>${feature}</li>`).join('')}
    </ul>

    <h2>Productos recomendados</h2>
    <ul>
        ${(props.products || []).slice(0, 5).map(product => `
            <li><a href="/producto/${product.slug}">${product.name}</a></li>
        `).join('')}
    </ul>
`);
</script>

<!-- ✅ NOSCRIPT FUERA DEL TEMPLATE -->
<noscript v-html="noscriptContent"></noscript>

<template>
    <AppWebLayout :title="producto?.name || 'Producto desconocido'" class="bg-fondoback" :categories="categories">
        <Header :categories="categories" :images="images" />
        <div class="h-12 lg:h-44"></div>

        <div class="visually-hidden">
            <h1>{{ producto?.name || 'Descubre nuestros productos' }}</h1>
            <p>{{ producto?.description || 'Este producto es una excelente opción para ti.' }}</p>
        </div>

        <Index 
            :producto="producto" 
            :images="images" 
            :productimages="productimages" 
            :products="products" 
            :productref="productref" 
            :subgroup="subgroup" 
            :brands="$page.props.brands" 
            :productSpecifications="$page.props.productSpecifications" 
        />

        <NavMobil :categories="categories" :images="$page.props.images" />
    </AppWebLayout>
</template>


<style>
.visually-hidden {
    position: absolute;
    clip-path: inset(50%);
    width: 1px;
    height: 1px;
    overflow: hidden;
    white-space: nowrap;
}

</style>

