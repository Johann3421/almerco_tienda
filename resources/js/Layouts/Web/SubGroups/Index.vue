<script setup>
import { ref, defineProps,onMounted, computed } from 'vue';
import CartMain from '@/Pages/Web/Carts/CartProductsMain.vue';
const props = defineProps({
    subgroup: Object, // Define el tipo de dato para la prop producto como un objeto
    products: Object,
    images: Object,
    brands: Array
});
const filterItems = ref([]);
const filters = ref([]);
const products = ref([]);
const productsold = ref([]);
const subgroupname = ref('');
const subgroupoldname = ref('');
const imagenesProducto = ref([]);
const codigosSeleccionados = ref([]);
const images = ref([]);
const imagesold = ref([]);
images.value = props.images;
imagesold.value = props.images;
const imagenesBrand = ref([]);

const page = ref(1); // Página actual

// Método para calcular cuántos productos caben en dos filas

// Método para cargar las imágenes de cada producto
const cargarImagenesProductos = () => {
    if ( images.value && images.value.length > 0) {
        products.value.forEach(producto => {
            const imagenes = images.value.filter(imagen => imagen.product_id === producto.id);
            imagenesProducto.value.push({ product_id: producto.id, imagenes });
        });
    }
};

const cargarImagenesBrands = () => {
    if (props.brands && props.brands.length > 0) {
        props.brands.forEach(brand => {
            brand.filter_items.forEach(filterItem => {
                filterItem.products.forEach(product => {
                    imagenesBrand.value.push({
                        product_id: product.id,
                        imagen: {
                            file_path: filterItem.file_path,
                            file: filterItem.file
                        }
                    });
                });
            });
        });
    }
};

const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.find(item => item.product_id === product_id);
    return brand ? brand.imagen : null;
};

// Método para obtener las imágenes asociadas con un product_id específico
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};
const hacerclick = async (filteritem) => {
    const index = codigosSeleccionados.value.findIndex(item => item.id === filteritem.id && item.filter_id === filteritem.filter.id);

    if (index === -1) {
        // Si no está seleccionado, lo añadimos
        codigosSeleccionados.value.push({ id: filteritem.id, filter_id: filteritem.filter.id });
    } else {
        // Si ya está seleccionado, lo removemos
        codigosSeleccionados.value.splice(index, 1);
    }

    if (codigosSeleccionados.value.length === 0) {
        products.value = productsold.value;
        images.value = imagesold.value;
        return;
    }

    try {
        const response = await axios.post('/filter-products', {
            filters: codigosSeleccionados.value,
            idtipo: props.subgroup.id, // Mantenemos la referencia a subgroup.id
            tipo: 'subgroup'
        });

        const newProducts = response.data.products;
        const newImages = response.data.images;

        // Verificar si los nuevos productos son iguales a los existentes
        if (JSON.stringify(productsold.value) !== JSON.stringify(newProducts)) {
            products.value = newProducts;
            images.value = newImages;
            page.value = 1; // Resetear a la primera página al aplicar el filtro
        }
    } catch (error) {
        console.error('Error al enviar la solicitud al controlador:', error);
    }
};

// Computed para la paginación
const paginatedProducts = computed(() => {
    const maxProductsPerRow = getProductsPerRow();
    const totalProductsPerPage = maxProductsPerRow * 2; // Dos filas
    const start = (page.value - 1) * totalProductsPerPage;
    const end = start + totalProductsPerPage;
    return products.value.slice(start, end);
});
const getProductsPerRow = () => {
    const width = window.innerWidth; // Obtiene el ancho de la ventana
    if (width >= 7680) return 21;
    if (width >= 3840) return 10;
    if (width >= 2808) return 7;
    if (width >= 2480) return 6;
    if (width >= 2152) return 5;
    if (width >= 1824) return 4;
    if (width >= 1496) return 3;
    if (width >= 1168) return 2; //
    return 1; // Ejemplo: 1 producto por fila en pantallas muy pequeñas
};
onMounted(() => {

    props.products.forEach(product => {
        if (product.filter_items && Array.isArray(product.filter_items)) {
            product.filter_items.forEach(item => {
                if (!filterItems.value.some(existingItem => existingItem.id === item.id)) {
                    filterItems.value.push(item);
                }
            });
        }
    });

    filterItems.value.forEach(item => {
        if (!filters.value.some(existingFilter => existingFilter.id === item.filter.id)) {
            filters.value.push(item.filter);
        }
    });
    products.value=props.subgroup.products;
    productsold.value=products.value;
    subgroupname.value=props.subgroup.name;
    subgroupoldname.value=subgroupname.value;
    cargarImagenesProductos();
    cargarImagenesBrands();
});
</script>

<template>
    <section class="sm:mt-48 sm:ml-32 sm:mr-28">
        <div class="block p-4 font-bold text-xl">
            <h1> {{ subgroupname }} ({{ products.length }})</h1>
        </div>
        <div class="relative sm:flex flex-row p-2">
            <div class="p-2 h-full">
                <h1 class="text-xl">Filtrar Por</h1>
                <div class="w-60 py-0.3" v-for="filter in filters" :key="filter.id">
                    <details class="rounded border border-gray-300 bg-white">
                        <summary class="flex cursor-pointer items-center justify-between gap-2 p-3 text-gray-900 transition">
                            <span class="text-sm font-medium"> {{  filter.name }} </span>
                            <span class="transition group-open:-rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </summary>
                        <div class="border-gray-200 bg-white">
                            <ul class="space-y-1 border-t border-gray-200 p-2">
                                <ul class="space-y-1 border-gray-200 p-2">
                                    <li v-for="(filteritem, index) in filterItems.filter(item => item.filter.id === filter.id)" :key="index">
                                        <div class="inline-flex items-center gap-2">
                                            <label :for="'Filter' + index">
                                                <input type="checkbox" :id="'Filter' + index" class="size-5 rounded border-gray-300" @change="hacerclick(filteritem, $event)" />
                                            </label>
                                            <span class="text-sm font-medium text-gray-700">{{ filteritem.name }}</span>
                                        </div>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                    </details>
                </div>

            </div>
            <div>
                <div class="flex flex-wrap justify-center w-full">
                    <CartMain v-for="product in paginatedProducts" :key="product.id" :producto="product" :images="getImagenesProducto(product.id)" :brand="getImagenesBrand(product.id)" :totalimages="images" class="m-2" />
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
        </div>
    </section>
</template>
