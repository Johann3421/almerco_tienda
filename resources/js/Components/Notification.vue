<script setup>
import { ref, defineProps } from "vue";

const props = defineProps({
    color: {
        type: String,
        default: "#d1d1d1",
    },
    timeout: {
        type: Number,
        default: 2000,
    },
    vertical: {
        type: Boolean,
        default: true,
    },
    location: {
        type: String,
        default: "right top",
    },
    rounded: {
        type: String,
        default: "sm",
    }
});

</script>

<template>
    <v-snackbar :color="color" :timeout="timeout" :vertical="vertical" :rounded="rounded" :location="location">
        <slot />
    </v-snackbar>
</template>
