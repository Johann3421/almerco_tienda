<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Banner from '@/Pages/Web/Viewport/Banner.vue';
import Products from '@/Layouts/Web/Products/Products.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
import CartMain from '@/Pages/Web/Carts/CartProductsMain.vue';
import SmallNav from '@/Pages/Web/Viewport/SmallNav.vue';
import { useSettingStore } from '@stores/SettingStore';
// Define la variable reactiva para almacenar el término de búsqueda
const props = defineProps({
    categories: Array,
    banners: Array,
    products: Array,
    subgroups: Array,
    images: Object,
    brands: Array
});
const settingsGlobal = useSettingStore();
const filteredProducts = ref([]);
const imagenesProducto = ref([]);
const imagenesBrand = ref([]);

const handleSearchChange = (searchTerm) => {
    filteredProducts.value = searchTerm;
}

const products = ref([]);

const cargarProductos = () => {
    // Iterar sobre cada subgrupo y agregar sus productos a la lista de productos
    props.subgroups.forEach(subgroup => {
        products.value.push(...subgroup.products);
    });
};
const productosPorFila = computed(() => {
    const tamañoDelCart = 350; // Ajusta este valor según el ancho del
    return Math.floor(window.innerWidth / tamañoDelCart);
});
// Método para cargar las imágenes de cada producto
const cargarImagenesProductos = () => {
    if (props.images && props.images.length > 0) {
        products.value.forEach(producto => {
            const imagenes = props.images.filter(imagen => imagen.product_id === producto.id);
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
const productGroups = computed(() => {
    const groups = [];
    for (let i = 0; i < products.value.length; i += productosPorFila.value) {
        groups.push(products.value.slice(i, i + productosPorFila.value));
    }
    return groups;
});
watch(() => window.innerWidth, () => {
    // Calcula nuevamente el número de productos por fila
    productosPorFila.value = Math.floor(window.innerWidth / tamañoDelCart);
});
// Método para obtener las imágenes asociadas con un product_id específico
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};
const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.find(item => item.product_id === product_id);
    return brand ? brand.imagen : null;
};
cargarProductos();
cargarImagenesProductos();
cargarImagenesBrands();
</script>

<template>
    <AppWebLayout title="Grupo Almerco" class="bg-fondoback" :categories="categories">
        <Header @search-change="handleSearchChange" :categories="categories" :images="images" />
        <div class="lg:mt-44">
            <Banner :banners="banners" />
        </div>
        <SmallNav :categories="categories" nombreboton="NOVEDADES" />
        <Products :subgroups="subgroups" :images="images" :brands="brands" />
        <div class="py-10" v-if="settingsGlobal.getImagmedvalue">
            <img class="hidden md:block aspect-39/10 w-full" :src="`/storage/${settingsGlobal.getImagmedvalue.file_path}/${settingsGlobal.getImagmedvalue.file}`" alt="">
            <img class="aspect-video w-full md:hidden" v-if="settingsGlobal.getImagmedmobilevalue" :src="`/storage/${settingsGlobal.getImagmedmobilevalue.file_path}/${settingsGlobal.getImagmedmobilevalue.file}`" alt="Imagen de móvil">
        </div>
        <SmallNav :categories="categories" nombreboton="OFERTAS" />
        <!-- Carrusel de Productos -->
        <div class="sm:px-10 2xl:px-20">
            <v-carousel hide-delimiters style="height: 100%;">
                <v-carousel-item v-for="(productGroup, groupIndex) in productGroups" :key="groupIndex">
                    <div class="flex justify-center 2xl:px-5">
                        <template v-for="(product, productIndex) in productGroup" :key="productIndex">
                            <CartMain :producto="product" :images="getImagenesProducto(product.id)"
                                :brand="getImagenesBrand(product.id)" :totalimages="images" />
                        </template>
                    </div>
                </v-carousel-item>
            </v-carousel>
        </div>
        <!-- NAV EN FORMATO MOBIL -->
        <NavMobil :categories="categories" :images="images" />
    </AppWebLayout>
</template>
