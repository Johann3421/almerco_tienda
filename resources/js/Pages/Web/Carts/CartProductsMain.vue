<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, defineProps, computed } from "vue";
import PapupCarrito from '@/Pages/Web/Carrito/PapupCarrito.vue';
import { useSettingStore } from '@stores/SettingStore';
const props = defineProps({
    producto: Object, // Define el tipo de dato para el prop producto como un objeto
    images: Object, // Define el tipo de dato para el prop imagenes como un array
    brand: Object,
    totalimages:Object
});
const settingsGlobal = useSettingStore();
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
const sku = ref('');
const partNumber = ref('');
const prodDescription = ref("");

id.value = props.producto.id;
filter_items.value = props.producto.filter_items;
valoration.value = props.producto.valoration;
specification.value = props.producto.specification;
nameproduct.value = props.producto.name;
stock.value = props.producto.stock;
pricedolar.value = props.producto.price;
sku.value = props.producto.sku;
partNumber.value = props.producto.part_number;
prodDescription.value = props.producto.description;
const windowWidth = ref(window.innerWidth);

const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};
const precioEnSoles = computed(() => {
    pricesoles.value = (props.producto.price * parseFloat(settingsGlobal.getDolarValue)).toFixed(2);
    return pricesoles.value;
});
const productos = ref([]);
const cantPedidos = ref(0);
const subtotalsoles = ref(0);
const showPopup = ref(false);
const obtenerCantidadPedidos = () => {
    productos.value = JSON.parse(localStorage.getItem('producto')) || [];
    cantPedidos.value = 0; // Restablecer a cero
    subtotalsoles.value = 0; // Restablecer a cero

    productos.value.forEach((item) => {
        cantPedidos.value += item.quantity;
        subtotalsoles.value += item.subtotalsoles;
    });
};
const closePopup = () => {
    showPopup.value = false;
    obtenerCantidadPedidos();
};
onMounted(() => {
    maxQuantity.value = props.producto.stock;
    const productosEnCarrito = JSON.parse(localStorage.getItem('producto')) || [];
    productoEnCarrito.value = productosEnCarrito.some(item => item.id === props.producto.id);
    window.addEventListener('resize', updateWindowWidth);
    updateWindowWidth();
    routeimage.value = props.images && props.images.length > 0 ? `/storage/${props.images[0].file_path}/${props.images[0].file}`: '';
    obtenerCantidadPedidos();
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateWindowWidth);
});

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const agregarAlCarrito = () => {
    if (stock.value === 0) {
        alert("Este producto no tiene stock disponible.");
        return; // Salimos de la función si no hay stock
    }
    togglePopup();
    const product = JSON.parse(localStorage.getItem("producto")) || [];
    const index = product.findIndex((item) => item.id === id.value);
    if (index !== -1) {
        const nuevaCantidad = product[index].quantity + quantity.value;

        if (nuevaCantidad > stock.value) {
            alert("No puedes agregar más de lo disponible en stock.");
            return; // Salimos si se supera el stock
        }
        product[index].quantity += quantity.value;
        product[index].subtotal = product[index].price * product[index].quantity;
        localStorage.setItem("producto", JSON.stringify(product));
        // closeCantP();
        return;
    } else {
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
        sku: sku.value,
        part_number: partNumber.value,
        description: prodDescription.value,
        routeimage: routeimage.value
        });
        localStorage.setItem("producto", JSON.stringify(product));
        // closeCantP();
    }
};
</script>

<template>
    <div class="group relative overflow-hidden m-1 border border-gray-300 border-opacity-1 border-0.5 px-4 py-2 bg-white w-full"
        :class="{ 'sm:w-80': windowWidth <= 7680, 'w-80': windowWidth > 7680 }">
        <div class="h-8 w-full">
            <img class="h-full" v-if="brand" :src="`/storage/${brand.file_path}/${brand.file}`" alt="Marca">
        </div>
        <Link :href="route('productid', { slug: producto.slug })" >
            <img :src="routeimage"
            alt="Imagen de Producto" class="w-full h-64 sm:w-80 object-cover rounded" /> <!-- Usa la propiedad image del producto -->
        </Link>

        <div class="relative flex justify-between items-star border-gray-100 bg-white h-48 py-1">
            <div class="flex flex-col w-full gap-2" @click="closeCantP">
                <h3 class="px-2 text-sm text-gray-900 text-center font-bold h-10"> {{ producto.name.length > 50 ? (producto.name.slice(0, 50)).charAt(0) + (producto.name.slice(0, 50)).slice(1) : producto.name.charAt(0) + producto.name.slice(1) }}
                    <Link v-if="producto.name.length > 50"
                        :href="route('productid', { slug: producto.slug })" class="text-blue font-medium">...Ver
                    </Link>
                </h3>
                <div class="flex flex-col px-5">
                    <span class="text-sm text-left"><span class="font-medium">Número de parte:</span> {{ producto.part_number }}</span>
                    <span class="text-sm text-left"><span class="font-medium"></span> {{ producto.sku }}</span>
                    <span class="text-sm text-left"><span class="font-medium">Stock:</span> {{ producto.stock }}</span>
                </div>
                <div class="p-1 text-sm text-gray-700 flex flex-col">
                    <div class="absolute bottom-12 flex flex-row justify-center items-center w-full">
                        <span class="w-full text-xl font-bold text-rojo text-center">$ {{ producto.price }}</span>
                        <p v-if="producto.discount>0" class="flex flex-row items-center">
                            <span>Descuento:</span>
                            <span class="text-orange-500 text-xl">{{ producto.discount }}%</span>
                        </p>
                    </div>
                    <div class="absolute w-full flex bottom-0 left-0 p-1">
                        <button @click="agregarAlCarrito"
                            class="relative w-full rounded-lg bg-encabezado p-2 text-white text-lg font-bold transition hover:scale-105 flex items-center justify-center">
                            <span class="text-xl font-bold">S/. {{ precioEnSoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                            <span class="absolute right-1 rounded-full bg-white px-1"><i class="mdi mdi-plus text-2xl"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <PapupCarrito v-if="showPopup" @close="closePopup" :images="totalimages" />
    </div>
</template>
