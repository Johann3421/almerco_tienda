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

// ✅ Generar contenido SEO-friendly para <noscript>
const noscriptContent = computed(() => `
    <h1>Descubre nuestros productos</h1>
    <ul>
        ${products.value.map(product => `
            <li>
                <strong>${product.name}</strong>: ${product.description || 'Producto disponible en nuestra tienda.'}
            </li>
        `).join('')}
    </ul>
`);

const generarSchemaMarkup = () => {
    const schemaMarkup = {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": products.value.map((product, index) => ({
            "@type": "Product",
            "name": product.name,
            "description": product.description,
            "brand": product.brand,
            "image": `/storage/${product.image_path}`,
            "offers": {
                "@type": "Offer",
                "price": product.price,
                "priceCurrency": "USD"
            }
        }))
    };
    return JSON.stringify(schemaMarkup, null, 2);
};

const actualizarSchemaMarkup = () => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = generarSchemaMarkup();
    
    const existingScript = document.querySelector('script[type="application/ld+json"]');
    if (existingScript) {
        existingScript.remove();
    }
    
    document.head.appendChild(script);
};

watch(products, () => {
    actualizarSchemaMarkup();
}, { deep: true });

onMounted(() => {
    cargarProductos();
    cargarImagenesProductos();
    cargarImagenesBrands();
    actualizarSchemaMarkup();
});

const actualizarKeywords = () => {
    nextTick(() => {
        const keywords = products.value.map(p => p.name.toLowerCase()).join(', ');
        let metaKeywords = document.querySelector('meta[name="keywords"]');

        if (!metaKeywords) {
            metaKeywords = document.createElement('meta');
            metaKeywords.setAttribute('name', 'keywords');
            document.head.appendChild(metaKeywords);
        }
        metaKeywords.setAttribute('content', keywords);
    });
};

watch(products, actualizarKeywords, { deep: true });

onMounted(() => {
    cargarProductos();
    cargarImagenesProductos();
    cargarImagenesBrands();
    actualizarKeywords();
});
</script>

<template>
    <!-- ✅ NOSCRIPT para SEO -->
    <noscript v-html="noscriptContent"></noscript>

    <AppWebLayout title="SEKAI TECH" class="bg-fondoback" :categories="categories">
        <Header @search-change="handleSearchChange" :categories="categories" :images="images" />
        <div class="lg:mt-44">
            <Banner :banners="banners" />
        </div>
        <SmallNav :categories="categories" nombreboton="NOVEDADES" />
        <Products :subgroups="subgroups" :images="images" :brands="brands" />

        <div class="py-10" v-if="settingsGlobal.getImagmedvalue">
            <img class="hidden md:block aspect-39/10 w-full"
                :src="`/storage/${settingsGlobal.getImagmedvalue.file_path}/${settingsGlobal.getImagmedvalue.file}`"
                :alt="settingsGlobal.getImagmedvalue.description || 'Imagen destacada del producto'">
            
            <img class="aspect-video w-full md:hidden"
                v-if="settingsGlobal.getImagmedmobilevalue"
                :src="`/storage/${settingsGlobal.getImagmedmobilevalue.file_path}/${settingsGlobal.getImagmedmobilevalue.file}`"
                :alt="settingsGlobal.getImagmedmobilevalue.description || 'Imagen destacada en móvil'">
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
