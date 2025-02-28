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
const showPopupCant = ref(false);
const routeimage = ref('');
routeimage.value = props.images && props.images.length > 0 ? `/storage/${props.images[0].file_path}/${props.images[0].file}`: '';

const showCartPopupCant = () => {
  showPopupCant.value = !showPopupCant.value;
};

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
};

// Mira cambios en las props y actualiza el producto
watch(() => props.producto, () => {
    product.value = props.producto;
});

const actualizarsubtotales = () => {
    emit('producto-eliminado');
};
</script>
<template>
    <li v-if="product" class="flex items-center justify-between border-b border-black bg-white p-2 text-xs">
        <img :src="routeimage" alt="Imagen" class="rounded object-cover w-1/4" />
        <div class="flex flex-col w-full px-2">
            <div class="flex justify-between border-b border-black">
                <div class="flex flex-col gap-1">
                    <span>{{ product.name }} </span>
                    <span class="font-bold"><span class="mr-1">Cantidad:</span>{{ product.quantity }}</span>
                    <div class="flex gap-2 items-center">
                        <span class="font-bold">Precio:</span>
                        <span class="py-1 px-3 text-rojo"><span class="mr-1">$</span>{{ product.pricedolar.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                        <span class="py-1 px-3"><span class="mr-1">S/.</span>{{ product.pricesoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    </div>
                </div>
                <div class="relative flex flex-col items-center py-2">
                    <i class="mdi mdi-delete text-4xl cursor-pointer" @click="eliminarProducto"></i>
                    <button class="w-10 h-10 rounded-full border text-3xl font-bold transition hover:scale-105"
                        @click="showCartPopupCant"><i class="mdi mdi-plus text-4xl cursor-pointer"></i></button>
                    <!-- Popup carrito -->
                    <div v-if="showPopupCant" class="absolute top-10 right-10 bg-white p-2 rounded-lg shadow-lg">
                        <CantP :producto="[product]" @producto-eliminado="actualizarsubtotales" />
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between w-full">
                <span class="font-bold">Subtotal: </span>
                <div class="flex justify-between w-full font-bold">
                    <span class="py-1 px-3 text-rojo">${{ product.subtotaldolar.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    <span class="py-1 px-3 bg-encabezado text-white rounded-full">S/.{{ product.subtotalsoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                </div>
            </div>
        </div>
    </li>
</template>
