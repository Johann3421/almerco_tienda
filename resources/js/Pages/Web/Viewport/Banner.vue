<script setup>
import { ref, defineProps } from 'vue';

const props = defineProps({
    banners: Array
});

const banners = ref([]);
banners.value = props.banners;

const getBannerImagePath = (banner) => {
    return `/storage/${banner.file_path}/${banner.file}`;
}

const colors = [
    'white',
];

</script>
<template>
    <v-carousel hide-delimiters show-arrows="hover" cycle class="mt-16" style="height: 100%; background-color: transparent">
        <v-carousel-item v-for="(banner, index) in banners" :key="index">
            <v-sheet>
                <img :src="getBannerImagePath(banner)" class="w-full object-cover" alt="Banner" />
            </v-sheet>
        </v-carousel-item>
    </v-carousel>
</template>
