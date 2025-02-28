<script setup>
import { ref, onMounted, defineProps, defineEmits } from 'vue';

const emit = defineEmits(['producto-eliminado']);

const props = defineProps({
    producto: Array // Define el tipo de dato para la prop producto como un objeto
});

// Definir variables reactivas para los datos del producto
const quantity = ref(1);
const id = ref(0);
const productname = ref('');
const pricesoles = ref(0);
const pricedolar = ref(0);
const stock = ref(0);
const maxQuantity = ref(1);

// Inicializar los valores cuando el componente se monta
onMounted(() => {
    // Verificar si hay un producto seleccionado y actualizar los valores
    if (props.producto && props.producto.length > 0) {
        const prod = props.producto[0];
        id.value = prod.id;
        productname.value = prod.name;
        pricesoles.value = prod.pricesoles;
        pricedolar.value = prod.pricedolar;
        stock.value = prod.stock;
        maxQuantity.value = prod.stock;
        quantity.value = prod.quantity || 1;
    }
});

// Función para incrementar la cantidad del producto
const incrementQuantity = () => {
    if (quantity.value < maxQuantity.value) {
        quantity.value++;
        updateProduct();
    }
};

// Función para decrementar la cantidad del producto
const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
        updateProduct();
    }
};

// Función para actualizar los subtotales y almacenar el producto en el Local Storage
const updateProduct = () => {
    if (props.producto && props.producto.length > 0) {
        const prod = props.producto[0];
        prod.quantity = quantity.value;
        prod.name = productname.value;
        prod.subtotalsoles = quantity.value * pricesoles.value;
        prod.subtotaldolar = quantity.value * pricedolar.value;
        actualizarLocalStorage(); // Llama a la función para actualizar el Local Storage
    }
};

// Función para actualizar el Local Storage
const actualizarLocalStorage = () => {
    // Actualiza el estado del producto en el Local Storage
    const productos = JSON.parse(localStorage.getItem('producto')) || [];
    const index = productos.findIndex(item => item.id === id.value);
    if (index !== -1) {
        productos[index] = {
            id: id.value,
            name: productname.value,
            quantity: quantity.value,
            pricesoles: pricesoles.value,
            pricedolar: pricedolar.value,
            stock: stock.value,
            subtotalsoles: quantity.value * pricesoles.value,
            subtotaldolar: quantity.value * pricedolar.value
        };
        localStorage.setItem('producto', JSON.stringify(productos));
        emit('producto-eliminado');
    }
};
</script>

<template>
    <div class="flex justify-center items-center">
        <span class="px-3 py-1 rounded-full border hover:bg-black hover:text-white cursor-pointer text-lg"
            @click="decrementQuantity">
            -
        </span>
        <input v-model="quantity" min="0" class="w-12 text-center px-2" readonly>
        <span class="px-3 py-1 rounded-full border hover:bg-black hover:text-white cursor-pointer text-lg"
            @click="incrementQuantity">
            +
        </span>
    </div>
</template>
