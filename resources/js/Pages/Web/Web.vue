<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import AppWebLayout from '@/Layouts/AppWebLayout.vue';
import Banner from '@/Pages/Web/Viewport/Banner.vue';
import Products from '@/Layouts/Web/Products/Products.vue';
import Header from '@/Pages/Web/Viewport/Cabecera.vue';
import NavMobil from '@/Pages/Web/Carrito/NavMobil.vue';
import CartMain from '@/Pages/Web/Carts/CartProductsMain.vue';
import SmallNav from '@/Pages/Web/Viewport/SmallNav.vue';
import { useSettingStore } from '@stores/SettingStore';

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
const products = ref([]);
const tamañoDelCart = 350;

const handleSearchChange = (searchTerm) => {
    filteredProducts.value = searchTerm;
};

const cargarProductos = () => {
    products.value = [];
    props.subgroups.forEach(subgroup => {
        products.value.push(...subgroup.products);
    });
};

const productosPorFila = computed(() => {
    return Math.floor(window.innerWidth / tamañoDelCart);
});

const cargarImagenesProductos = () => {
    imagenesProducto.value = [];
    if (props.images) {
        products.value.forEach(producto => {
            const imagenes = props.images.filter(imagen => imagen.product_id === producto.id);
            imagenesProducto.value.push({ product_id: producto.id, imagenes });
        });
    }
};

const cargarImagenesBrands = () => {
    imagenesBrand.value = [];
    if (props.brands) {
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
    productosPorFila.value = Math.floor(window.innerWidth / tamañoDelCart);
});

const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};

const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.find(item => item.product_id === product_id);
    return brand ? brand.imagen : null;
};

onMounted(() => {
    cargarProductos();
    cargarImagenesProductos();
    cargarImagenesBrands();
});

const actualizarSEO = () => {
    nextTick(() => {
        const productTitles = products.value.map(p => p.name).join(', ');
        
        // Actualizar el título de la página
        document.title = `Productos | SEKAI TECH - ${productTitles}`;
        
        // Añadir un H1 dinámico para el título del producto (oculto)
        const h1Element = document.createElement('h1');
        h1Element.textContent = `Productos: ${productTitles}`;
        h1Element.classList.add('hidden-h1'); // Aplicar la clase para ocultar
        document.body.prepend(h1Element); // Añadir el H1 al inicio del body
        
        // Actualizar la descripción meta
        const metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) {
            metaDescription.setAttribute('content', `Descubre los mejores productos en SEKAI TECH: ${productTitles}`);
        }
        
        // Actualizar las palabras clave meta
        const keywords = products.value.map(p => p.name.toLowerCase()).join(', ');
        const metaKeywords = document.querySelector('meta[name="keywords"]');
        if (metaKeywords) {
            metaKeywords.setAttribute('content', keywords);
        }
        
        // Añadir meta tags para author y publisher
        const metaAuthor = document.createElement('meta');
        metaAuthor.setAttribute('name', 'author');
        metaAuthor.setAttribute('content', 'Johann Abad Campos');
        document.head.appendChild(metaAuthor);
        
        const metaPublisher = document.createElement('meta');
        metaPublisher.setAttribute('name', 'publisher');
        metaPublisher.setAttribute('content', 'Johann Abad Campos');
        document.head.appendChild(metaPublisher);
    });
};

watch(products, actualizarSEO, { deep: true });
onMounted(() => {
    cargarProductos();
    cargarImagenesProductos();
    cargarImagenesBrands();
    actualizarSEO();
});
</script>

<template>
    <AppWebLayout title="SEKAI TECH" class="bg-fondoback" :categories="categories">
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
            <v-carousel v-if="productGroups.length > 0" hide-delimiters style="height: 100%;">
                <v-carousel-item v-for="(productGroup, groupIndex) in productGroups" :key="groupIndex">
                    <div class="flex justify-center 2xl:px-5">
                        <template v-for="(product, productIndex) in productGroup" :key="productIndex">
                            <CartMain :producto="product" :images="getImagenesProducto(product.id)"
                                :brand="getImagenesBrand(product.id)" :totalimages="images" />
                        </template>
                    </div>
                </v-carousel-item>
            </v-carousel>
            <p v-else class="text-center text-gray-500">No hay productos disponibles.</p>
        </div>
        
        <NavMobil :categories="categories" :images="images" />
    </AppWebLayout>
</template>


<style>
/* Añade esto en tu archivo de estilos CSS */
.hidden-h1 {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}
</style>