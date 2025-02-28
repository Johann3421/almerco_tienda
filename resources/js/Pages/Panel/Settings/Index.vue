<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SimpleHeader from '@/Components/SimpleHeader.vue';
import Notification from '@/Components/Notification.vue';
import { Link, Head } from "@inertiajs/vue3";
import Favicon from "@assets/img/favicon.png";
import { ref, onMounted } from "vue";
import useFormRules from "@utils/UseFormRules";
import { handleFileUpload, handleSubmit } from "@utils/FormUtils";

// Refs para los formularios y el estado
const fetch_loader_form = ref(false);

const tipo_de_cambio = ref({ id: 0, key: "", value: "", active: false, token: "" });
const img_superior = ref({ id: 0, key: "", value: "", active: false, imagen: null, imagen_url: null });
const img_superior_mobile = ref({ id: 0, key: "", value: "", active: false, imagen: null, imagen_url: null });
const img_medio = ref({ id: 0, key: "", value: "", active: false, imagen: null, imagen_url: null });
const img_medio_mobile = ref({ id: 0, key: "", value: "", active: false, imagen: null, imagen_url: null });

const alert = ref({ status: false, message: "", color: "#D4E7C5", timeout: 1000, vertical: true, location: "right top", rounded: "md" });

const dialog_tipo_de_cambio = ref(false);
const dialog_img_superior = ref(false);
const dialog_img_superior_mobile = ref(false);
const dialog_img_medio = ref(false);
const dialog_img_medio_mobile = ref(false);

const props = defineProps({
    tipo_de_cambio: { type: Object, required: true },
    img_superior: { type: Object, required: true },
    img_superior_mobile: { type: Object, required: true },
    img_medio: { type: Object, required: true },
    img_medio_mobile: { type: Object, required: true }
});

onMounted(() => {
    tipo_de_cambio.value = props.tipo_de_cambio
    img_superior.value = props.img_superior
    img_superior_mobile.value = props.img_superior_mobile
    img_medio.value = props.img_medio
    img_medio_mobile.value = props.img_medio_mobile
});

// Función genérica para actualizar formularios
const updateForm = async (formRef, url, data, successMessage, dialogRef) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        fetch_loader_form.value = true;
        try {
            const response = await handleSubmit(url, data);
            if (response.status === 200) {
                Object.assign(data, response.data.data);
                showAlert(true, successMessage, "#D4E7C5", 2000);
                // formRef.value.reset();
            }
        } catch (error) {
            handleError(error, "Error al actualizar los datos");
        } finally {
            fetch_loader_form.value = false;
            dialogRef.value = false;
        }
    }
};

const updateTipoDeCambio = () => updateForm(form_tipo_de_cambio, route("settings.storeTipoDeCambio"), tipo_de_cambio.value, "Tipo de cambio actualizado", dialog_tipo_de_cambio);
const updateImgSuperior = () => updateForm(form_img_superior, route("settings.storeImgSuperior"), img_superior.value, "Imagen superior actualizada", dialog_img_superior);
const updateImgSuperiorMobile = () => updateForm(form_img_superior_mobile, route("settings.storeImgSuperiorMobile"), img_superior_mobile.value, "Imagen superior mobile actualizada", dialog_img_superior_mobile);
const updateImgMedio = () => updateForm(form_img_medio, route("settings.storeImgMedio"), img_medio.value, "Imagen media actualizada", dialog_img_medio);
const updateImgMedioMobile = () => updateForm(form_img_medio_mobile, route("settings.storeImgMedioMobile"), img_medio_mobile.value, "Imagen media mobile actualizada", dialog_img_medio_mobile);

// Subida de imágenes
const uploadImagen = (event, imgData) => handleFileUpload(event, imgData, (errorMessage) => handleError(null, errorMessage));

const rules = useFormRules

// FormRefs
const form_tipo_de_cambio = ref(null);
const form_img_superior = ref(null);
const form_img_superior_mobile = ref(null);
const form_img_medio = ref(null);
const form_img_medio_mobile = ref(null);

// Modal de formularios
const Modal = (dialog) => {
    if (dialog === "tipo_de_cambio") dialog_tipo_de_cambio.value = true;
    if (dialog === "img_superior") dialog_img_superior.value = true;
    if (dialog === "img_superior_mobile") dialog_img_superior_mobile.value = true;
    if (dialog === "img_medio") dialog_img_medio.value = true;
    if (dialog === "img_medio_mobile") dialog_img_medio_mobile.value = true;
};

// Función para mostrar alertas
const showAlert = (status, message, color, timeout) => {
    alert.value = { ...alert.value, status, message, color, timeout };
};

// Manejo de errores
const handleError = (error, errorMessage) => {
    console.error(error);
    showAlert(true, errorMessage, "#F9D7DA", 3000);
};

const handleUpdate = () => {
    localStorage.clear();
};

</script>

<template>

    <Head title="Configuración del Sistema">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_tipo_de_cambio" width="500" persistent>
        <v-card max-width="500" title="Editar el Precio del Dólar" class="rounded-lg">
            <v-form ref="form_tipo_de_cambio" @submit.prevent="updateTipoDeCambio">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="tipo_de_cambio.key" label="Nombre (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: tipo_de_cambio" :rules="[rules.required]" disabled>
                    </v-text-field>

                    <v-text-field density="comfortable" v-model="tipo_de_cambio.value" label="Valor (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: 20.00" :rules="[rules.required]">
                    </v-text-field>

                    <v-checkbox v-model="tipo_de_cambio.active" label="Activo" color="primary"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_tipo_de_cambio = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form" @click="handleUpdate">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_img_superior" width="500" persistent>
        <v-card max-width="500" title="Editar Imagen de Anuncio Superior" class="rounded-lg">
            <v-form ref="form_img_superior" @submit.prevent="updateImgSuperior">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="img_superior.key" label="Nombre (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: img_superior" :rules="[rules.required]" disabled>
                    </v-text-field>

                    <v-text-field density="comfortable" v-model="img_superior.url" label="URL (*)" color="primary"
                        variant="outlined" required
                        hint="Ejemplo: https://grupoalmerco.com.pe/product/impresora-laser-xerox-phaser-7100vnp-color-30ppm-a3"
                        :rules="[rules.required]">
                    </v-text-field>

                    <!-- <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]"
                        @change="uploadImagenSuperior" @click:clear="img_superior.imagen_url = null">
                    </v-file-input> -->
                    <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]"
                        @change="uploadImagen($event, img_superior)" @click:clear="img_superior.imagen_url = null">
                    </v-file-input>

                    <v-img v-if="img_superior.imagen_url" :src="img_superior.imagen_url" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-37/6" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-img v-else-if="img_superior.file"
                        :src="`/storage/${img_superior.file_path}/${img_superior.file}`" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-37/6" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-img v-else
                        src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                        width="100%" cover class="mt-4 rounded-lg mb-4 aspect-37/6" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-checkbox v-model="img_superior.active" label="Activo" color="primary"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_img_superior = false; img_superior.imagen_url = null">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_img_superior_mobile" width="500" persistent>
        <v-card max-width="500" title="Editar Imagen de Anuncio Superior - Mobile" class="rounded-lg">
            <v-form ref="form_img_superior_mobile" @submit.prevent="updateImgSuperiorMobile">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="img_superior_mobile.key" label="Nombre (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: img_superior_mobile" :rules="[rules.required]" disabled>
                    </v-text-field>

                    <v-text-field density="comfortable" v-model="img_superior_mobile.url" label="URL (*)" color="primary"
                        variant="outlined" required
                        hint="Ejemplo: https://grupoalmerco.com.pe/product/impresora-laser-xerox-phaser-7100vnp-color-30ppm-a3"
                        :rules="[rules.required]">
                    </v-text-field>

                    <!-- <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]"
                        @change="uploadImagenSuperior" @click:clear="img_superior_mobile.imagen_url = null">
                    </v-file-input> -->
                    <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]"
                        @change="uploadImagen($event, img_superior_mobile)" @click:clear="img_superior_mobile.imagen_url = null">
                    </v-file-input>

                    <v-img v-if="img_superior_mobile.imagen_url" :src="img_superior_mobile.imagen_url" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-215/24" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-img v-else-if="img_superior_mobile.file"
                        :src="`/storage/${img_superior_mobile.file_path}/${img_superior_mobile.file}`" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-215/24" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-img v-else
                        src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                        width="100%" cover class="mt-4 rounded-lg mb-4 aspect-215/24" alt="Imagen de Anuncio Superior">
                    </v-img>

                    <v-checkbox v-model="img_superior_mobile.active" label="Activo" color="primary"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_img_superior_mobile = false; img_superior_mobile.imagen_url = null">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_img_medio" width="500" persistent>
        <v-card max-width="500" title="Editar Imagen de Anuncio Medio" class="rounded-lg">
            <v-form ref="form_img_medio" @submit.prevent="updateImgMedio">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="img_medio.key" label="Nombre (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: img_medio" :rules="[rules.required]" disabled>
                    </v-text-field>

                    <v-text-field density="comfortable" v-model="img_medio.url" label="URL (*)" color="primary"
                        variant="outlined" required
                        hint="Ejemplo: https://grupoalmerco.com.pe/product/impresora-laser-xerox-phaser-7100vnp-color-30ppm-a3"
                        :rules="[rules.required]">
                    </v-text-field>

                    <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]" @change="uploadImagen($event, img_medio)"
                        @click:clear="img_medio.imagen_url = null">
                    </v-file-input>

                    <v-img v-if="img_medio.imagen_url" :src="img_medio.imagen_url" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-39/10" alt="Imagen de Anuncio Medio">
                    </v-img>

                    <v-img v-else-if="img_medio.file" :src="`/storage/${img_medio.file_path}/${img_medio.file}`"
                        width="100%" cover class="mt-4 rounded-lg mb-4 aspect-39/10" alt="Imagen de Anuncio Medio">
                    </v-img>

                    <v-img v-else
                        src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                        width="100%" cover class="mt-4 rounded-lg mb-4 aspect-39/10" alt="Imagen de Anuncio Medio">
                    </v-img>

                    <v-checkbox v-model="img_medio.active" label="Activo" color="primary"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_img_medio = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_img_medio_mobile" width="500" persistent>
        <v-card max-width="500" title="Editar Imagen de Anuncio Medio - Mobile" class="rounded-lg">
            <v-form ref="form_img_medio_mobile" @submit.prevent="updateImgMedioMobile">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="img_medio_mobile.key" label="Nombre (*)" color="primary"
                        variant="outlined" required hint="Ejemplo: img_medio_mobile" :rules="[rules.required]" disabled>
                    </v-text-field>

                    <v-text-field density="comfortable" v-model="img_medio_mobile.url" label="URL (*)" color="primary"
                        variant="outlined" required
                        hint="Ejemplo: https://grupoalmerco.com.pe/product/impresora-laser-xerox-phaser-7100vnp-color-30ppm-a3"
                        :rules="[rules.required]">
                    </v-text-field>

                    <v-file-input label="Imagen (*)" color="primary" variant="outlined" required accept="image/*"
                        hint="Solo se permiten archivos de imagen" :rules="[rules.required]"
                        @change="uploadImagen($event, img_medio_mobile)" @click:clear="img_medio_mobile.imagen_url = null">
                    </v-file-input>

                    <v-img v-if="img_medio_mobile.imagen_url" :src="img_medio_mobile.imagen_url" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-16/9" alt="Imagen de Anuncio Medio - Mobile">
                    </v-img>

                    <v-img v-else-if="img_medio_mobile.file"
                        :src="`/storage/${img_medio_mobile.file_path}/${img_medio_mobile.file}`" width="100%" cover
                        class="mt-4 rounded-lg mb-4 aspect-16/9" alt="Imagen de Anuncio Medio - Mobile">
                    </v-img>

                    <v-img v-else
                        src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h
                        =750&dpr=1" width="100%" cover class="mt-4 rounded-lg mb-4 aspect-16/9" alt="Imagen de Anuncio Medio">
                    </v-img>

                    <v-checkbox v-model="img_medio_mobile.active" label="Activo" color="primary"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>

                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_img_medio_mobile = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>


    <AuthenticatedLayout>
        <div class="py-6 h-full">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
                <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">
                    <section class="bg-gris-300 w-full grid place-items-center">
                        <div
                            class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg">

                            <SimpleHeader title="Configuración del Sistema" breadcrumb="Configuración"
                                :breadcrumb_link="route('settings.index')" />

                            <article class="text-black px-4 py-8 mt-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    <div @click="Modal('tipo_de_cambio')"
                                        class="flex items-start gap-4 rounded-xl border p-8 shadow-xl transition hover:border-orange-500/10 hover:shadow-orange-500/10 cursor-pointer">
                                        <span class="shrink-0 rounded-lg bg-gray-700 p-2">
                                            <v-icon color="green" size="x-large">mdi-currency-usd</v-icon>
                                        </span>

                                        <div>
                                            <h2 class="text-lg font-bold"> Precio del Dólar</h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                Cambia el precio del dólar en tiempo real.
                                            </p>
                                        </div>
                                    </div>
                                    <div @click="Modal('img_superior')"
                                        class="flex items-start gap-4 rounded-xl border p-8 shadow-xl transition hover:border-orange-500/10 hover:shadow-orange-500/10 cursor-pointer">
                                        <span class="shrink-0 rounded-lg bg-gray-700 p-2">
                                            <v-icon color="orange" size="x-large">mdi-image-area</v-icon>
                                        </span>

                                        <div>
                                            <h2 class="text-lg font-bold"> Imagen de Anuncio Superior</h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                Cambia la imagen y el enlace de la parte superior.
                                            </p>
                                        </div>
                                    </div>
                                    <div @click="Modal('img_superior_mobile')"
                                        class="flex items-start gap-4 rounded-xl border p-8 shadow-xl transition hover:border-orange-500/10 hover:shadow-orange-500/10 cursor-pointer">
                                        <span class="shrink-0 rounded-lg bg-gray-700 p-2">
                                            <v-icon color="orange" size="x-large">mdi-image-area</v-icon>
                                        </span>

                                        <div>
                                            <h2 class="text-lg font-bold">Imagen de Anuncio Superior - Mobile</h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                Cambia la imagen y el enlace de la parte superior.
                                            </p>
                                        </div>
                                    </div>
                                    <div @click="Modal('img_medio')"
                                        class="flex items-start gap-4 rounded-xl border p-8 shadow-xl transition hover:border-orange-500/10 hover:shadow-orange-500/10 cursor-pointer">
                                        <span class="shrink-0 rounded-lg bg-gray-700 p-2">
                                            <v-icon color="orange" size="x-large">mdi-panorama-variant</v-icon>
                                        </span>

                                        <div>
                                            <h2 class="text-lg font-bold"> Imagen de Anuncio Medio</h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                Cambia la imagen y el enlace de la parte media.
                                            </p>
                                        </div>
                                    </div>
                                    <div @click="Modal('img_medio_mobile')"
                                        class="flex items-start gap-4 rounded-xl border p-8 shadow-xl transition hover:border-orange-500/10 hover:shadow-orange-500/10 cursor-pointer">
                                        <span class="shrink-0 rounded-lg bg-gray-700 p-2">
                                            <v-icon color="orange" size="x-large">mdi-panorama-variant</v-icon>
                                        </span>

                                        <div>
                                            <h2 class="text-lg font-bold"> Imagen de Anuncio Medio - Mobile</h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                Cambia la imagen y el enlace de la parte media.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
