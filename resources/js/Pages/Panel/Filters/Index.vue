<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SimpleHeader from '@/Components/SimpleHeader.vue';
import Notification from '@/Components/Notification.vue';
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import useFormRules from "@utils/UseFormRules";
import Favicon from "@assets/img/favicon.png";

const props = defineProps({
    filters: {
        type: Array,
        required: true
    }
})

const page = ref(1)

const page_item = ref(1)

const filters = ref([]);

const search = ref("")

const search_item = ref("")

const items_per_page = ref(10)

const items_per_page_item = ref(5)

const fetch_loader = ref(false)

const fetch_loader_form = ref(false)

const fetchFilters = async (criteria) => {

    fetch_loader.value = true;

    const url_base = window.location.href;

    const url = `${url_base}/buscar/${criteria}`

    try {
        const response = await axios.get(url)

        filters.value = response.data
    } catch (error) {
        console.error(error)
    } finally {
        fetch_loader.value = false;
    }
}

const headers = ref([])

const headers_items = ref([])

onMounted(async () => {
    headers.value = [
        { title: "# Filtro", key: "index", align: "center" },
        { title: "Nombre", key: "name", align: "center" },
        { title: "Activo", key: "active", align: "center" },
        { title: "Acciones", key: "actions", align: "center", sortable: false },
    ]

    headers_items.value = [
        { title: "# Opción", key: "index", align: "center" },
        { title: "Nombre", key: "name", align: "center" },
        { title: "Activo", key: "active", align: "center" },
        { title: "Acciones", key: "actions", align: "center", sortable: false },
    ]

    filters.value = props.filters;
})

const pageCount = computed(() => {
    return Math.ceil(filters.value.length / items_per_page.value);
})

const filter = ref({
    id: 0,
    name: "",
    active: true,
})

const filter_item = ref({
    id: 0,
    name: "",
    active: true,
    filter_id: null,
    file_path: null,
    file: null,
    image: null,
    image_url: null,
})

const rules = useFormRules

const dialog_create_filter = ref(false)

const dialog_create_filter_item = ref(false)

const dialog_update_filter = ref(false)

const dialog_update_filter_item = ref(false)

const dialog_delete_filter = ref(false)

const dialog_delete_filter_item = ref(false)

const form_create_filter = ref(null)

const form_update_filter = ref(null)

const form_create_filter_item = ref(null)

const form_update_filter_item = ref(null)

const Modal = (dialog, selected) => {

    filter.value = {
        id: 0,
        name: "",
        active: true,
    }

    filter_item.value = {
        id: 0,
        name: "",
        active: true,
        filter_id: null,
    }

    if (dialog === "create_filter") {
        dialog_create_filter.value = true
    } else if (dialog === "update_filter") {
        filter.value = { ...selected }
        dialog_update_filter.value = true
    } else if (dialog === "delete_filter") {
        filter.value = { ...selected }
        dialog_delete_filter.value = true
    } else if (dialog === "create_filter_item") {
        dialog_create_filter_item.value = true
    } else if (dialog === "update_filter_item") {
        filter_item.value = { ...selected }
        dialog_update_filter_item.value = true
    } else if (dialog === "delete_filter_item") {
        filter_item.value = { ...selected }
        dialog_delete_filter_item.value = true
    }
}

const storeFilter = async () => {

    const { valid } = await form_create_filter.value.validate()

    if (valid) {
        try {
            fetch_loader_form.value = true;
            const response = await axios.post(route('filters.store'), {
                name: filter.value.name,
                active: filter.value.active
            })

            if (response.status === 201) {
                filters.value.push(response.data.filter)

                showAlert(true, response.data.message, "#D4E7C5", 2000)
            }

        } catch (error) {

            handleError(error, "Error al guardar el filtro")

        } finally {
            fetch_loader_form.value = false;
            dialog_create_filter.value = false;

            await form_create_filter.value.reset()
        }
    }

}

const updateFilter = async () => {

    const { valid } = await form_update_filter.value.validate()

    if (valid) {
        try {
            fetch_loader_form.value = true;
            const response = await axios.patch(route('filters.update', filter.value.id), {
                name: filter.value.name,
                active: filter.value.active
            })

            if (response.status === 200) {
                const index = filters.value.findIndex(f => f.id === filter.value.id)

                filters.value[index] = response.data.filter

                showAlert(true, response.data.message, "#D4E7C5", 2000)
            }

        } catch (error) {
            handleError(error, "Error al actualizar el filtro")
        } finally {
            fetch_loader_form.value = false;
            dialog_update_filter.value = false;

            await form_update_filter.value.reset()
        }
    }

}

const deleteFilter = async () => {
    try {
        fetch_loader_form.value = true;
        const response = await axios.delete(route('filters.destroy', filter.value.id))

        if (response.status === 200) {
            filters.value = filters.value.filter(f => f.id !== filter.value.id)

            showAlert(true, response.data.message, "#D4E7C5", 2000)
        }

    } catch (error) {
        handleError(error, "Error al eliminar el filtro")
    } finally {
        fetch_loader_form.value = false;
        dialog_delete_filter.value = false;
    }
}

const storeFilterItem = async () => {

    const { valid } = await form_create_filter_item.value.validate()

    if (valid) {
        try {
            fetch_loader_form.value = true;

            const formData = new FormData();
            formData.append("name", filter_item.value.name);
            formData.append("active", filter_item.value.active);
            formData.append("filter_id", filter_item.value.filter_id);

            if (filter_item.value.image_url) formData.append("image", filter_item.value.image);

            const response = await axios.post(route('filters.items.store', {
                filtro: filter_item.value.filter_id
            }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })

            if (response.status === 201) {

                const index = filters.value.findIndex(f => f.id === filter_item.value.filter_id)

                filters.value[index] = response.data.filter

                showAlert(true, response.data.message, "#D4E7C5", 2000)
            }

        } catch (error) {

            handleError(error, "Error al guardar el item del filtro")

        } finally {
            fetch_loader_form.value = false;
            dialog_create_filter_item.value = false;

            await form_create_filter_item.value.reset()
        }
    }

}

const updateFilterItem = async () => {

    const { valid } = await form_update_filter_item.value.validate()

    if (valid) {
        try {
            fetch_loader_form.value = true;

            const formData = new FormData();
            formData.append("name", filter_item.value.name);
            formData.append("active", filter_item.value.active);
            formData.append("filter_id", filter_item.value.filter_id);

            if (filter_item.value.image_url) formData.append("image", filter_item.value.image);

            const response = await axios.post(route('filters.items.update', {
                filtro: filter_item.value.filter_id,
                item: filter_item.value.id
            }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PATCH'
                }
            })

            if (response.status === 200) {
                const index = filters.value.findIndex(f => f.id === filter_item.value.filter_id)

                filters.value[index] = response.data.filter

                showAlert(true, response.data.message, "#D4E7C5", 2000)
            }

        } catch (error) {
            handleError(error, "Error al actualizar el item del filtro")
        } finally {
            fetch_loader_form.value = false;
            dialog_update_filter_item.value = false;

            await form_update_filter_item.value.reset()
        }
    }

}

const deleteFilterItem = async () => {
    try {
        fetch_loader_form.value = true;
        const response = await axios.delete(route('filters.items.destroy', {
            filtro: filter_item.value.filter_id,
            item: filter_item.value.id
        }))

        if (response.status === 200) {
            const index = filters.value.findIndex(f => f.id === filter_item.value.filter_id)

            filters.value[index].filter_items = filters.value[index].filter_items.filter(fi => fi.id !== filter_item.value.id)

            showAlert(true, response.data.message, "#D4E7C5", 2000)
        }

    } catch (error) {
        handleError(error, "Error al eliminar el item del filtro")
    } finally {
        fetch_loader_form.value = false;
        dialog_delete_filter_item.value = false;
    }
}

const uploadImageFilterItem = async (event) => {
    const file = event.target.files[0];

    if (file.size > 1024 * 1024 * 10) {
        handleError(null, "La imagen no debe pesar más de 10MB");
        return;
    }

    if (file.type !== "image/jpeg" && file.type !== "image/png" && file.type !== "image/jpg" && file.type !== "image/webp" && file.type !== "image/svg+xml") {
        handleError(null, "La imagen debe ser de tipo jpg, jpeg, png, webp o svg");
        return;
    }

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        filter_item.value.image_url = reader.result;
    };

    filter_item.value.image = file;
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

    <Head title="Filtros">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_create_filter" width="500" persistent>
        <v-card max-width="500" title="Guardar Filtro" prepend-icon="mdi-filter">
            <v-form @submit.prevent="storeFilter" ref="form_create_filter" validate-on="submit lazy">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="filter.name" label="Nombre del Filtro (*)"
                        color="primary" variant="outlined" required hint="Ejemplo: Colores" :rules="[rules.required]">
                    </v-text-field>

                    <v-checkbox label="Activo" v-model="filter.active" color="primary" :rules="[rules.boolean]">

                    </v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_create_filter = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_update_filter" width="500" persistent>
        <v-card max-width="500" title="Actualizar Filtro" prepend-icon="mdi-filter">
            <v-form @submit.prevent="updateFilter" ref="form_update_filter" validate-on="submit lazy">
                <v-card-text class="px-8 py-8">
                    <v-text-field density="comfortable" v-model="filter.name" label="Nombre del Filtro (*)"
                        color="primary" variant="outlined" required :rules="[rules.required]">
                    </v-text-field>

                    <v-checkbox label="Activo" v-model="filter.active" color="primary" :rules="[rules.boolean]">

                    </v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_update_filter = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_delete_filter" width="500" persistent>
        <v-card max-width="500" title="Eliminar Filtro" prepend-icon="mdi-filter">
            <v-card-text class="px-8 py-8">
                ¿Estás seguro de eliminar el filtro <strong>{{ filter.name }}</strong>?
            </v-card-text>

            <v-card-actions>
                <v-divider></v-divider>
                <v-btn @click="dialog_delete_filter = false">
                    Cancelar
                </v-btn>

                <v-btn color="error" @click="deleteFilter" :loading="fetch_loader_form">
                    Eliminar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_create_filter_item" width="500" persistent>
        <v-card max-width="500" title="Guardar Opción del Filtro" prepend-icon="mdi-filter">
            <v-form @submit.prevent="storeFilterItem" ref="form_create_filter_item" validate-on="submit lazy">
                <v-card-text class="px-8 py-8">

                    <v-autocomplete :items="filters" color="primary" item-title="name" item-value="id"
                        label="Filtro (*)" chips v-model="filter_item.filter_id" variant="outlined"
                        :loading="fetch_loader" density="comfortable">
                        <template v-slot:chip="{ props, item }">
                            <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                        </template>

                        <template v-slot:item="{ props, item }">
                            <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                        </template>

                        <template v-slot:no-data>
                            <v-list-item>
                                <v-list-item-title>
                                    No hay filtros disponibles
                                </v-list-item-title>
                            </v-list-item>
                        </template>
                    </v-autocomplete>

                    <v-text-field density="comfortable" v-model="filter_item.name" label="Nombre del Item (*)"
                        color="primary" variant="outlined" required hint="Ejemplo: Rojo" :rules="[rules.required]">
                    </v-text-field>

                    <div class="flex items-center">
                        <div class="w-10/12">
                            <v-file-input label="Imagen del Item" color="primary" variant="outlined" accept="image/*"
                                hide-details density="comfortable" @change="uploadImageFilterItem"
                                @click:clear="filter_item.image_url = null">
                            </v-file-input>
                        </div>

                        <div class="w-2/12 ml-4">
                            <v-img v-if="filter_item.image_url" :src="filter_item.image_url" width="100%" cover
                                class="mt-4 rounded-lg mb-4 aspect-1/1" alt="Imagen de Anuncio Superior">
                            </v-img>

                            <v-img v-else
                                src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                width="100%" cover class="mt-4 rounded-lg mb-4 aspect-1/1"
                                alt="Imagen de Anuncio Superior">
                            </v-img>
                        </div>
                    </div>

                    <v-checkbox label="Activo" v-model="filter_item.active" color="primary"
                        :rules="[rules.boolean]"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_create_filter_item = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_update_filter_item" width="500" persistent>
        <v-card max-width="500" title="Actualizar Opción del Filtro" prepend-icon="mdi-filter">
            <v-form @submit.prevent="updateFilterItem" ref="form_update_filter_item" validate-on="submit lazy">
                <v-card-text class="px-8 py-8">

                    <v-autocomplete :items="filters" color="primary" item-title="name" item-value="id"
                        label="Filtro (*)" chips v-model="filter_item.filter_id" variant="outlined"
                        :loading="fetch_loader" disabled>
                        <template v-slot:chip="{ props, item }">
                            <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                        </template>

                        <template v-slot:item="{ props, item }">
                            <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                        </template>

                        <template v-slot:no-data>
                            <v-list-item>
                                <v-list-item-title>
                                    No hay filtros disponibles
                                </v-list-item-title>
                            </v-list-item>
                        </template>
                    </v-autocomplete>

                    <v-text-field density="comfortable" v-model="filter_item.name" label="Nombre del Item (*)"
                        color="primary" variant="outlined" required :rules="[rules.required]">
                    </v-text-field>

                    <div class="flex items-center">
                        <div class="w-10/12">
                            <v-file-input label="Imagen del Item" color="primary" variant="outlined" accept="image/*"
                                hide-details density="comfortable" @change="uploadImageFilterItem"
                                @click:clear="filter_item.image_url = null">
                            </v-file-input>
                        </div>

                        <div class="w-2/12 ml-4">
                            <v-img v-if="filter_item.image_url" :src="filter_item.image_url" width="100%" cover
                                class="mt-4 rounded-lg mb-4 aspect-1/1" alt="Imagen de Anuncio Superior">
                            </v-img>

                            <v-img v-else-if="filter_item.file"
                                :src="`/storage/${filter_item.file_path}/${filter_item.file}`"
                                width="100%" cover class="mt-4 rounded-lg mb-4 aspect-1/1"
                                alt="Imagen de Anuncio Superior">
                            </v-img>

                            <v-img v-else
                                src="https://images.pexels.com/photos/22710826/pexels-photo-22710826/free-photo-of-amor-cafe-taza-copa.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                width="100%" cover class="mt-4 rounded-lg mb-4 aspect-1/1"
                                alt="Imagen de Anuncio Superior">
                            </v-img>
                        </div>
                    </div>

                    <v-checkbox label="Activo" v-model="filter_item.active" color="primary"
                        :rules="[rules.boolean]"></v-checkbox>

                    <small class="text-caption text-medium-emphasis">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_update_filter_item = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetch_loader_form">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_delete_filter_item" width="500" persistent>
        <v-card max-width="500" title="Eliminar Opción del Filtro" prepend-icon="mdi-filter">
            <v-card-text class="px-8 py-8">
                ¿Estás seguro de eliminar la opción <strong>{{ filter_item.name }}</strong>?
            </v-card-text>

            <v-card-actions>
                <v-divider></v-divider>
                <v-btn @click="dialog_delete_filter_item = false">
                    Cancelar
                </v-btn>

                <v-btn color="error" @click="deleteFilterItem" :loading="fetch_loader_form">
                    Eliminar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <AuthenticatedLayout>
        <div class="py-6 h-full">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
                <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">
                    <section class="bg-gris-300 w-full grid place-items-center">
                        <div
                            class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg">
                            <SimpleHeader title="Gestiona tus Filtros" breadcrumb="Filtros"
                                :breadcrumb_link="route('filters.index')" />

                            <article class="mt-4">
                                <v-card variant="flat">
                                    <template v-slot:title>
                                        <v-row align="center" justify="space-between" density="comfortable" class="p-2">
                                            <v-col cols="12" md="8">
                                                <v-sheet>
                                                    <h2 class="text-lg md:text-xl lg:text-xl text-gray-900">Filtros</h2>
                                                </v-sheet>
                                            </v-col>
                                            <v-col cols="12" md="2">
                                                <v-sheet>
                                                    <v-btn color="primary" @click="Modal('create_filter')"
                                                        variant="tonal">
                                                        <v-icon left>mdi-plus</v-icon>
                                                        Agregar Filtro
                                                    </v-btn>
                                                </v-sheet>
                                            </v-col>
                                            <v-col cols="12" md="2">
                                                <v-sheet>
                                                    <v-btn color="primary" @click="Modal('create_filter_item')"
                                                        variant="tonal">
                                                        <v-icon left>mdi-plus</v-icon>
                                                        Agregar Opción
                                                    </v-btn>
                                                </v-sheet>
                                            </v-col>
                                        </v-row>
                                    </template>

                                    <template v-slot:text>
                                        <v-text-field v-model="search" label="Buscar Filtro"
                                            prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
                                            density="comfortable" color="primary">
                                        </v-text-field>

                                        <v-data-table :headers="headers" :items="filters" :search="search" show-expand
                                            no-data-text="Actualmente no hay filtros disponibles" v-model:page="page"
                                            items-per-page-text="Filtros por página" :loading="fetch_loader"
                                            loading-text="Cargando filtros..." :items-per-page="items_per_page">

                                            <template v-slot:bottom>
                                                <div class="text-center pt-2">
                                                    <v-pagination v-model="page" :length="pageCount"></v-pagination>
                                                </div>
                                            </template>

                                            <template v-slot:item.index="{ index }">
                                                {{ index + 1 }}
                                            </template>

                                            <template v-slot:loader="{ isActive }">
                                                <v-progress-linear :active="isActive" color="primary" height="3"
                                                    indeterminate>
                                                </v-progress-linear>
                                            </template>

                                            <template v-slot:item.active="{ item }">
                                                <v-chip color="success" v-if="item.active">
                                                    Activo
                                                </v-chip>
                                                <v-chip color="error" v-else>
                                                    Inactivo
                                                </v-chip>
                                            </template>

                                            <template v-slot:item.actions="{ item }">
                                                <v-icon class="cursor-pointer" color="primary"
                                                    style="margin-right: .5rem;" @click="Modal('update_filter', item)">
                                                    mdi-pencil
                                                </v-icon>
                                                <template v-if="item.filter_items && item.filter_items.length === 0">
                                                    <v-icon class="cursor-pointer" color="primary"
                                                        style="margin-left: .5rem;"
                                                        @click="Modal('delete_filter', item)">
                                                        mdi-delete
                                                    </v-icon>
                                                </template>
                                            </template>

                                            <template v-slot:expanded-row="{ columns, item }">
                                                <tr>
                                                    <td :colspan="columns.length" style="padding: 1rem 5rem 1rem 5rem;">

                                                        <v-text-field v-model="search_item" label="Buscar Item"
                                                            prepend-inner-icon="mdi-magnify" variant="outlined"
                                                            hide-details density="comfortable" color="primary"
                                                            style="margin: 1rem 0rem 0 0rem;">
                                                        </v-text-field>

                                                        <v-data-table :headers="headers_items"
                                                            :items="item.filter_items"
                                                            no-data-text="Actualmente no hay items del filtro"
                                                            v-model:page="page_item" :loading="fetch_loader"
                                                            loading-text="Cargando items del filtro..."
                                                            :search="search_item" :items-per-page="items_per_page_item">

                                                            <template v-slot:loader="{ isActive }">
                                                                <v-progress-linear :active="isActive" color="primary"
                                                                    height="3" indeterminate>
                                                                </v-progress-linear>
                                                            </template>

                                                            <template v-slot:item.index="{ index }">
                                                                {{ index + 1 }}
                                                            </template>

                                                            <template v-slot:item.active="{ item }">
                                                                <v-chip color="success" v-if="item.active">
                                                                    Activo
                                                                </v-chip>
                                                                <v-chip color="error" v-else>
                                                                    Inactivo
                                                                </v-chip>
                                                            </template>

                                                            <template v-slot:item.actions="{ item }">
                                                                <v-icon class="cursor-pointer" color="primary"
                                                                    style="margin-right: .5rem;"
                                                                    @click="Modal('update_filter_item', item)">
                                                                    mdi-pencil
                                                                </v-icon>
                                                                <v-icon class="cursor-pointer" color="primary"
                                                                    style="margin-left: .5rem;"
                                                                    @click="Modal('delete_filter_item', item)">
                                                                    mdi-delete
                                                                </v-icon>
                                                            </template>

                                                        </v-data-table>
                                                    </td>
                                                </tr>
                                            </template>
                                        </v-data-table>
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
