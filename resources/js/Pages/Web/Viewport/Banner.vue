<script setup>
import { ref, defineProps } from 'vue';

const props = defineProps({
    banners: Array
});

const banners = ref(props.banners || []);

const getBannerImagePath = (banner) => {
    return `/storage/${banner.file_path}/${banner.file}`;
};

const navigateToBanner = (banner) => {
    if (banner.url) {
        window.location.href = banner.url; // Redirige al usuario a la URL del banner
    }
};
</script>

<template>
    <v-carousel hide-delimiters show-arrows="hover" cycle class="mt-16" style="height: 100%; background-color: transparent">
        <v-carousel-item v-for="(banner, index) in banners" :key="index">
            <v-sheet>
                <img 
                    :src="getBannerImagePath(banner)" 
                    class="w-full object-cover cursor-pointer" 
                    alt="Banner" 
                    @click="navigateToBanner(banner)" 
                />
            </v-sheet>
        </v-carousel-item>
    </v-carousel>
</template>