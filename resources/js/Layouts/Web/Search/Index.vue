<script setup>
import { defineProps, ref, onMounted, computed } from 'vue';
import CartMain from '@/Pages/Web/Carts/CartProductsMain.vue';

// Define las props del componente
const props = defineProps({
    products: Array, // Define el tipo de la prop productos como un Array
    images: Object,
    brands: Array
});

const products = ref([]);
const imagenesProducto = ref([]);
const imagenesBrand = ref([]);
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
const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.find(item => item.product_id === product_id);
    return brand ? brand.imagen : null;
};
// Método para obtener las imágenes asociadas con un product_id específico
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};
// Cargar los productos cuando el componente se monta
onMounted(() => {
    products.value = props.products;
    cargarImagenesProductos();
    cargarImagenesBrands();
});

</script>

<template>
    <div class="flex flex-row pt-10">
        <div class="flex flex-wrap justify-center">
            <CartMain v-for="product in products" :key="product.product_id" :producto="product" :images="getImagenesProducto(product.id)" :brand="getImagenesBrand(product.id)" :totalimages="images" />
        </div>
    </div>
</template>
