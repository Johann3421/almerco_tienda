<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from "vue";
import cartCompra from '@/Pages/Web/Carrito/CartCompra.vue';
import CartPromo from '@/Pages/Web/Carrito/CartPromo.vue';
import imagecheck from "@assets/img/iconocheck.png";
import { useOrderStore } from '@stores/OrderStore';

const props = defineProps({
    images: Object,
    products: Array
});
// Información para facturación
const seccionActual = ref('resumencompra');
const tipoComprobante = ref('boleta');
const imagenesProducto = ref([]);
const imagenesProductoOnPromotion = ref([]);
const celular = ref('');
const nroDocumento = ref('');
const nombrecompleto = ref('');
const direccion = ref('');
const email = ref('');
const emailGuardado = ref('');
const tipoDocumento = ref('dni');
const limpiarcampos = () => {
    celular.value = '';
    nroDocumento.value = '';
    nombrecompleto.value = '';
    direccion.value = '';
    email.value = '';
};
const orderStore = useOrderStore();
const order = ref({
    id: "",
    customer_name: "",
    customer_email: "",
    customer_phone: "",
    customer_address: "",
    customer_document_number: "",
    customer_document_type: 1,
    order_status: "",
    order_status_update: "Cancelado",
    order_code: "",
    order_total: 0,
    observation: "",
    delivery_date: "",
    active: true,
    items: [
        {
            id: 0,
            uuid: crypto.randomUUID(),
            product_id: 0,
            amount: 0,
            unit_price: 0,
            unit_stock: 0,
            unit_name: "",
            total: 0
        }
    ]
})
//Load
const isLoading = ref(false);
const orderCode = ref('');
const whatsappLink = computed(() => {
    const encodedProductName = encodeURIComponent(`*${orderCode.value}*`);
    return `https://wa.me/51991375813?text=¡Hola!%20,%20quiero%20saber%20sobre%20mi%20Orden%20de%20Compra%20:%0A${encodedProductName}`;
});
const limpiarfacturacion = () => {
    if (tipoComprobante.value === 'boleta') {
        if (nroDocumento.value.length !== 8) {
            nroDocumento.value = '';
            nombrecompleto.value = '';
            tipoDocumento.value = 'ruc';
        }
    } else {
        if (nroDocumento.value.length !== 11) {
            nroDocumento.value = '';
            nombrecompleto.value = '';
            tipoDocumento.value = 'dni';
        }
    }
};

const mostrarSeccion = (seccion) => {
    seccionActual.value = seccion;
};
const sections = ref([
    { name: 'resumencompra', label: 'RESUMEN DE COMPRA', iconClass: 'mdi mdi-numeric-1-box-outline text-4xl' },
    { name: 'datoscliente', label: 'DATOS DEL CLIENTE', iconClass: 'mdi mdi-numeric-2-box-outline text-4xl' },
    { name: 'pago', label: 'ENVIO Y PAGO', iconClass: 'mdi mdi-numeric-3-box-outline text-4xl' }
]);

const cantCarts = ref(0);
const productos = ref([]);
const products = ref([]);
products.value = props.products;
const subtotalsoles = ref(0);
// Tipo de IGV: 'mas', 'incluido', o 'exonerada'
const tipoIGV = ref("exonerada");
const obtenerProductos = () => {
    productos.value = JSON.parse(localStorage.getItem('producto')) || [];
    cantCarts.value = productos.value.length;
    subtotalsoles.value = productos.value.reduce((acc, item) => acc + item.subtotalsoles, 0);
};
const calcularIGVMas = (subtotal) => {
    return (subtotal * 0.18).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const calcularIGVIncluido = (subtotal) => {
    const igv = subtotal * (1 - 1 / 1.18);
    return igv.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const calcularSubtotalIncluido = (subtotal) => {
    const igv = parseFloat(calcularIGVIncluido(subtotal));
    return (subtotal - igv).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const calcularTotal = (subtotal) => {
    if (tipoIGV.value === "mas") {
        return (parseFloat(subtotal) + parseFloat(calcularIGVMas(subtotal))).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else if (tipoIGV.value === 'incluido') {
        return (parseFloat(subtotal)).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        return parseFloat(subtotal).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
const cargarImagenesProductosOnPromotion = () => {
    if (props.images && props.images.length > 0) {
        products.value.forEach(producto => {
            const imagenes = props.images.filter(imagen => imagen.product_id === producto.id);
            imagenesProductoOnPromotion.value.push({ product_id: producto.id, imagenes });
        });
    }
};
const getImagenesProductoOnPromotion = (product_id) => {
    const imagenes = imagenesProductoOnPromotion.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};

const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find(item => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};
const ChangeBoleta = () => {
    if (tipoComprobante.value === 'factura') {
        tipoComprobante.value = 'boleta';
    }
    limpiarfacturacion();
};
const ChangeFactura = () => {
    if (tipoComprobante.value === 'boleta') {
        tipoComprobante.value = 'factura';

    }
    limpiarfacturacion();
};
// Definir errores para validación
const errores = ref({
    nroDocumento: '',
    celular: '',
    nombrecompleto: '',
    direccion: '',
    email: ''
});

const validarCampos = () => {
    errores.value.nroDocumento = nroDocumento.value ? '' : 'El número de documento es obligatorio';
    errores.value.celular = celular.value ? '' : 'El número de celular es obligatorio';
    errores.value.nombrecompleto = nombrecompleto.value ? '' : 'El nombre completo es obligatorio';
    errores.value.direccion = direccion.value ? '' : 'La dirección es obligatoria';
    errores.value.email = email.value ? '' : 'El correo es obligatorio';

    // Verificar si todos los campos son válidos
    return Object.values(errores.value).every(error => error === '');
};

const generarOrdenCompra = async () => {
    if (!validarCampos()) {
        return; // Si la validación falla, no continuar
    }
    // Iniciar el loader
    isLoading.value = true;
    // Asignar valores al pedido
    order.value.customer_name = nombrecompleto.value;
    order.value.customer_email = email.value;
    order.value.customer_phone = celular.value;
    order.value.customer_address = direccion.value;
    order.value.customer_document_number = nroDocumento.value;
    order.value.customer_document_type = tipoComprobante.value === 'boleta' ? 1 : 2;
    order.value.order_status = 'Pendiente de Pago';
    order.value.order_code = Math.random().toString(36).substring(7);
    order.value.observation = 'Orden de compra generada desde la web';
    order.value.delivery_date = new Date().toISOString();
    order.value.active = true;
    emailGuardado.value = email.value; //guardar en variable para mostrar en pantalla
    // Asegúrate de que order_total sea un número válido
    const totalString = calcularTotal(subtotalsoles.value); // Obtén el total como string
    const total = parseFloat(totalString.replace(/,/g, '')); // Elimina comas y convierte a número

    // Verifica que el total sea un número válido
    if (isNaN(total)) {
        console.error('El total calculado no es un número válido:', totalString);
        return; // Abortamos si el total no es válido
    }

    order.value.order_total = total;

    // Mapeo de productos del carrito a la estructura requerida por el modelo de order
    const filteredItems = productos.value.map(producto => ({
        product_id: producto.id,
        amount: producto.quantity,
        unit_price: producto.pricesoles,
        total: producto.subtotalsoles,
        unit_name: "unidad"
    }));

    // Verifica que haya productos
    if (filteredItems.length === 0) {
        console.error('No hay productos en el carrito');
        return;
    }

    // Asignar los ítems al pedido
    order.value.items = filteredItems;

    // Asegúrate de que el pedido tenga todos los campos requeridos
    const orderData = {
        ...order.value,
        items: filteredItems,
    };

    try {
        // Enviar datos al backend
        const response = await axios.post(route('pedidos.store'), orderData);

        if (response.status === 201) {
            console.log('Orden de compra generada con éxito', response.data);
            orderCode.value = response.data.order.order_code;
            await enviarCorreoConfirmacion(response.data.order.id);
            orderStore.clearOrderItems();
            limpiarcampos();
            localStorage.clear();
            mostrarSeccion('pago');
        }
    } catch (error) {
        const errorMessage = error.response ? error.response.data.message || 'Error desconocido' : 'Error de conexión';
        alert(`Error al generar la orden de compra: ${errorMessage}`);

        if (error.response && error.response.data.errors) {
            alert(`Errores de validación: ${JSON.stringify(error.response.data.errors)}`);
        }
    } finally {
        // Detener el loader
        isLoading.value = false;
    }
};
const enviarCorreoConfirmacion = async (orderId) => {
    try {
        const response = await axios.post(`/orders/${orderId}/send-confirmation`);
        console.log('Correo enviado con éxito:', response.data);
    } catch (error) {
        const errorMessage = error.response ? error.response.data.message || 'Error desconocido' : 'Error de conexión';
        alert(`Error al enviar el correo: ${errorMessage}`);
    }
};

const verificarstorage = () => {
    if (productos.value.length === 0) {
        console.error('No hay productos en el carrito');
        // Redirigir al usuario al inicio de la página
        window.location.href = '/'; // Cambia '/' por la ruta que desees
        return;
    }
};
onMounted(() => {
    obtenerProductos();
    verificarstorage();
    cargarImagenesProductos();
    cargarImagenesProductosOnPromotion();
    mostrarSeccion('resumencompra');
});

</script>

<template>
    <div class="lg:mt-44 w-full py-5 md:px-10 lg:px-2 2xl:px-20" id="fixedContent">
        <div class="grid bg-gray-100">
            <div class="hidden md:flex flex-col md:flex-row grid-cols-1 justify-around w-full gap-2 border-t">
                <span
                    class="p-3 w-full text-center text-plomomediooscuro rounded hover:text-greenhad flex items-center justify-center"
                    :class="{ 'text-green-700  font-bold': seccionActual === 'resumencompra' }"><i
                        class="mdi mdi-numeric-1-box-outline text-4xl"></i><span>RESUMEN DE COMPRA</span></span>
                <span
                    class="p-3 w-full text-center text-plomomediooscuro rounded hover:text-greenhad flex items-center justify-center"
                    :class="{ 'text-green-700 font-bold': seccionActual === 'datoscliente' }"><i
                        class="mdi mdi-numeric-2-box-outline text-4xl"></i><span>DATOS DEL CLIENTE</span></span>
                <span
                    class="p-3 w-full text-center text-plomomediooscuro rounded hover:text-greenhad flex items-center justify-center"
                    :class="{ 'text-green-700  font-bold': seccionActual === 'pago' }"><i
                        class="mdi mdi-numeric-3-box-outline text-4xl"></i><span>ENVIO Y PAGO</span></span>
            </div>
            <div class="flex py-2 md:hidden">

                <div class="flex overflow-x-auto items-center w-full">
                    <span v-for="(section, index) in sections" :key="index"
                        class="p-3 w-full text-center text-plomomediooscuro rounded hover:text-rojo flex items-center justify-center"
                        :class="{ 'font-bold': seccionActual === section.name, 'hidden': seccionActual !== section.name }"
                        @click="mostrarSeccion(section.name)">
                        <i :class="section.iconClass"></i>
                        <span>{{ section.label }}</span>
                    </span>
                </div>

            </div>
            <div class="relative w-full">
                <div id="resumencompra" v-if="seccionActual === 'resumencompra'"
                    class="flex flex-col lg:flex-row justify-between lg:items-start w-full">
                    <div class="flex flex-col w-full justify-between p-2">
                        <div
                            class="hidden md:flex md:justify-between md:items-center w-full p-4 bg-gray-50 text-xl font-bold">
                            <p class="">Total {{ cantCarts }} productos</p>
                            <Link :href="route('web')" class="rounded-full text-greenhad"> Seguir comprando</Link>
                        </div>
                        <ul class="text-sm">
                            <cartCompra v-for="index in cantCarts" :key="index" :index="index"
                                :producto="productos[index - 1]" @producto-eliminado="obtenerProductos"
                                :images="productos[index - 1] ? getImagenesProducto(productos[index - 1].id) : []" />
                        </ul>
                    </div>

                    <div class="flex flex-col md:flex-row lg:flex-col items-center justify-center bg-gray-100 p-2">
                        <div
                            class="flex flex-col gap-2 items-center border rounded-md px-8 bg-white py-5 w-full md:w-1/2 lg:w-72">
                            <h1 class="font-bold">Subtotal de Compra</h1>
                            <div class="flex flex-col w-full md:w-60 text-center text-lg">
                                <div class="flex w-full justify-between"
                                    v-if="tipoIGV !== 'incluido' && subtotalsoles > 0">
                                    <span>Subtotal </span>
                                    <div class="flex flex-col">
                                        <span>S/. {{ subtotalsoles.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                            }) }} </span>
                                    </div>
                                </div>
                                <div class="flex w-full justify-between"
                                    v-if="tipoIGV === 'incluido' && subtotalsoles > 0">
                                    <span>Subtotal </span>
                                    <div class="flex flex-col">
                                        <span>S/. {{ calcularSubtotalIncluido(subtotalsoles) }} </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="flex w-full justify-between" v-if="tipoIGV === 'mas' && subtotalsoles > 0">
                                    <span>IGV </span>
                                    <span>S/. {{ calcularIGVMas(subtotalsoles) }} </span>
                                </div>
                                <div class="flex w-full justify-between"
                                    v-if="tipoIGV === 'incluido' && subtotalsoles > 0">
                                    <span>IGV </span>
                                    <span>S/. {{ calcularIGVIncluido(subtotalsoles) }} </span>
                                </div>
                                <div class="flex w-full justify-between" v-if="tipoIGV === 'exonerada'">
                                    <span>IGV </span>
                                    <span>S/. 0.00 </span>
                                </div>
                                <hr>
                                <div class="flex w-full justify-between font-bold">
                                    <span>Total </span>
                                    <span>S/. {{ calcularTotal(subtotalsoles) }} </span>
                                </div>
                            </div>
                            <div class="text-center">
                                <button href="#" @click="mostrarSeccion('datoscliente')"
                                    class="rounded-full border bg-rojo hover:bg-greenhad px-10 py-2 text-xs font-bold text-white ">FINALIZAR
                                    COMPRA</button>
                            </div>
                        </div>
                        <div class="">
                            <div class="md:w-80 lg:p-2">
                                <v-carousel hide-delimiters show-arrows="hover" cycle style="height: 100%;">
                                    <v-carousel-item v-for="(product, groupIndex) in products" :key="groupIndex">
                                        <div class="md:px-2 py-2 h-full">
                                            <CartPromo :producto="product"
                                                :image="getImagenesProductoOnPromotion(product.id)" />
                                        </div>
                                    </v-carousel-item>
                                </v-carousel>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="datoscliente" ref="form_create_order" v-if="seccionActual === 'datoscliente'"
                    class="flex flex-col gap-4 w-full p-5 bg-white border rounded-xl">
                    <h2 class="font-medium text-2xl text-center">Datos de Facturación</h2>
                    <div class="w-full flex flex-col md:flex-row justify-between gap-5 text-blue-600 font-bold">
                        <button
                            class="hover:bg-blue-700 hover:text-white px-5 py-3 rounded-lg border md:w-1/2 text-left"
                            :class="{ 'bg-blue-700 text-white': tipoComprobante === 'boleta' }"
                            @click="ChangeBoleta">BOLETA</button>
                        <button
                            class="hover:bg-blue-700 hover:text-white px-5 py-3 rounded-lg border md:w-1/2 text-left"
                            :class="{ 'bg-blue-700 text-white': tipoComprobante === 'factura' }"
                            @click="ChangeFactura">FACTURA</button>
                    </div>
                    <div class="w-full flex flex-col md:flex-row gap-5" id="datos">
                        <div class="flex flex-col gap-3 md:w-1/2">
                            <div class="flex flex-col md:flex-row w-full gap-2 justify-between">
                                <div class="flex flex-col gap-2 md:w-1/2" v-if="tipoComprobante === 'boleta'">
                                    <label for="nroDocumento">Dni:</label>
                                    <input type="text" id="nroDocumento" v-model="nroDocumento"
                                        class="w-full border p-3 rounded-xl" required maxlength="8">
                                    <span v-if="errores.nroDocumento" class="text-red-500">{{ errores.nroDocumento
                                        }}</span>
                                </div>
                                <div class="flex flex-col gap-2 md:w-1/2" v-if="tipoComprobante === 'factura'">
                                    <label for="nroDocumento">Ruc:</label>
                                    <input type="text" id="nroDocumento" v-model="nroDocumento"
                                        class="w-full border p-3 rounded-xl" required maxlength="11">
                                    <span v-if="errores.nroDocumento" class="text-red-500">{{ errores.nroDocumento
                                        }}</span>
                                </div>
                                <div class="flex flex-col gap-2 md:w-1/2">
                                    <label for="celular">Nro. de Celular:</label>
                                    <input type="text" id="celular" v-model="celular"
                                        class="w-full border p-3 rounded-xl" maxlength="12">
                                    <span v-if="errores.celular" class="text-red-500">{{ errores.celular }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2" v-if="tipoComprobante === 'boleta'">
                                <label for="nombrecompleto">Nombre Completo:</label>
                                <input type="text" id="nombrecompleto" v-model="nombrecompleto"
                                    class="w-full border p-3 rounded-xl">
                                <span v-if="errores.nombrecompleto" class="text-red-500">{{ errores.nombrecompleto
                                    }}</span>
                            </div>
                            <div class="flex flex-col gap-2" v-if="tipoComprobante === 'factura'">
                                <label for="nombrecompleto">Razón Social:</label>
                                <input type="text" id="nombrecompleto" v-model="nombrecompleto"
                                    class="w-full border p-3 rounded-xl">
                                <span v-if="errores.nombrecompleto" class="text-red-500">{{ errores.nombrecompleto
                                    }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col lg:w-1/2 gap-2">
                            <div class="flex flex-col gap-2">
                                <label for="direccion">Dirección:</label>
                                <input type="text" id="direccion" v-model="direccion"
                                    class="w-full border p-3 rounded-xl" required>
                                <span v-if="errores.direccion" class="text-red-500">{{ errores.direccion }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="email">Correo: (Donde se enviará el detalle de la compra)</label>
                                <input type="email" id="email" name="email" multiple v-model="email"
                                    class="w-full border p-3 rounded-xl" required>
                                <span v-if="errores.email" class="text-red-500">{{ errores.email }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                            <div class="text-xl font-bold mb-4">Cargando...</div>
                            <div class="loader-animation w-10 h-10 border-4 border-gray-200 border-t-blue-500 rounded-full animate-spin mx-auto"></div>
                        </div>
                    </div>
                    <div class="w-full flex flex-col xl:flex-row gap-2 justify-end">
                        <button class="border bg-blue-700 text-white rounded-xl px-10 py-2 text-lg hover:bg-blue-500 font-bold" @click="mostrarSeccion('resumencompra')">VOLVER</button>
                        <button class="border bg-green-600 text-white rounded-xl px-10 py-2 text-lg hover:bg-green-700 font-bold" @click="generarOrdenCompra">GUARDAR</button>
                        <button class="border bg-rojo text-white rounded-xl px-10 py-2 text-lg hover:bg-red-700 font-bold" @click="limpiarcampos">CANCELAR</button>
                    </div>
                </div>
                <div id="pago" v-if="seccionActual === 'pago'" class="w-full h-full py-10 px-4 bg-white flex flex-col justify-between items-center gap-2">
                    <div class="flex flex-col md:flex-row items-center justify-center h-60 md:h-full gap-2">
                        <div class="w-full md:w-auto md:text-center">
                            <img class="w-20" :src="imagecheck" alt="">
                        </div>
                        <div class="flex flex-col justify-start items-start text-left leading-tight">
                            <div class="items-start text-lg font-bold leading-tight">
                                <p>Su pedido fue</p>
                                <p>registrado exitosamente</p>
                            </div>
                            <p class="leading-tight">Nota: Los datos del pedido han sido enviados al <br> correo: <span class="text-rojo font-medium">{{ emailGuardado }}</span></p>
                        </div>
                    </div>
                    <div class="md:p-3 w-full text-center">
                        <a
                            v-if="orderCode"
                            :href="whatsappLink"
                            class="w-full bg-green-500 text-white md:text-lg font-bold py-3 px-5 md:px-10 rounded-full"
                            target="_blank">
                            <i class="mdi mdi-whatsapp font-bold text-2xl"></i>
                            Consulta vía WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Estilos opcionales para resaltar el elemento activo */
span {
    cursor: pointer;
}

span.active {
    font-weight: bold;
}
</style>
