<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    year: {
        type: Number,
        default: new Date().getFullYear(),
    },
});
</script>

<template>
    <footer class="bg-white text-center text-xs p-3 w-full">
        <p>&copy; {{ year }} - Todos los derechos reservados</p>
    </footer>
</template>
