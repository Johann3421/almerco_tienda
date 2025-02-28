<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";
import CantP from '@/Pages/Web/Viewport/PapupCant.vue';

const emit = defineEmits(['producto-eliminado']);

// Definir las props que el componente puede recibir
const props = defineProps({
    producto: Object, // Define el tipo de dato para el prop producto como un objeto
    images: Object, // Define el tipo de dato para el prop imagenes como un array
});
const product = ref(props.producto);
const routeimage = ref('');
routeimage.value = props.images && props.images.length > 0 ? `/storage/${props.images[0].file_path}/${props.images[0].file}`: '';

// Función para eliminar el producto del almacenamiento local
const eliminarProducto = () => {
    const productos = JSON.parse(localStorage.getItem('producto')) || [];
    const index = productos.findIndex(item => item.id === product.value.id);
    if (index !== -1) {
        productos.splice(index, 1); // Eliminar el producto del array
        localStorage.setItem('producto', JSON.stringify(productos)); // Actualizar localStorage
        product.value = null; // Limpiar la referencia del producto
        emit('producto-eliminado');
    }
    window.location.reload();
};

// Mira cambios en las props y actualiza el producto
watch(() => props.producto, () => {
    product.value = props.producto;
});

const actualizarsubtotales = () => {
    emit('producto-eliminado');
    window.location.reload();
};
</script>
<template>
    <li v-if="product" class="py-1">
        <div class="flex flex-col md:flex-row items-center justify-between border border-black p-4 w-full bg-gray-50">
            <div class="flex items-center w-full">
                <div class="w-1/2 md:w-1/3 lg:w-1/4 p-1">
                    <img :src="routeimage" alt="Imagen" class="w-full rounded object-cover" />
                </div>
                <div class="w-full">
                    <span>{{ product.name }} </span>
                </div>
            </div>
            <div class="hidden xl:flex xl:flex-col justify-center items-center xl:w-1/4">
                <span class="xl:px-6 rounded-xl ">$ {{ product.pricedolar.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                <span class="xl:px-6  rounded-xl font-bold">S/. {{ product.pricesoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
            </div>
            <div class="flex items-center py-2 gap-5 w-full lg:w-1/2">
                <form action="">
                    <CantP :producto="[product]" @producto-eliminado="actualizarsubtotales" />
                </form>

                <div class="flex flex-col justify-center items-center">
                    <span class="text-rojo px-4 py-1 rounded-xl font-bold">${{ product.subtotaldolar.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    <span class="text-white bg-encabezado px-4 py-1 rounded-xl font-bold">S/.{{ product.subtotalsoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                </div>
                <form>
                    <i class="mdi mdi-delete text-4xl cursor-pointer text-rojo" @click="eliminarProducto"></i>
                </form>
            </div>
        </div>
    </li>
</template>
