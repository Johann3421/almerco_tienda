<script setup>
import { defineProps, ref, onMounted, computed } from 'vue';
import CartMain from '@/Pages/Web/Carts/CartProductsMain.vue';

const props = defineProps({
    subgroups: Array,
    images: Array,
    brands: Array
});

const products = ref([]);
const imagenesProducto = ref(new Map());
const imagenesBrand = ref(new Map());
const page = ref(1); // Página actual

const cargarProductos = () => {
    props.subgroups.forEach(subgroup => {
        products.value.push(...subgroup.products);
    });
};

const cargarImagenesProductos = () => {
    if (Array.isArray(props.images) && props.images.length > 0) {
        props.images.forEach(imagen => {
            if (!imagenesProducto.value.has(imagen.product_id)) {
                imagenesProducto.value.set(imagen.product_id, []);
            }
            imagenesProducto.value.get(imagen.product_id).push(imagen);
        });
    }
};

const cargarImagenesBrands = () => {
    if (Array.isArray(props.brands) && props.brands.length > 0) {
        props.brands.forEach(brand => {
            brand.filter_items.forEach(filterItem => {
                filterItem.products.forEach(product => {
                    if (!imagenesBrand.value.has(product.id)) {
                        imagenesBrand.value.set(product.id, []);
                    }
                    imagenesBrand.value.get(product.id).push({
                        file_path: filterItem.file_path,
                        file: filterItem.file
                    });
                });
            });
        });
    }
};

const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.get(product_id);
    return imagenes ? imagenes : [];
};

const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.get(product_id);
    return brand && brand.length > 0 ? brand[0] : null;
};

// Método para calcular cuántos productos se pueden mostrar por fila
const getProductsPerRow = () => {
    const width = window.innerWidth; // Obtiene el ancho de la ventana
    if (width >= 7680) return 23;
    if (width >= 7256) return 22;
    if (width >= 6928) return 21;
    if (width >= 6600) return 20;
    if (width >= 6272) return 19;
    if (width >= 5944) return 18;
    if (width >= 5616) return 17;
    if (width >= 5288) return 16;
    if (width >= 4960) return 15;
    if (width >= 4632) return 14;
    if (width >= 4304) return 13;
    if (width >= 3976) return 12;
    if (width >= 3648) return 11;
    if (width >= 3320) return 10;
    if (width >= 2992) return 9;
    if (width >= 2664) return 8;
    if (width >= 2560) return 7;
    if (width >= 2008) return 6;
    if (width >= 1920) return 5;
    if (width >= 1352) return 4; //
    if (width >= 1024) return 3; //
    if (width >= 696) return 2;
    return 1; // Ejemplo: 1 producto por fila en pantallas muy pequeñas
};

// Calcula el subconjunto de productos a mostrar basado en la página actual
const paginatedProducts = computed(() => {
    const maxProductsPerRow = getProductsPerRow();
    const totalProductsPerPage = maxProductsPerRow * 2; // Dos filas
    const start = (page.value - 1) * totalProductsPerPage;
    const end = start + totalProductsPerPage;
    return products.value.slice(start, end);
});

onMounted(() => {
    cargarProductos();
    cargarImagenesProductos();
    cargarImagenesBrands();
    window.addEventListener('resize', () => {
        // Forzar la actualización de los productos paginados en caso de cambiar el tamaño de la ventana
        paginatedProducts.value; // Solo accede para que se recalculen los productos
    });
});
</script>

<template>
    <div>
        <div class="flex flex-row p-5">
            <div class="flex flex-wrap justify-center">
                <CartMain v-for="product in paginatedProducts" :key="product.id" :producto="product" :images="getImagenesProducto(product.id)" :brand="getImagenesBrand(product.id)" :totalimages="images" />
            </div>
        </div>
        <div class="text-center mt-4">
            <v-container>
                <v-row justify="center">
                    <v-col cols="8">
                        <v-container class="max-width">
                            <v-pagination
                                v-model="page"
                                :length="Math.ceil(products.length / (getProductsPerRow() * 2))">
                            </v-pagination>
                        </v-container>
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </div>
</template>
