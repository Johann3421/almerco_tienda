<script setup>
import { Link } from '@inertiajs/vue3';
import { useSettingStore } from '@stores/SettingStore';
import PapupCarrito from './PapupCarrito.vue';
import Logo from '@assets/img/logo.png';
import { ref, nextTick, onMounted } from "vue";

const categories = ref([]);
const groups = ref([]);
const subgroups = ref([]);
const search = ref('');
const imagenesProducto = ref([]);
const props = defineProps({
    categories: Array, // Define el tipo de la prop productos como un Array
    images: Object
});
const settingsGlobal = useSettingStore();
const cargarcategories = async () => {
    categories.value = props.categories;
};

const cargargroups = async () => {
    // Itera sobre cada categoría y agrega sus grupos a la lista de grupos
    categories.value.forEach(category => {
        groups.value.push(...category.groups);
    });
};

const cargarsubgroups = async () => {
    // Itera sobre cada grupo y agrega sus subgrupos a la lista de subgrupos
    groups.value.forEach(group => {
        subgroups.value.push(...group.subgroups);
    });
};

const mostrarDiv = ref(false);
const showPopup = ref(false);
const showSearch = ref(false);
const searchInput = ref(null); // Referencia al elemento de búsqueda
const filteredProducts = ref([]);
const filteredImagesProducts = ref([]);
const showPopupProduct = ref(false);
const routeimage = ref([]);

const toggleDropdownSub = (categoria) => {
    categoria.dropdownVisibleSub = !categoria.dropdownVisibleSub;
};

const toggleDiv = () => {
    mostrarDiv.value = !mostrarDiv.value;
};

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const closePopup = () => {
    showPopup.value = false;
};

const toggleSearch = () => {
    showSearch.value = !showSearch.value;
    if (showSearch.value) {
        nextTick(() => {
            if (searchInput.value !== null) {
                searchInput.value.focus();
            }
        });
    }
};

const closeSearch = () => {
    showSearch.value = false;
};
const updateSearch = async () =>{
    try {
        const response = await axios.get(`/filter-products-search?search=${search.value}`);
        // Actualizar la lista de productos filtrados
        filteredProducts.value = response.data.products;
        filteredImagesProducts.value = response.data.images;
        cargarImagenesProductos(filteredProducts.value, filteredImagesProducts.value);
        for (let i = 0; i < filteredProducts.value.length; i++) {
            getImagenesProducto(filteredProducts.value[i].id);
        }
        // Mostrar el popup si hay resultados
        showPopupProduct.value = filteredProducts.value.length > 0;
      } catch (error) {
        console.error('Error al buscar productos:', error);
      }
};
const handleEnter = async () => {
    const url = route('search-products', { s: search.value.toUpperCase() });
    window.location.href = url;
};
const cargarImagenesProductos = (filteredProducts, filteredImages) => {
    if (filteredProducts && filteredProducts.length > 0) {
        filteredProducts.forEach(product => {
            const imagenes = filteredImages.filter(image => image.product_id === product.id);
            imagenesProducto.value.push({ product_id: product.id, imagenes });
        });
    }
};
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    if (imagenes && imagenes.imagenes.length > 0) {
        mostrarimageproductfilter(product_id, imagenes.imagenes[0].file_path, imagenes.imagenes[0].file);
    }
};
const mostrarimageproductfilter = (product_id, file_path, file) => {
    const linkroute = `/storage/${file_path}/${file}`;
    const existingRoute = routeimage.value.find(route => route.id === product_id);
    if (!existingRoute) {
        routeimage.value.push({ id: product_id, route: linkroute });
    }
};
const getRouteForProduct = (productId) => {
    const route = routeimage.value.find(item => item.id === productId);
    return route ? route.route : ''; // Retorna la ruta si se encuentra, de lo contrario retorna una cadena vacía
};
const scrollPosition = ref(0);
const handleScroll = () => {
    if (settingsGlobal.getImagsupvalue) {
        scrollPosition.value = window.scrollY;
    }else{
        scrollPosition.value = 0;
    }
};
onMounted(async () => {

    await cargarcategories();
    await cargargroups();
    await cargarsubgroups();
    window.addEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="fixed flex flex-col lg:hidden w-full" :class="{ 'top-0': scrollPosition > 0 || !settingsGlobal.getImagsupvalue , 'top-12': settingsGlobal.getImagsupvalue }">
        <nav class="relative flex justify-around bg-stone-700 p-1 items-center w-full text-white">
            <div v-if="mostrarDiv" @click="toggleDiv">
                <span><i class="mdi mdi-close text-4xl"></i></span>
            </div>
            <div v-else class="flex flex-row">
                <!-- Ícono del menú que se utiliza para mostrar/ocultar el div -->
                <span class="block" @click="toggleDiv"> <i class="mdi mdi-menu text-4xl"></i> </span>
            </div>

            <Link class="flex cursor-pointer w-full h-14 px-5 justify-center" :href="route('web')">
                <img class="rounded-md h-full" :src="Logo" alt="Logo">
            </Link>
            <!-- BUSCADOR -->
            <div v-show="showSearch"
                class="w-full absolute right-0 top-0 bottom-0 flex items-center bg-white px-5 border-b border-black">
                <v-text-field v-model="search" @input="updateSearch" @keyup.enter="handleEnter" label="Buscar Producto"
                    prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line density="comfortable"
                    class="w-full h-12 bg-white rounded focus:outline-none focus:ring focus:border-orange-500">
                </v-text-field>
                <div v-if="showPopupProduct"
                    class="absolute z-10 w-auto bg-white rounded-md shadow-lg top-full mt-2 overflow-y-auto"
                    @mouseleave="hidePopupOnMouseLeave">
                    <ul>
                        <Link :href="route('productid', { slug: product.slug })" v-for="product in filteredProducts"
                            :key="product.id"
                            class="flex items-center gap-1 px-4 py-2 cursor-pointer hover:bg-fondoback">
                        <img :src="getRouteForProduct(product.id)"
                            alt="Img" class=" h-12 w-12 rounded object-cover" />
                        <p class="flex flex-col text-sm">
                            <span>{{ product.name }}</span>
                            <span class="font-bold">Stock: {{ product.stock }}</span>
                        </p>
                        </Link>
                    </ul>
                </div>
                <span class="cursor-pointer" @click="closeSearch"><i class="mdi mdi-close text-4xl"></i></span>
            </div>
            <span @click="toggleSearch" class="cursor-pointer"><i class="mdi mdi-magnify text-4xl"></i></span>
        </nav>
        <div class="flex flex-col items-center shrink-0 transition duration-300 z-10 bg-white">
            <div v-if="mostrarDiv" class="w-full fixed bg-white">
                <!-- Contenido del div que quieres ocultar/mostrar -->
                <nav class="flex justify-around items-center p-2 w-full border-b border-black">
                    <!-- <a href="" class="block text-center px-4">
                        <i class="mdi mdi-account text-4xl"></i>
                        <p>Iniciar Sesión</p>
                    </a> -->
                    <button @click="togglePopup" class="block text-center px-4">
                        <i class="mdi mdi-cart text-4xl"></i>
                        <p>Carrito</p>
                    </button>
                </nav>
                <div class="p-4 w-full border-b border-black bg-fondoback">
                    <h1>Categorías</h1>
                </div>
                <div class="w-full">
                    <ul class="absolute w-full bg-white h-screen overflow-y-auto">
                        <li v-for="(categoria, index) in categories" :key="index"
                            @mouseover="categoria.dropdownVisibleSub = true"
                            @mouseout="categoria.dropdownVisibleSub = false">
                            <details :open="categoria.dropdownVisibleSub"
                                class="group hover:bg-orange-100 hover:text-white w-full bg-white">
                                <summary @click="toggleDropdownSub(categoria)"
                                    :class="{ 'bg-orange-300': categoria.dropdownVisibleSub, 'hover:bg-orange-300': !categoria.dropdownVisibleSub }"
                                    class="flex p-2 rounded text-white cursor-pointer transition-all duration-300">
                                    <span :class="{ 'rotate-360': categoria.dropdownVisibleSub }"
                                        class="flex shrink-0 transition duration-300 justify-between">
                                        <li class="block rounded-lg px-4 py-1 text-sm font-medium text-black">{{ categoria.name }}</li>
                                    </span>
                                </summary>
                                <ul class="w-full h-full border-l border-orange-200 bg-orange-100">
                                    <li v-for="(group, subIndex) in groups" :key="subIndex"
                                        @mouseover="group.dropdownVisibleSub = true"
                                        @mouseout="group.dropdownVisibleSub = false">
                                        <details v-if="group.category_id === categoria.id"
                                            :open="group.dropdownVisibleSub"
                                            class="group hover:bg-orange-100 hover:text-white">
                                            <summary @click="toggleDropdownSub(group)"
                                                :class="{ 'bg-orange-200': group.dropdownVisibleSub, 'hover:bg-orange-200': !group.dropdownVisibleSub }"
                                                class="flex p-2 text-white cursor-pointer transition-all duration-300">
                                                <li class="block px-4 py-1 text-sm font-medium text-black">
                                                {{ group.name }}
                                                </li>
                                            </summary>
                                            <ul
                                                class="bg-white w-full h-full overflow-y-auto">
                                                <li v-for="(subgroup, subsubIndex) in subgroups" :key="subsubIndex" class="text-center">
                                                    <Link v-if="subgroup.group_id === group.id"
                                                        :href="route('subgroupid', { slug: subgroup.slug })"
                                                        class="block px-4 py-3 text-sm font-medium text-black hover:bg-orange-300 hover:text-white focus:ring focus:ring-orange-200 transition-all duration-300 border-b">
                                                    {{ subgroup.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </details>
                                    </li>
                                </ul>
                            </details>
                        </li>
                    </ul>
                </div>
            </div>
            <PapupCarrito v-if="showPopup" @close="closePopup" :images="images" />
        </div>
    </div>
</template>
