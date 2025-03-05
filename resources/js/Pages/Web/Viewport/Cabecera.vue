<script setup>
import { Link } from '@inertiajs/vue3';
import Promotion from '@/Pages/Web/Viewport/Promotion.vue';
import Nav from '@/Pages/Web/Viewport/Nav.vue';
import PapupCarrito from '@/Pages/Web/Carrito/PapupCarrito.vue';
import Logo from '@assets/img/logo.png';
import { ref, onMounted } from "vue";
import { useSettingStore } from '@stores/SettingStore';
const categories = ref([]);
const groups = ref([]);
const subgroups = ref([]);
const search = ref('');
const imagenesProducto = ref([]);
const props = defineProps({
    categories: Array,
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
const showGroup = (categoryId) => {
    categories.value.forEach((category) => {
        category.dropdownVisibleSubnav = category.id === categoryId;
    });
};

const hideGroup = () => {
    categories.value.forEach((category) => {
        category.dropdownVisibleSubnav = false;
    });
};
let lastScrollTop = 0;

window.addEventListener('scroll', () => {
    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    let promoDiv = document.getElementById('promoDiv');
    let fixedContent = document.getElementById('fixedContent');

    if (promoDiv && fixedContent) { // Verificar si los elementos existen
        if (currentScroll > lastScrollTop) {
            // Scroll hacia abajo
            promoDiv.classList.add('hidden');
            fixedContent.style.top = "0"; // Fija el div en la parte superior
        } else {
            // Si el scroll está en la parte superior de la página
            if (currentScroll === 0) {
                // Scroll hacia arriba
                promoDiv.classList.remove('hidden');
                fixedContent.style.top = promoDiv.clientHeight + "px"; // Ajusta la posición debajo del div de promociones
            }
        }
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Para manejar el caso cuando el usuario se desplaza hasta el tope de la página
});
const dropdownVisible = ref(false);
const dropdownVisibleSub = ref(false);
const showPopup = ref(false);
const showPopupProduct = ref(false);
const filteredProducts = ref([]);
const filteredImagesProducts = ref([]);
const routeimage = ref([]);
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
const toggleDropdownSub = () => {
    dropdownVisibleSub.value = !dropdownVisibleSub.value;
};

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const closePopup = () => {
    showPopup.value = false;
    obtenerCantidadPedidos();
};

const updateSearch = async () => {
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

const productos = ref([]);
const cantPedidos = ref(0);
const subtotalsoles = ref(0);

const obtenerCantidadPedidos = async () => {
    productos.value = JSON.parse(localStorage.getItem('producto')) || [];
    cantPedidos.value = 0; // Restablecer a cero
    subtotalsoles.value = 0; // Restablecer a cero

    productos.value.forEach((item) => {
        cantPedidos.value += item.quantity;
        subtotalsoles.value += item.subtotalsoles;
    });
};

const handleEnter = async () => {
    const url = route('search-products', { s: search.value.toUpperCase() });
    window.location.href = url;
};
const hidePopupOnMouseLeave = () => {
    showPopupProduct.value = false;
};
onMounted(async () => {
    await cargarcategories();
    await cargargroups();
    await cargarsubgroups();
    await obtenerCantidadPedidos();
});
</script>

<template>
    <div>
        <!-- Promociones -->
        <div class="hidden md:block" v-if="settingsGlobal.getImagsupvalue">
            <Promotion :settingsbanner="settingsGlobal.getImagsupvalue" />
        </div>
        <div class="block md:hidden" v-if="settingsGlobal.getImagsupmobilevalue">
            <Promotion :settingsbanner="settingsGlobal.getImagsupmobilevalue" />
        </div>
        <div class="fixed z-10"
            :class="{ 'top-12': settingsGlobal.getImagsupvalue, 'top-0': !settingsGlobal.getImagsupvalue }"
            id="fixedContent">
            <!-- Aplicar degradado vertical aquí -->
            <div class="hidden lg:flex flex-col bg-gradient-to-b from-sky-900 to-sky-500 2xl:px-24">
                <div class="hidden lg:flex p-5 sm:items-center">
                    <div class="flex justify-between items-center w-full gap-10">
                        <!-- Logo -->
                        <Link class="flex cursor-pointer w-72 h-20 px-5" :href="route('web')">
                        <img class="rounded-md w-full h-full" :src="Logo" alt="Logo">
                        </Link>
                        <!-- Buscador -->
                        <div class="w-100 hidden lg:flex items-center relative rounded-full">
                            <v-text-field v-model="search" @input="updateSearch" @keyup.enter="handleEnter"
                                label="Buscar ProductoS" prepend-inner-icon="mdi-magnify" variant="outlined"
                                hide-details single-line density="comfortable" class="bg-transparent text-white">
                            </v-text-field>
                            <!-- Popup de productos filtrados -->
                            <div v-if="showPopupProduct"
                                class="absolute z-10 w-auto bg-white rounded-md shadow-lg mt-1 top-full overflow-y-scroll"
                                @mouseleave="hidePopupOnMouseLeave">
                                <ul>
                                    <Link :href="route('productid', { slug: product.slug })"
                                        v-for="product in filteredProducts" :key="product.id"
                                        class="flex items-center gap-1 px-4 py-2 cursor-pointer hover:bg-gray-100">
                                    <img :src="getRouteForProduct(product.id)" alt="Img"
                                        class="h-12 w-12 rounded object-cover" />
                                    <p class="flex flex-col text-sm text-gray-800">
                                        <span>{{ product.name }}</span>
                                        <span class="font-bold">Stock: {{ product.stock }}</span>
                                    </p>
                                    </Link>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Navegación para el carrito y redes sociales -->
                    <div class="px-4 hidden lg:flex items-center">
                        <div class="flex items-center">
                            <div class="flex flex-row items-center">
                                <button @click="togglePopup" class="relative">
                                    <i class="mdi mdi-cart text-4xl text-white p-2 rounded-xl m-2"></i>
                                    <span class="absolute text-white -top-3 right-0 bg-sky-500 rounded-full px-2">{{
                                        cantPedidos }}</span>
                                </button>
                                <span class="text-white pt-4"><span class="mx-1">S/.</span>{{
                                    subtotalsoles.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                    maximumFractionDigits: 2 }) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mr-2">
                        <Nav />
                    </div>
                    <div class="text-white text-center text-xs font-bold bg-sky-500 px-1 py-1 rounded-lg">
                        <p class="w-20">T. Cambio <br>S/. {{ settingsGlobal.getDolarValue }}</p>
                    </div>
                </div>
                <PapupCarrito v-if="showPopup" @close="closePopup" :images="images" />
            </div>

            <!-- Bloque gris (sin cambios) -->
            <div class="relative hidden lg:flex items-center w-screen bg-navegation gap-1 2xl:px-24">
                <details :open="dropdownVisible" class="relative w-1/6 h-16 xl:px-4" @mouseover="dropdownVisible = true"
                    @mouseout="dropdownVisible = false">
                    <summary
                        :class="{ 'bg-gradient-blue text-white': dropdownVisible, 'hover:bg-gradient-blue': !dropdownVisible }"
                        class="flex items-center justify-between px-4 text-white cursor-pointer 2xl:w-48 h-full">
                        <span class="text-xs font-bold flex items-center"><i
                                class="mdi mdi-menu text-lg mx-2"></i>CATEGORIAS</span>
                        <span class="shrink-0 group-open:-rotate-180">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" :class="{ 'text-white': dropdownVisible }"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </summary>
                    <div class="absolute bg-white w-auto top-16">
                        <ul class="bg-white">
                            <li v-for="(categoria, index) in categories" :key="index"
                                @mouseover="categoria.dropdownVisibleSub = true"
                                @mouseout="categoria.dropdownVisibleSub = false">
                                <details :open="categoria.dropdownVisibleSub"
                                    class="group hover:bg-gradient-blue hover:text-white relative">
                                    <summary @click="toggleDropdownSub(categoria)"
                                        :class="{ 'bg-sky-500': categoria.dropdownVisibleSub, 'hover:bg-sky-500': !categoria.dropdownVisibleSub }"
                                        class="flex p-2 rounded cursor-pointer text-xs border-b w-52">
                                        <span :class="{ 'rotate-360': categoria.dropdownVisibleSub }"
                                            class="flex shrink-0 transition duration-100 justify-between w-full">
                                            <Link :href="route('categoryid', { slug: categoria.slug })"
                                                class="block px-4 py-1 font-bold text-gray-800"><v-icon>{{
                                                categoria.icon }}</v-icon> {{ categoria.name }}</Link>
                                        </span>
                                    </summary>
                                    <div
                                        class="flex flex-wrap absolute bg-white top-0 left-56 w-lvw border-l border-sky-300 font-bold">
                                        <div v-for="(group, subIndex) in groups" :key="subIndex" class="flex">
                                            <div v-if="group.category_id === categoria.id"
                                                :open="group.dropdownVisibleSub" class="flex border-b">
                                                <div @click="toggleDropdownSub(group)"
                                                    class="p-3 cursor-pointer text-xs">
                                                    <Link :href="route('groupid', { slug: group.slug })"
                                                        class="py-2 font-bold text-sky-500">
                                                    <v-icon>{{ group.icon }}</v-icon> {{ group.name }}
                                                    </Link>
                                                    <ul class="w-full h-full overflow-y-auto">
                                                        <li v-for="(subgroup, subsubIndex) in subgroups"
                                                            :key="subsubIndex" class="">
                                                            <Link v-if="subgroup.group_id === group.id"
                                                                :href="route('subgroupid', { slug: subgroup.slug })"
                                                                class="block py-2 text-xs hover:bg-sky-100 focus:ring focus:ring-sky-200 transition-all duration-300">
                                                            <v-icon>{{ subgroup.icon }}</v-icon> {{ subgroup.name }}
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </li>
                        </ul>
                    </div>
                </details>

                <div class="relative pl-5 h-16 w-full" v-if="categories.length > 0">
                    <div class="flex items-center overflow-hidden w-full h-full">
                        <div v-for="(categorianav, index) in categories" :key="index" id="grupoid"
                            class="flex items-center overflow-hidden w-full">
                            <details :open="categorianav.dropdownVisibleSubnav" @mouseover="showGroup(categorianav.id)"
                                @mouseout="hideGroup(categorianav.id)"
                                class="flex rounded hover:bg-gradient-blue text-xs font-medium text-center transition-all duration-300 w-full">
                                <summary
                                    class="flex items-center justify-center 2xl:py-5 transition-all duration-300 w-full">
                                    <Link class="text-white px-2 2xl:px-4 flex items-center h-16 w-full"
                                        :href="route('categoryid', { slug: categorianav.slug })"><v-icon
                                        class="pr-0.5">{{ categorianav.icon }}</v-icon> {{ categorianav.name }}</Link>
                                </summary>
                                <div class="absolute w-full top-16 left-0 right-0 bg-white">
                                    <ul class="flex flex-wrap">
                                        <li v-for="(group, subIndex) in groups" :key="subIndex">
                                            <div v-if="group.category_id === categorianav.id"
                                                :open="group.dropdownVisibleSubnav"
                                                class="group text-sky-500 text-center">
                                                <div @click="toggleDropdownSub(group)"
                                                    class="p-1 cursor-pointer transition-all duration-200 text-xs font-bold">
                                                    <Link :href="route('groupid', { slug: group.slug })"
                                                        class="block px-2 py-2">
                                                    <v-icon>{{ group.icon }}</v-icon> {{ group.name }}
                                                    </Link>
                                                </div>
                                                <ul class="bg-white">
                                                    <li v-for="(subgroup, subsubIndex) in subgroups" :key="subsubIndex">
                                                        <Link v-if="subgroup.group_id === group.id"
                                                            :href="route('subgroupid', { slug: subgroup.slug })"
                                                            class="block px-2 py-2 text-xs hover:bg-sky-100 hover:text-sky-500 focus:ring focus:ring-sky-200 transition-all duration-300">
                                                        <v-icon>{{ subgroup.icon }}</v-icon> {{ subgroup.name }}
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
