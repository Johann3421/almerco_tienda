<script setup>
import { Link } from '@inertiajs/vue3';
import cartMobil from '@/Pages/Web/Carrito/CartMobil.vue';
import { ref, defineEmits, defineProps, onMounted } from "vue";

const emit = defineEmits(['close']);
const props = defineProps({
    images: Object
});

const productos = ref([]);
const subtotalsoles = ref(0);
const cantCarts = ref(0);
const imagenesProducto = ref([]);
// Tipo de IGV: 'mas', 'incluido', o 'exonerada'
const tipoIGV = 'exonerada'; // Puedes cambiar este valor según corresponda

const showDiv = ref(true);

const closePopup = () => {
    emit('close');
    window.location.reload();
};
const toggleDetalle = () => {
    const detalle = document.getElementById('detalle');
    detalle.classList.toggle('hidden');
};

const obtenerProductos = () => {
    productos.value = JSON.parse(localStorage.getItem('producto')) || [];
    productos.value.reverse();
    cantCarts.value = productos.value.length;
    subtotalsoles.value = productos.value.reduce((acc, item) => acc + item.subtotalsoles, 0);
};

const calcularIGVMas = (subtotal) => {
  return (subtotal * 0.18).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const calcularIGVIncluido = (subtotal) => {
  const igv = subtotal * (1 - 1 / 1.18);
  return igv.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
const calcularSubtotalIncluido = (subtotal) => {
    const igv = parseFloat(calcularIGVIncluido(subtotal));
    return (parseFloat(subtotal) - igv).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
const calcularTotal = (subtotal) => {
  if (tipoIGV === 'mas') {
    return (parseFloat(subtotal) + parseFloat(calcularIGVMas(subtotal))).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  } else if (tipoIGV === 'incluido') {
    return parseFloat(subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  } else {
    return parseFloat(subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
};
// Método para cargar las imágenes de cada producto
const cargarImagenesProductos = () => {
    if (props.images && props.images.length > 0) {
        productos.value.forEach(producto => {
            const imagenes = props.images.filter(imagen => imagen.product_id === producto.id);
            imagenesProducto.value.push({ product_id: producto.id, imagenes });
        });
    }
};

// Método para obtener las imágenes asociadas con un product_id específico
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};

onMounted(() => {
    obtenerProductos();
    cargarImagenesProductos();
});
</script>

<template>
    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <div v-if="showDiv" class="fixed h-full w-full sm:w-1/2 lg:w-1/3 border px-0 py-0 right-0"
            aria-modal="true" role="dialog" tabindex="-1">
            <div class="flex flex-col justify-between h-screen w-full bg-plomoclaro">
                <div class="flex justify-between bg-naranja items-center h-16 px-5">
                    <button @click="closePopup" class="text-white transition hover:scale-110">
                        <span class="sr-only">Close cart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h1 class="text-white text-xl font-bold">Mi carrito</h1>
                </div>
                <div class="overflow-y-auto h-screen">
                    <ul class="bg-fondoback text-sm">
                        <cartMobil v-for="(producto, index) in productos" :key="producto.id" :index="index + 1" :producto="producto" @producto-eliminado="obtenerProductos" :images="getImagenesProducto(producto.id)" />
                    </ul>

                    <div class="flex flex-col w-full sticky bottom-0 p-2 text-center bg-fondoback">
                        <div class="hidden border-b border-black bg-white w-full" id="detalle">
                            <div class="flex px-4 py-2 w-full justify-between" v-if="tipoIGV !== 'incluido' && subtotalsoles > 0">
                              <span class="font-bold">Subtotal: </span>
                              <div class="flex flex-col">
                                <span>S/.{{ subtotalsoles.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }} </span>
                              </div>
                            </div>
                            <div class="flex px-4 py-2 w-full justify-between" v-if="tipoIGV === 'incluido' && subtotalsoles > 0">
                                <span class="font-bold">Subtotal: </span>
                                <div class="flex flex-col">
                                  <span>S/.{{ calcularSubtotalIncluido(subtotalsoles) }} </span>
                                </div>
                              </div>
                            <div class="flex px-4 py-2 w-full justify-between" v-if="tipoIGV === 'mas' && subtotalsoles > 0">
                              <span class="font-bold">IGV: </span>
                              <span>S/.{{ calcularIGVMas(subtotalsoles) }} </span>
                            </div>
                            <div class="flex px-4 py-2 w-full justify-between" v-if="tipoIGV === 'incluido' && subtotalsoles > 0">
                              <span class="font-bold">IGV: </span>
                              <span>S/.{{ calcularIGVIncluido(subtotalsoles) }} </span>
                            </div>
                            <div class="flex px-4 py-2 w-full justify-between" v-if="tipoIGV === 'exonerada'">
                              <span class="font-bold">IGV: </span>
                              <span>S/.0.00 </span>
                            </div>
                            <div class="flex px-4 py-2 w-full justify-between">
                              <span class="font-bold">Total: </span>
                              <span>S/.{{ calcularTotal(subtotalsoles) }} </span>
                            </div>
                          </div>
                        <div class="flex flex-col text-xl font-bold">
                            <button @click="toggleDetalle" class="rounded border border-orange-600 px-5 py-3 text-orange-600 transition hover:ring-1 hover:ring-orange-400 mb-2 bg-white">Ver detallado</button>
                            <Link :href="cantCarts > 0 ? route('baysid') : ''" class="rounded px-5 py-3 text-white transition hover:bg-gray-600" :class="{ 'bg-gray-700': cantCarts > 0, 'bg-gray-400 cursor-not-allowed': cantCarts === 0 }">
                                Ir al Carrito
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
