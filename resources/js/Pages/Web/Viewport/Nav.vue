<script setup>
import tiktok from '@assets/img/extra/tiktokico.png';
</script>
<template>
    <ul class="flex items-center">
        <li>
            <a  target="_blank" class=" h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.facebook.com/GrupoalmercoPeru/">
                <i class="mdi text-white mdi-facebook text-3xl"></i>
            </a>
        </li>
        <li>
            <a target="_blank" class=" h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://wa.me/51991375813?text=¡Hola!%20Quiero%20recibir%20mas%20información%20y%20promociones.">
                <i class="mdi text-white mdi-whatsapp text-3xl"></i>
            </a>
        </li>
        <li>
            <a target="_blank" class="h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.tiktok.com/@grupo_almerco">
                <img :src="tiktok" alt="tiktok" class="p-2">
            </a>
        </li>
    </ul>
</template>
