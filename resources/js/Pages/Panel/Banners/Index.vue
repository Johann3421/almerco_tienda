<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SimpleHeader from '@/Components/SimpleHeader.vue';
import Notification from '@/Components/Notification.vue';
import DragAndDrop from '@/Components/DragAndDrop.vue';
import { Link, Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import useFormRules from "@utils/UseFormRules";
import Favicon from "@assets/img/favicon.png";
import { useBannerImageStore } from '@stores/BannerImageStore';

const props = defineProps({
    banners: {
        type: Array,
        required: true
    }
})

const BannerImage = useBannerImageStore();

const banners = ref([])

const fetch_loader = ref(false)

const fetch_loader_form = ref(false)

const fetchBanners = async (criteria) => {

    fetch_loader.value = true;

    try {
        const response = await axios.get(route('banners.find', criteria));

        banners.value = response.data;

    } catch (error) {
        handleError(error, "Error al cargar los Banners");
    }

    fetch_loader.value = false;
}

onMounted(async () => {

    banners.value = props.banners;

    // await fetchBanners("page");
})

const updating = ref(false);

const banner = ref({
    id: null,
    name: "",
    file_path: "",
    file: "",
    image: "",
    active: true,
    token: "",
})
const rules = useFormRules

const dialog_delete_banner = ref(false)

const form_create_banner = ref(null)

const form_update_banner = ref(null)

const Modal = (dialog, selection) => {

    if (dialog === "delete_banner") {
        dialog_delete_banner.value = true;

        banner.value = selection;
    }
}

const EditBanner = ($banner) => {
    BannerImage.$reset();

    updating.value = true;

    banner.value = $banner;
}

const storeBanner = async () => {

    const { valid } = await form_create_banner.value.validate()

    if (!BannerImage.getFile) {
        showAlert(true, "Error: Archivo no seleccionado", "#F9D7DA", 3000);
        return;
    }

    if (valid) {

        fetch_loader_form.value = true;

        try {
            const formData = new FormData();

            formData.append("name", banner.value.name);
            formData.append("image", BannerImage.getFile);
            formData.append("active", banner.value.active);

            const response = await axios.post(route('banners.store'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.status === 201) {

                banners.value.push(response.data.banner);

                showAlert(true, response.data.message, "#D4E7C5", 2000);
            }

        } catch (error) {
            handleError(error, "Error al agregar el Banner");
        } finally {
            fetch_loader_form.value = false;

            BannerImage.$reset();

            form_create_banner.value.reset();
        }
    }
}

const updateBanner = async () => {
    const { valid } = await form_update_banner.value.validate()

    if (valid) {

        fetch_loader_form.value = true;

        try {
            const formData = new FormData();

            formData.append("name", banner.value.name);
            formData.append("image", BannerImage.getFile);
            formData.append("active", banner.value.active);

            const response = await axios.post(route('banners.update', banner.value.id), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PATCH'
                }
            });

            if (response.status === 200) {

                const index = banners.value.findIndex(banner => banner.id === response.data.banner.id);

                banners.value[index] = response.data.banner;

                showAlert(true, response.data.message, "#D4E7C5", 2000);
            }

        } catch (error) {
            handleError(error, "Error al actualizar el Banner");
        } finally {
            fetch_loader_form.value = false;
            updating.value = false;
            BannerImage.$reset();

            form_update_banner.value.reset();
        }
    }
}

const deleteBanner = async () => {
    try {
        fetch_loader_form.value = true;
        const response = await axios.delete(route('banners.destroy', banner.value.id))

        if (response.status === 200) {
            banners.value = banners.value.filter(b => b.id !== banner.value.id)

            showAlert(true, response.data.message, "#D4E7C5", 2000)

            BannerImage.$reset();

            Updating.value = false;
        }

    } catch (error) {
        handleError(error, "Error al eliminar el Banner")
    } finally {
        fetch_loader_form.value = false;
        dialog_delete_banner.value = false;
    }
}

const Cancel = () => {
    BannerImage.$reset();

    updating.value = false;

    banner.value = {
        id: null,
        name: "",
        file_path: "",
        file: "",
        image: "",
        active: true,
        token: "",
    }
}

const alert = ref({
    status: false,
    message: "",
    color: "#D4E7C5",
    timeout: 1000,
    vertical: true,
    location: "right top",
    rounded: "md"
})

const showAlert = (status, message, color, timeout) => {
    alert.value.status = status
    alert.value.message = message
    alert.value.color = color
    alert.value.timeout = timeout
}

const handleError = (error, errorMessage) => {
    console.error(error);
    showAlert(true, errorMessage, "#F9D7DA", 3000);
};
</script>

<template>

    <Head title="Banners">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_delete_banner" max-width="450" persistent>
        <v-card max-width="450" title="Eliminar Banner" prepend-icon="mdi-delete">
            <v-card-text>
                ¿Estás seguro de eliminar el Banner <strong>{{ banner.name }}</strong>?
            </v-card-text>
            <v-card-actions class="justify-between" style="padding: 1rem 3rem;">
                <v-divider></v-divider>
                <v-btn @click="dialog_delete_banner = false">
                    Cancelar
                </v-btn>

                <v-btn color="error" @click="deleteBanner" :loading="fetch_loader_form">
                    Eliminar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <AuthenticatedLayout>
        <div class="py-6 h-full">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
                <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">

                    <section class="bg-gris-300 w-full grid place-items-center mt-4">
                        <div
                            class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg">
                            <SimpleHeader title="Gestiona tus Banners" breadcrumb="Banners"
                                :breadcrumb_link="route('banners.index')" />

                            <article class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 p-2 md:p-4 mt-4">
                                <v-card class="md:col-span-1 p-2 md:p-6 sm:order-last md:order-first max-h-fit"
                                    :loading="fetch_loader">

                                    <template v-slot:loader="{ isActive }">
                                        <v-progress-linear :active="isActive" color="primary" height="3" indeterminate>
                                        </v-progress-linear>
                                    </template>

                                    <template v-slot:title>
                                        <h2 class="mb-1 text-center p-2">
                                            Banners
                                        </h2>
                                    </template>

                                    <template v-slot:text v-if="!fetch_loader">
                                        <template v-if="banners.length > 0">
                                            <div class="grid grid-rows-auto md:grid-rows-auto gap-4 md:gap-6 pt-2">
                                                <template v-for="banner in banners" :key="banner.id">
                                                    <div class="rounded-lg cursor-pointer hover:opacity-80"
                                                        @click="EditBanner(banner)">
                                                        <!-- :src="route('getImage', {uri: banner.file_path+'/'+banner.file})" -->
                                                        <img :src="`/storage/${banner.file_path}/${banner.file}`"
                                                            alt="Banner"
                                                            class="w-full rounded-lg aspect-39/10 object-cover">
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="flex justify-center items-center h-96 text-underlined">
                                                No hay banners disponibles
                                            </div>
                                        </template>
                                    </template>

                                    <template v-slot:text v-else>
                                        <div class="flex justify-center items-center h-96">
                                            <v-progress-circular color="primary" indeterminate :size="35" :width="4">
                                                <!-- <template v-slot:default>
                                                    {{ value }} %
                                                </template> -->
                                            </v-progress-circular>
                                        </div>
                                    </template>
                                </v-card>

                                <v-card class="md:col-span-2 border-2 p-2 md:p-6 sm:order-first md:order-last"
                                    :loading="BannerImage.getLoading">

                                    <template v-slot:loader="{ isActive }">
                                        <v-progress-linear :active="isActive" color="primary" height="4"
                                            indeterminate></v-progress-linear>
                                    </template>

                                    <template v-slot:prepend>
                                        <v-icon class="mx-2" v-if="!updating">mdi-image-plus</v-icon>
                                        <v-icon class="mx-2" v-else>mdi-image-edit</v-icon>
                                    </template>

                                    <template v-slot:title>
                                        <h2 class="mb-1 p-2" v-if="!updating">
                                            Agregar Banner
                                        </h2>
                                        <h2 class="mb-1 p-2" v-else>
                                            Detalles del Banner
                                        </h2>
                                    </template>

                                    <template v-if="!updating">
                                        <v-form ref="form_create_banner" @submit.prevent="storeBanner">
                                            <v-card-text class="px-6">
                                                <div class="flex flex-col">

                                                    <DragAndDrop color_border="border-orange-400" />

                                                    <template v-if="BannerImage.getWorking">

                                                        <span class="flex items-center my-6">
                                                            <span class="h-px flex-1 bg-primary"></span>
                                                            <span class="shrink-0 px-6 text-primary">Detalles</span>
                                                            <span class="h-px flex-1 bg-primary"></span>
                                                        </span>

                                                        <v-text-field label="Nombre" placeholder="Nombre del Banner"
                                                            variant="outlined" density="comfortable"
                                                            hint="Ejemplo: Banner 1" class="text-[#212121]"
                                                            color="primary" v-model="banner.name"
                                                            :rules="[rules.required]">
                                                        </v-text-field>

                                                        <div class="flex flex-col md:mt-4 pl-2">
                                                            <h3 class="text-[1.1rem] text-overline">
                                                                Previsualización:
                                                            </h3>
                                                        </div>

                                                        <div
                                                            class="flex justify-content-center align-items-center sm:pb-2 md:pb-4 mt-4">
                                                            <v-img alt="Imagen cargada"
                                                                class="w-full rounded-lg aspect-39/10" cover
                                                                lazy-src="https://picsum.photos/500/300"
                                                                :src="(BannerImage.getLoading) ? '' : BannerImage.getUrl">
                                                                <template v-slot:placeholder>
                                                                    <div
                                                                        class="d-flex align-center justify-center fill-height">
                                                                        <v-progress-circular color="grey-lighten-4"
                                                                            indeterminate>
                                                                        </v-progress-circular>
                                                                    </div>
                                                                </template>
                                                            </v-img>
                                                        </div>

                                                    </template>
                                                </div>
                                            </v-card-text>

                                            <v-card-actions class="justify-between mx-36" style="padding: 2rem;">
                                                <template v-if="BannerImage.getWorking">
                                                    <v-btn type="button"
                                                        class="justify-center py-2 uppercase px-8 shadow-md text-sm font-semibold rounded-sm focus:outline-none transform transition-transform hover:scale-105"
                                                        @click="BannerImage.$reset" variant="tonal" color="grey">
                                                        Cancelar
                                                    </v-btn>
                                                    <v-btn type="submit" :loading="fetch_loader_form"
                                                        class="justify-center py-2 uppercase px-8 shadow-md text-sm font-semibold rounded-sm  focus:outline-none transform transition-transform hover:scale-105"
                                                        variant="tonal" color="primary">
                                                        Agregar
                                                    </v-btn>
                                                </template>
                                            </v-card-actions>
                                        </v-form>
                                    </template>

                                    <template v-else>
                                        <v-form ref="form_update_banner" @submit.prevent="updateBanner">
                                            <v-card-text class="px-6">
                                                <div class="flex flex-col">

                                                    <DragAndDrop color_border="border-orange-400" />

                                                    <span class="flex items-center my-6">
                                                        <span class="h-px flex-1 bg-primary"></span>
                                                        <span class="shrink-0 px-6 text-primary">Detalles</span>
                                                        <span class="h-px flex-1 bg-primary"></span>
                                                    </span>

                                                    <v-text-field label="Nombre" placeholder="Nombre del Banner"
                                                        variant="outlined" v-model="banner.name" density="comfortable"
                                                        hint="Ejemplo: Banner 1" class="text-[#212121]" color="primary"
                                                        :rules="[rules.required]">
                                                    </v-text-field>

                                                    <div class="flex flex-col md:mt-4">
                                                        <h3 class="text-[1rem] font-semibold text-gray-700">
                                                            Previsualización:</h3>
                                                    </div>

                                                    <div
                                                        class="flex justify-content-center align-items-center sm:pb-2 md:pb-4 mt-4">
                                                        <!-- : route('getImage', {uri: banner.file_path+'/'+banner.file})" -->
                                                        <v-img alt="Imagen cargada"
                                                            class="w-full rounded-lg aspect-39/10" cover
                                                            lazy-src="https://picsum.photos/500/300"
                                                            :src="(BannerImage.getUrl)
                                                                ? BannerImage.getUrl
                                                                : `/storage/${banner.file_path}/${banner.file}`">
                                                            <template v-slot:placeholder>
                                                                <div
                                                                    class="d-flex align-center justify-center fill-height">
                                                                    <v-progress-circular color="grey-lighten-4"
                                                                        indeterminate>
                                                                    </v-progress-circular>
                                                                </div>
                                                            </template>
                                                        </v-img>
                                                    </div>
                                                </div>
                                            </v-card-text>

                                            <v-card-actions class="justify-between" style="padding: 2rem;">
                                                <v-btn type="button"
                                                    class="justify-center py-2 uppercase px-8 shadow-md text-sm font-semibold rounded-sm focus:outline-none transform transition-transform hover:scale-105"
                                                    @click="Cancel" variant="tonal" color="grey">
                                                    Cancelar
                                                </v-btn>

                                                <div class="flex gap-4">
                                                    <v-btn type="submit"
                                                        class="justify-center py-2 uppercase px-8 shadow-md text-sm font-semibold rounded-sm  focus:outline-none transform transition-transform hover:scale-105"
                                                        variant="tonal" color="primary" :loading="fetch_loader_form">
                                                        Actualizar
                                                    </v-btn>
                                                    <v-btn type="button"
                                                        class="justify-center py-2 uppercase px-8 shadow-md text-sm font-semibold rounded-sm  focus:outline-none transform transition-transform hover:scale-105"
                                                        @click="Modal('delete_banner', banner)" variant="tonal"
                                                        color="red">
                                                        Eliminar
                                                    </v-btn>
                                                </div>
                                            </v-card-actions>
                                        </v-form>
                                    </template>
                                </v-card>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
