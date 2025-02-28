<script setup>
import { defineProps } from 'vue';
const props = defineProps({
    settingsbanner: Object
});
const settingsbanner = props.settingsbanner;
</script>

<template>
    <div id="promoDiv" class="bg-naranjaclaro text-white transition-all duration-500 h-12 w-full">
        <a v-if="settingsbanner && settingsbanner.url" :href="settingsbanner.url" target="_blank">
            <img v-if="settingsbanner && settingsbanner.file_path && settingsbanner.file" :src="`/storage/${settingsbanner.file_path}/${settingsbanner.file}`" alt="Imagen Promocion" class="h-full w-full object-cover">
        </a>
    </div>
</template>
