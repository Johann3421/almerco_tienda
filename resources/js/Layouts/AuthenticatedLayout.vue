<script setup>
import Menu from '@/Layouts/Partials/Menu.vue';
import Header from '@/Layouts/Partials/Header.vue';
import Footer from '@/Layouts/Partials/Footer.vue';
</script>

<template>
    <v-layout class="min-h-screen">
        <Header />

        <Menu />

        <v-main>
            <slot />
            <Footer />
        </v-main>
    </v-layout>
</template>
