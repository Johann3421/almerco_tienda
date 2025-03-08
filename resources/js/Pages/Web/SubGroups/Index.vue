<script setup>
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Index from '@/Layouts/Web/SubGroups/Index.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
import { defineProps } from 'vue';
defineProps({
    categories: Array
});
</script>

<template>
    <AppWebLayout title="SEKAI TECH" class="bg-fondoback" :categories="$page.props.categories" >
        <Header :categories="$page.props.categories" :images="$page.props.images" />
        <div class="mt-16">
            <Index :subgroup="$page.props.subgroup" :products="$page.props.products" :images="$page.props.images" :brands="$page.props.brands" />
        </div>
        <!-- NAV EN FORMATO MOBIL -->
        <NavMobil :categories="$page.props.categories" :images="$page.props.images" />
    </AppWebLayout>
</template>
