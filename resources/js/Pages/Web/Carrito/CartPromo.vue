<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, defineProps, computed } from "vue";
import { useSettingStore } from '@stores/SettingStore';
// Definir las props que el componente puede recibir
const props = defineProps({
    producto: Object, // Define el tipo de dato para el prop producto como un objeto
    image: Object, // Define el tipo de dato para el prop imagenes como un array
});
const settingsGlobal = useSettingStore();
const showPopup = ref(false);
const pricesoles = ref(0);
const quantity = ref(1);
const maxQuantity = ref(1);
const id = ref(0);
const pricedolar = ref(0);
const stock = ref(0);
const nameproduct = ref('');
const specification = ref('');
const valoration = ref(0);
const filter_items = ref([]);
const productoEnCarrito = ref(false);
const routeimage = ref('');

id.value = props.producto.id;
filter_items.value = props.producto.filter_items;
valoration.value = props.producto.valoration;
specification.value = props.producto.specification;
nameproduct.value = props.producto.name;
stock.value = props.producto.stock;
pricedolar.value = props.producto.price;
const precioEnSoles = computed(() => {
    pricesoles.value = (props.producto.price * parseFloat(settingsGlobal.getDolarValue)).toFixed(2);
    return pricesoles.value;
});
const windowWidth = ref(window.innerWidth);

const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};

onMounted(() => {
    maxQuantity.value = props.producto.stock;
    const productosEnCarrito = JSON.parse(localStorage.getItem('producto')) || [];
    productoEnCarrito.value = productosEnCarrito.some(item => item.id === props.producto.id);
    window.addEventListener('resize', updateWindowWidth);
    updateWindowWidth();
    routeimage.value = props.image && props.image.length > 0 ? `/storage/${props.image[0].file_path}/${props.image[0].file}`: '';
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateWindowWidth);
});

const closeCantP = () => {
    showPopup.value = false;
};

const agregarAlCarrito = () => {
    if (stock.value === 0) {
        alert("Este producto no tiene stock disponible.");
        return; // Salimos de la función si no hay stock
    }
    const product = JSON.parse(localStorage.getItem('producto')) || [];
    const index = product.findIndex((item) => item.id === id.value);
    if (index !== -1) {
        const nuevaCantidad = product[index].quantity + quantity.value;

        if (nuevaCantidad > stock.value) {
            alert("No puedes agregar más de lo disponible en stock.");
            return; // Salimos si se supera el stock
        }
        product[index].quantity += quantity.value;
        product[index].subtotal = product[index].price * product[index].quantity;
        localStorage.setItem('producto', JSON.stringify(product));
        closeCantP();
        return;
    }else{
        product.push({
            id: id.value,
            name: nameproduct.value,
            pricedolar: pricedolar.value,
            pricesoles: precioEnSoles.value,
            quantity: quantity.value,
            subtotaldolar: pricedolar.value * quantity.value,
            subtotalsoles: precioEnSoles.value * quantity.value,
            specification: specification.value,
            valoration: valoration.value,
            filter_items: filter_items.value,
            stock: stock.value,
            routeimage: routeimage.value,
        });
        localStorage.setItem('producto', JSON.stringify(product));
        closeCantP();
        window.location.reload();
    }
};
</script>

<template>
    <div class="border border-gray-300 border-opacity-1 border-0.5 p-2 bg-white w-full rounded-lg">
        <div class="flex items-center">
            <Link :href="route('productid', { slug: producto.slug })" class="p-1" >
                <img :src="routeimage" alt="Imagen" class="w-32 h-full rounded" /> <!-- Usa la propiedad image del producto -->
            </Link>
            <p class="text-xs text-gray-900 text-left font-medium "> {{ producto.name.length > 80 ? (producto.name.slice(0, 77) + ' ...').charAt(0) + (producto.name.slice(0, 77) + ' ...').slice(1) : producto.name.charAt(0) + producto.name.slice(1) }}
                <Link v-if="producto.name.length > 80"
                    :href="route('productid', { slug: producto.slug })" class="text-blue font-medium">Ver más
                </Link>
            </p>
        </div>

        <div class="text-sm text-gray-700 flex flex-col">
            <div class="flex flex-row items-center w-full">
                <span class="w-full text-lg font-bold text-rojo text-center">$ {{ producto.price }}</span>
                <span class="w-full text-lg font-bold">S/. {{ precioEnSoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                <p v-if="producto.discount>0" class="flex flex-row items-center">
                    <span>Descuento:</span>
                    <span class="text-orange-500 text-lg">{{ producto.discount }}%</span>
                </p>
            </div>
            <form class="w-full flex p-1"> <!-- Modificado aquí -->
                <button @click="agregarAlCarrito"
                    class="w-full rounded-lg bg-cremaclaro p-2 font-bold transition hover:text-white flex items-center justify-center">
                    <span class="">AGREGAR AL CARRITO</span>
                </button>
            </form>
        </div>
    </div>
</template>
