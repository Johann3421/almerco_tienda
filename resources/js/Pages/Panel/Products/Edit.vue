<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Notification from '@/Components/Notification.vue';
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from 'vue';
import useFormRules from "@utils/UseFormRules";
import Favicon from "@assets/img/favicon.png";
import DoubleHeader from '@/Components/DoubleHeader.vue';
import * as XLSX from 'xlsx';

const props = defineProps({
    product: Object,
    categories: Array,
    filters: Array,
});

const importFromExcel = (event) => {
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                // Asumimos que el primer sheet es el que contiene las especificaciones
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const excelData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                // Validar si hay datos suficientes para procesar
                if (!excelData || excelData.length < 2) {
                    throw new Error("El archivo Excel no contiene datos suficientes.");
                }

                // Asumimos que las columnas 'property' y 'description' están en las columnas 0 y 1
                const newSpecifications = excelData.slice(1).map(row => ({
                    key: row[0],
                    value: row[1]
                }));

                // Concatenar las especificaciones importadas con las existentes
                product.value.specifications = product.value.specifications.concat(newSpecifications);
            } catch (error) {
                // Manejo de errores y mostrar mensaje al usuario
                console.error("Error al procesar el archivo Excel:", error.message);
                handleError("Error al importar desde Excel", error.message);
            }
        };
        reader.readAsArrayBuffer(file);
    } else {
        handleError("Error", "No se seleccionó ningún archivo.");
    }
};

const closeSpecificationsDialog = () => {

    product.value.specifications = product.value.specifications.filter(
        spec => spec.key !== '' && spec.value !== ''
    );

    dialog_add_specifications.value = false
}

const addSpecification = () => {
    product.value.specifications.push({ key: '', value: '' });
}

const removeSpecification = (index) => {
    product.value.specifications.splice(index, 1);
}

const submitSpecifications = () => {
    const allValid = product.value.specifications.every(
      spec => spec.key !== '' && spec.value !== ''
    );

    if (!allValid) {
        handleError(null, "Debes completar todas los campos de las especificaciones");
        return;
    }

    dialog_add_specifications.value = false;
}

const dialog_add_specifications = ref(false);

const fetch_loader = ref(false);

const fetch_loader_form = ref(false);

const product = ref({
    id: 0,
    sku: '',
    part_number: '',
    description: '',
    name: '',
    slug: '',
    valoration: 0,
    price: null,
    stock: null,
    discount: 0,
    previous_price: 0,
    on_sale: false,
    on_promotion: false,
    active: true,
    token: '',
    manufacturer_url: '',
    image1: null,
    image1_id: null,
    image2: null,
    image2_id: null,
    image3: null,
    image3_id: null,
    image4: null,
    image4_id: null,
    imageUrl1: null,
    imageUrl2: null,
    imageUrl3: null,
    imageUrl4: null,
    subgroups: [],
    filters: [],
    images: [],
    specifications: [],
});

const rules = useFormRules

const form_update_product = ref(null)

const uploadImage = (e, imageIndex) => {
    const file = e.target.files[0];

    if (file.size > 1024 * 1024 * 10) {
        handleError(null, "La imagen no debe pesar más de 10MB");
        return;
    }

    if (file.type !== "image/jpeg" && file.type !== "image/png") {
        handleError(null, "La imagen debe ser de tipo jpg o png");
        return;
    }

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        product.value[`imageUrl${imageIndex}`] = reader.result;
    };

    product.value[`image${imageIndex}`] = file;
}

const updateProduct = async () => {
    const { valid } = await form_update_product.value.validate();

    if (valid) {
        fetch_loader_form.value = true;

        const response = await fetch(route('products.update', product.value.id), {
            method: 'POST',
            headers: {
                'X-HTTP-Method-Override': 'PATCH', // Para sobreescribir el método HTTP
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                sku: product.value.sku,
                part_number: product.value.part_number,
                description: product.value.description,
                name: product.value.name,
                slug: product.value.slug,
                price: product.value.price,
                stock: product.value.stock,
                discount: product.value.discount,
                previous_price: product.value.previous_price,
                on_sale: product.value.on_sale,
                on_promotion: product.value.on_promotion,
                manufacturer_url: product.value.manufacturer_url,
                active: product.value.active,
                specifications: product.value.specifications,
                filters: product.value.filters,
                subgroups: product.value.subgroups,
            }),
        });

        try {
            const data = await response.json();

            if (response.ok) {

                if (data.productId) {
                    const id = data.productId;

                    if (product.value.image1) {
                        const successful = await uploadImageChunk(product.value.image1, id, product.value.image1_id, 'image1');

                        if (successful) {
                            showAlert(true, "Imagen 1 subida correctamente", "#D4E7C5", 3000);
                        } else {
                            throw new Error("Error al subir la imagen 1");
                        }
                    }

                    if (product.value.image2) {
                        const successful = await uploadImageChunk(product.value.image2, id, product.value.image2_id, 'image2');

                        if (successful) {
                            showAlert(true, "Imagen 2 subida correctamente", "#D4E7C5", 3000);
                        } else {
                            throw new Error("Error al subir la imagen 2");
                        }
                    }

                    if (product.value.image3) {
                        const successful = await uploadImageChunk(product.value.image3, id, product.value.image3_id, 'image3');

                        if (successful) {
                            showAlert(true, "Imagen 3 subida correctamente", "#D4E7C5", 3000);
                        } else {
                            throw new Error("Error al subir la imagen 3");
                        }
                    }

                    if (product.value.image4) {
                        const successful = await uploadImageChunk(product.value.image4, id, product.value.image4_id, 'image4');

                        if (successful) {
                            showAlert(true, "Imagen 4 subida correctamente", "#D4E7C5", 3000);
                        } else {
                            throw new Error("Error al subir la imagen 4");
                        }
                    }
                }

                showAlert(true, data.message, "#D4E7C5", 3000);

                window.location.href = route('products.index');
            } else if (response.status === 422) {
                const errors = Object.values(data.errors).flat();

                errors.forEach(error => {
                    handleError(null, error);
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            if (error.message) {
                handleError(error, error.message);
            } else {
                handleError(error, "Error al actualizar el producto");
            }
        } finally {
            fetch_loader_form.value = false;
        }
    }
}

const uploadImageChunk = async (imageFile, productId, oldImageId, imageField) => {
    const chunkSize = 1024 * 1024 * 1;
    const totalChunks = Math.ceil(imageFile.size / chunkSize);
    const chunkId = crypto.randomUUID();
    const fileExtension = imageFile.name.split('.').pop();

    let start = 0;
    let chunkIndex = 1;

    try {
        while (start < imageFile.size) {
            const formData = new FormData();
            const chunk = imageFile.slice(start, start + chunkSize);

            formData.append('chunk', chunk);
            formData.append('field', imageField);
            formData.append('product_id', productId);
            if (oldImageId) formData.append('old_image_id', oldImageId);
            formData.append('chunk_id', chunkId);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            formData.append('file_extension', fileExtension);

            const response = await fetch(route('products.uploadImageChunk', productId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Error al subir la imagen: ${imageField}`);
            }

            start += chunkSize;
            chunkIndex++;
        }

        return true;
    } catch (error) {
        handleError(null, `Error al subir la imagen: ${imageField}`);
        return false;
    }
};

const genereateSlug = () => {
    product.value.slug = product.value.name
        .toLowerCase()
        .replace(/ /g, "-")
        .replace(/á/g, "a")
        .replace(/é/g, "e")
        .replace(/í/g, "i")
        .replace(/ó/g, "o")
        .replace(/ú/g, "u")
        .replace(/ñ/g, "n")
        .replace(/[^a-z0-9-]/g, "");
}

onMounted(() => {
    product.value.id = props.product.id;
    product.value.sku = props.product.sku;

    if (product.value.sku === null) {
        const paddedId = String(product.value.id + 1).padStart(6, '0');
        product.value.sku = `SKU-${paddedId}`;
    }

    product.value.part_number = props.product.part_number;
    product.value.description = props.product.description;
    product.value.name = props.product.name;
    product.value.slug = props.product.slug;
    product.value.price = parseFloat(props.product.price);
    product.value.stock = parseFloat(props.product.stock);
    product.value.discount = parseFloat(props.product.discount);
    product.value.previous_price = parseFloat(props.product.previous_price);
    product.value.manufacturer_url = props.product.manufacturer_url;

    product.value.on_sale = props.product.on_sale;
    product.value.on_promotion = props.product.on_promotion;
    product.value.active = props.product.active;

    product.value.subgroups = props.product.subgroups.map(subgroup => subgroup.id);
    product.value.filters = props.product.filters_items.map(filter => filter.id);
    product.value.specifications = props.product.specifications;

    product.value.images = props.product.images;

    if (product.value.images.length > 0) {
        product.value.image1_id = product.value.images[0].id;
    }

    if (product.value.images.length > 1) {
        product.value.image2_id = product.value.images[1].id;
    }

    if (product.value.images.length > 2) {
        product.value.image3_id = product.value.images[2].id;
    }

    if (product.value.images.length > 3) {
        product.value.image4_id = product.value.images[3].id;
    }
});

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

    <Head :title="`${product.name}`">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_add_specifications" max-width="900" persistent>
        <v-card prepend-icon="mdi-information" title="Especificaciones del Producto">

            <template v-slot:append>
                <v-btn icon @click="addSpecification" color="#1e88e5">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </template>

            <v-card-text class="mt-4">
                <v-row v-for="(spec, index) in product.specifications" dense class="text-center mb-3" :key="index">
                    <v-col cols="12" md="3" sm="3">
                      <v-text-field
                        v-model="spec.key"
                        label="Propiedad (*)"
                        type="text"
                        density="comfortable"
                        color="#1e88e5"
                        variant="outlined"
                        counter
                        persistent-hint
                        hint="Ejemplo: Color, Tamaño, Peso, etc."
                        :rules="[rules.required]"
                        required
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="7" sm="7">
                      <v-text-field
                        v-model="spec.value"
                        label="Descripción (*)"
                        type="text"
                        density="comfortable"
                        color="#1e88e5"
                        variant="outlined"
                        counter
                        persistent-hint
                        hint="Ejemplo: Rojo, 10x10, 1kg, etc."
                        :rules="[rules.required]"
                        required
                      ></v-text-field>
                    </v-col>

                    <!-- Botón de eliminar fila -->
                    <v-col cols="12" md="2" sm="2">
                      <v-btn icon @click="removeSpecification(index)" color="red">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </v-col>
                </v-row>

                <small class="text-caption text-medium-emphasis">(*) indica campo obligatorio</small>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn color="green" append-icon="mdi-file-excel">
                    <input type="file" @change="importFromExcel" accept=".xlsx, .xls" class="hidden" id="file">
                    <label for="file" class="cursor-pointer">Importar desde Excel</label>
                </v-btn>

                <v-spacer></v-spacer>
                <v-btn @click="closeSpecificationsDialog">
                    Cancelar
                </v-btn>

                <v-btn color="#1e88e5" type="submit" :loading="fetch_loader" :disabled="product.specifications.length === 0"
                @click="submitSpecifications">
                    Agregar Especificaciones
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

                            <DoubleHeader :title="product.name" breadcrumb="Productos"
                                :breadcrumb_link="route('products.index')" :breadcrumb_2="product.name"
                                :breadcrumb_link_2="route('products.edit', product.id)" />

                            <article class="mt-4">
                                <v-form class="flex flex-wrap" @submit.prevent="updateProduct" ref="form_update_product"
                                    validate-on="submit lazy">
                                    <div class="w-full md:w-2/3 pr-2">
                                        <v-card title="Información Básica del Producto" variant="text"
                                            prepend-icon="mdi-package-variant-plus">
                                            <v-card-text>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12" md="6">
                                                            <v-text-field label="Nombre (*)" v-model="product.name"
                                                                hint="Introduce el nombre del producto" required
                                                                type="text" color="#1e88e5" variant="outlined"
                                                                density="comfortable" :rules="[rules.required]"
                                                                @input="genereateSlug">
                                                            </v-text-field>
                                                        </v-col>

                                                        <v-col cols="12" md="6">
                                                            <v-text-field label="Slug (*)" type="text"
                                                                v-model="product.slug"
                                                                hint="Introduce el slug del producto" required
                                                                color="#1e88e5" variant="outlined" density="comfortable"
                                                                :rules="[rules.required, rules.slug]">
                                                            </v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-row>
                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="SKU (*)" type="text"
                                                                v-model="product.sku" disabled
                                                                hint="Introduce el SKU del producto" required
                                                                color="#1e88e5" variant="outlined" density="comfortable"
                                                                :rules="[rules.required]">
                                                            </v-text-field>
                                                        </v-col>

                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="Número de Parte (*)" type="text"
                                                                v-model="product.part_number"
                                                                hint="Introduce el número de parte del producto"
                                                                required color="#1e88e5" variant="outlined"
                                                                density="comfortable" :rules="[rules.required]">
                                                            </v-text-field>
                                                        </v-col>

                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="Stock (*)" type="number"
                                                                v-model="product.stock"
                                                                hint="Introduce el stock del producto" required
                                                                color="#1e88e5" variant="outlined" density="comfortable"
                                                                :rules="[rules.number]">
                                                            </v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-row>
                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="Precio (*)" type="number"
                                                                v-model="product.price"
                                                                hint="Introduce el precio del producto" required
                                                                color="#1e88e5" variant="outlined" density="comfortable"
                                                                :rules="[rules.required, rules.decimal]">
                                                            </v-text-field>
                                                        </v-col>

                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="Precio Anterior" type="number"
                                                                v-model="product.previous_price"
                                                                hint="Introduce el precio anterior del producto"
                                                                color="#1e88e5" variant="outlined" density="comfortable"
                                                                :rules="[rules.decimal]">
                                                            </v-text-field>
                                                        </v-col>

                                                        <v-col cols="12" md="4">
                                                            <v-text-field label="Descuento" type="number"
                                                                v-model="product.discount" density="comfortable"
                                                                hint="Introduce el descuento del producto"
                                                                color="#1e88e5" variant="outlined"
                                                                :rules="[rules.number]">
                                                            </v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-row>
                                                        <v-col cols="12" md="12">
                                                            <v-textarea label="Descripción (*)" density="comfortable"
                                                                type="text" v-model="product.description"
                                                                hint="Introduce la descripción del producto" required
                                                                variant="outlined" color="#1e88e5" auto-grow counter
                                                                :rules="[rules.required]">
                                                            </v-textarea>
                                                        </v-col>
                                                    </v-row>
                                                    <v-row>
                                                        <v-col cols="12" md="12">
                                                            <v-text-field label="URL del Fabricante" type="text"
                                                                density="comfortable" v-model="product.manufacturer_url"
                                                                color="#1e88e5" variant="outlined"
                                                                hint="Introduce la URL del fabricante"
                                                                :rules="[rules.url]"
                                                                >
                                                            </v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-row>
                                                        <v-btn type="text" color="#1e88e5" variant="tonal"
                                                            density="default" class="w-100 transform hover:scale-110"
                                                            @click.prevent="dialog_add_specifications = true; if (product.specifications.length === 0) addSpecification()">
                                                            Ver especificaciones del producto
                                                        </v-btn>
                                                    </v-row>
                                                    <span
                                                        class="block w-full text-caption text-medium-emphasis text-right text-[#1e88e5] mt-4 px-4">
                                                        (*) Campos obligatorios
                                                    </span>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                        <v-card title="Imagenes del Producto" prepend-icon="mdi-image-plus"
                                            variant="text">
                                            <v-card-text>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12" md="3">
                                                            <v-file-input v-model="product.image1" accept="image/*"
                                                                variant="outlined" label="Imagen 1 (*)"
                                                                @change="uploadImage($event, 1)" density="comfortable"
                                                                color="#1e88e5" hide-details required>
                                                            </v-file-input>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <v-file-input v-model="product.image2" accept="image/*"
                                                                variant="outlined" label="Imagen 2 (*)"
                                                                @change="uploadImage($event, 2)" density="comfortable"
                                                                hide-details color="#1e88e5" required>
                                                            </v-file-input>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <v-file-input v-model="product.image3" accept="image/*"
                                                                variant="outlined" label="Imagen 3 (*)"
                                                                @change="uploadImage($event, 3)" density="comfortable"
                                                                hide-details color="#1e88e5" required>
                                                            </v-file-input>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <v-file-input v-model="product.image4" accept="image/*"
                                                                variant="outlined" label="Imagen 4 (*)"
                                                                @change="uploadImage($event, 4)" density="comfortable"
                                                                hide-details color="#1e88e5" required>
                                                            </v-file-input>
                                                        </v-col>
                                                    </v-row>

                                                    <v-row>
                                                        <v-col cols="12" md="3">
                                                            <img :src="product.imageUrl1
                                                                ? product.imageUrl1
                                                                : (product.images.length > 0)
                                                                    ? `/storage/${product.images[0].file_path}/${product.images[0].file}`
                                                                    : 'https://images.pexels.com/photos/205507/pexels-photo-205507.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'"
                                                                class="w-full rounded-lg object-cover aspect-1/1"
                                                                alt="Imagen 1">
                                                            </img>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <img :src="product.imageUrl2
                                                                ? product.imageUrl2
                                                                : (product.images.length > 1)
                                                                    ? `/storage/${product.images[1].file_path}/${product.images[1].file}`
                                                                    : 'https://images.pexels.com/photos/10782402/pexels-photo-10782402.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'"
                                                                class="w-full rounded-lg object-cover aspect-1/1"
                                                                alt="Imagen 2">
                                                            </img>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <img :src="product.imageUrl3
                                                                ? product.imageUrl3
                                                                : (product.images.length > 2)
                                                                    ? `/storage/${product.images[2].file_path}/${product.images[2].file}`
                                                                    : 'https://images.pexels.com/photos/7531569/pexels-photo-7531569.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'"
                                                                class="w-full rounded-lg object-cover aspect-1/1"
                                                                alt="Imagen 3">
                                                            </img>
                                                        </v-col>

                                                        <v-col cols="12" md="3">
                                                            <img :src="product.imageUrl4
                                                                ? product.imageUrl4
                                                                : (product.images.length > 3)
                                                                    ? `/storage/${product.images[3].file_path}/${product.images[3].file}`
                                                                    : 'https://images.pexels.com/photos/7531569/pexels-photo-7531569.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'"
                                                                class="w-full rounded-lg object-cover aspect-1/1"
                                                                alt="Imagen 4">
                                                            </img>
                                                        </v-col>
                                                    </v-row>

                                                    <span
                                                        class="block w-full text-caption text-medium-emphasis text-right text-[#1e88e5] mt-8 px-4">
                                                        (*) Campos obligatorios
                                                    </span>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                    </div>
                                    <div class="w-full md:w-1/3 pl-2">
                                        <v-card title="Características del Producto" prepend-icon="mdi-cog"
                                            variant="text">
                                            <v-card-text>
                                                <v-card class="mb-4" title="Disponibilidad" variant="text"
                                                    prepend-icon="mdi-store">
                                                    <v-card-text>
                                                        <v-row>
                                                            <v-col cols="12" md="12">
                                                                <v-sheet
                                                                    class="d-flex flex-row justify-space-between align-middle px-4">
                                                                    <div class="align-self-center pa-2 cursor-pointer text-overline"
                                                                        @click="product.on_promotion = !product.on_promotion">
                                                                        En Promoción
                                                                    </div>
                                                                    <div class="align-self-center pa-2">
                                                                        <v-switch hide-details
                                                                            v-model="product.on_promotion"
                                                                            color="#1e88e5">
                                                                        </v-switch>
                                                                    </div>
                                                                </v-sheet>
                                                            </v-col>
                                                            <v-col cols="12" md="12">
                                                                <v-sheet
                                                                    class="d-flex flex-row justify-space-between align-middle px-4">
                                                                    <div class="align-self-center pa-2 cursor-pointer text-overline"
                                                                        @click="product.on_sale = !product.on_sale">
                                                                        En Tienda
                                                                    </div>
                                                                    <div class="align-self-center pa-2">
                                                                        <v-switch hide-details v-model="product.on_sale"
                                                                            color="#1e88e5">
                                                                        </v-switch>
                                                                    </div>
                                                                </v-sheet>
                                                            </v-col>
                                                        </v-row>
                                                    </v-card-text>
                                                </v-card>

                                                <v-card class="mb-4" title="Sub Grupo" variant="text"
                                                    prepend-icon="mdi-tag">
                                                    <v-card-text>
                                                        <v-row>
                                                            <v-col cols="12" md="12">
                                                                <v-sheet class="pt-10 px-4">
                                                                    <v-autocomplete v-model="product.subgroups"
                                                                        :items="categories" label="Sub Grupo (*)"
                                                                        item-title="name" item-value="id" chips multiple
                                                                        no-data-text="No se encontraron sub grupos"
                                                                        closable-chips color="#1e88e5"
                                                                        variant="outlined" density="comfortable"
                                                                        :rules="[rules.required, rules.array, rules.arrayNotEmpty]">

                                                                        <template v-slot:chip="{ props, item }">
                                                                            <v-chip v-bind="props" :text="item.raw.name"
                                                                                color="#1e88e5"></v-chip>
                                                                        </template>

                                                                        <template v-slot:item="{ props, item }">
                                                                            <v-list-item v-bind="props"
                                                                                :title="item.raw.name">
                                                                                <template v-slot:subtitle>
                                                                                    <span
                                                                                        class="text-overline text-[#002528]">
                                                                                        {{ item.raw.group.category.name
                                                                                        }}
                                                                                    </span>
                                                                                    <span>
                                                                                        <v-icon class="text-[#006d78]"
                                                                                            color="#006d78">mdi-chevron-right</v-icon>
                                                                                    </span>
                                                                                    <span
                                                                                        class="text-overline text-[#855e35]">
                                                                                        {{ item.raw.group.name }}
                                                                                    </span>
                                                                                </template>
                                                                            </v-list-item>
                                                                        </template>

                                                                    </v-autocomplete>
                                                                </v-sheet>
                                                            </v-col>
                                                        </v-row>
                                                    </v-card-text>
                                                </v-card>

                                                <v-card class="mb-4" title="Filtros" variant="text"
                                                    prepend-icon="mdi-tag">
                                                    <v-card-text>
                                                        <v-row>
                                                            <v-col cols="12" md="12">
                                                                <v-sheet class="pt-10 px-4">
                                                                    <v-autocomplete v-model="product.filters"
                                                                        :items="filters" label="Filtros (*)"
                                                                        item-title="name" item-value="id" chips multiple
                                                                        no-data-text="No se encontraron Filtros"
                                                                        closable-chips color="#1e88e5"
                                                                        variant="outlined" density="comfortable"
                                                                        :rules="[rules.required, rules.array, rules.arrayNotEmpty]">

                                                                        <template v-slot:chip="{ props, item }">
                                                                            <v-chip v-bind="props" :text="item.raw.name"
                                                                                color="#1e88e5"></v-chip>
                                                                        </template>

                                                                        <template v-slot:item="{ props, item }">
                                                                            <v-list-item v-bind="props"
                                                                                :title="item.raw.name">
                                                                                <template v-slot:subtitle>
                                                                                    <span
                                                                                        class="text-overline text-[#002528]">
                                                                                        {{ item.raw.filter.name }}
                                                                                    </span>
                                                                                </template>
                                                                            </v-list-item>
                                                                        </template>

                                                                    </v-autocomplete>
                                                                </v-sheet>
                                                            </v-col>
                                                        </v-row>
                                                    </v-card-text>
                                                </v-card>

                                                <v-btn type="submit" color="#1e88e5" variant="tonal"
                                                    :loading="fetch_loader_form" density="default"
                                                    class="w-100 transform hover:scale-110">
                                                    Actualizar Producto
                                                </v-btn>
                                            </v-card-text>
                                        </v-card>
                                    </div>
                                </v-form>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
