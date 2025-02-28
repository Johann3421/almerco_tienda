<script setup>
import { ref } from "vue";
import { Head, Link } from '@inertiajs/vue3';
import Footer from '@/Pages/Web/Viewport/Footer.vue';
import { useSettingStore } from '@stores/SettingStore';
import Favicon from "@assets/img/favicon.png";

defineProps({
    title: String
});
const settingGlobal = useSettingStore();
const settingsdolar = ref(0);
const settingimagensuperior = ref('');
const settingimagenmedio = ref('');
const settingimagensuperiormobil = ref('');
const settingimagenmediomobil = ref('');
const getDolarValue = async () => {
    try {
        const response = await axios.get(`/settingdolar`);
        const tipoDeCambio = response.data.tipo_de_cambio;
        const imagensuperior = response.data.img_superior;
        const imagenmedio = response.data.img_medio;
        const imagensuperiormobil = response.data.img_superior_mobile;
        const imagenmediomobil = response.data.img_medio_mobile;
        settingsdolar.value = parseFloat(tipoDeCambio.value);
        settingimagensuperior.value = imagensuperior;
        settingimagenmedio.value = imagenmedio;
        settingimagensuperiormobil.value = imagensuperiormobil;
        settingimagenmediomobil.value = imagenmediomobil;
        settingGlobal.updateDolarValue(settingsdolar.value,settingimagensuperior.value,settingimagenmedio.value, settingimagensuperiormobil.value, settingimagenmediomobil.value);
      } catch (error) {
        console.error('Error al buscar configuracion:', error);
      }
};
getDolarValue();
</script>

<template>
    <div>
        <Head :title="title">
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="Somos una empresa especializada en el comercio de productos tecnológicos de las mejores marcas, ofreciendo garantía y un servicio de calidad.">
            <meta name="robots" content="index, follow, nocache">
            <meta property="og:title" content="Grupo Almerco">
            <meta property="og:description" content="Somos una empresa especializada en el comercio de productos tecnológicos de las mejores marcas, ofreciendo garantía y un servicio de calidad.">
            <meta property="og:url" content="https://grupoalmerco.com.pe/">
            <meta property="og:site_name" content="Grupo Almerco">
            <meta property="og:locale" content="es">
        </Head>

        <!-- TERMINO DEL NAV MOBIL -->
            <slot />
        <Footer />
    </div>
</template>
