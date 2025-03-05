<script setup>
import tiktok from '@assets/img/extra/tiktokblack.png';
</script>

<template>
    <ul class="flex items-center">
        <!-- Icono de Facebook -->
        <li>
            <a target="_blank" class="h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.facebook.com/GrupoalmercoPeru/">
                <i class="mdi text-black mdi-facebook text-3xl"></i>
            </a>
        </li>

        <!-- Icono de WhatsApp -->
        <li>
            <a target="_blank" class="h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://wa.me/51991375813?text=¡Hola!%20Quiero%20recibir%20mas%20información%20y%20promociones.">
                <i class="mdi text-black mdi-whatsapp text-3xl"></i>
            </a>
        </li>

        <!-- Icono de TikTok -->
        <li>
            <a target="_blank" class="h-10 w-10 rounded-full flex items-center justify-center transition hover:opacity-85" href="https://www.tiktok.com/@grupo_almerco">
                <img :src="tiktok" alt="tiktokblack">
            </a>
        </li>
    </ul>
</template>