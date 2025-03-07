<script setup>
import { Link, usePage } from "@inertiajs/vue3"; // Importa usePage
import { ref, defineProps, onMounted, computed, watch } from "vue";
import CartMain from "@/Pages/Web/Carts/CartProductsMain.vue";
import PapupCarrito from '@/Pages/Web/Carrito/PapupCarrito.vue';
import { useSettingStore } from "@stores/SettingStore";

// Define las props del componente
const props = defineProps({
    producto: Object,
    productimages: Object,
    images: Object,
    products: Array,
    subgroups: Array,
    productref: Object,
    subgroup: Object,
    brands: Array,
    productSpecifications: Array,
});

const settingsGlobal = useSettingStore();
const id = ref(0);
const pricesoles = ref(0);
const pricedolar = ref(0);
const stock = ref(0);
const quantity = ref(1);
const maxQuantity = ref(1);
const nameproduct = ref("");
const specification = ref("");
const valoration = ref(0);
const filter_items = ref([]);
const imagenesProducto = ref([]);
const products = ref([]);
const categoriaref = ref({});
const categoriarefslug = ref({});
const gruporef = ref({});
const gruporefslug = ref({});
const subgruporef = ref({});
const subgruporefslug = ref({});
const sku = ref("");
const partNumber = ref("");
const prodDescription = ref("");
const prodSpecification = ref("");
const imagenesBrand = ref([]);
const routeimage = ref('');
const manufacturer_url = ref('');
const productespecificaciones = ref([]);

// Inicialización de datos
categoriaref.value = capitalizeFirstLetter(props.productref[0].category.name);
categoriarefslug.value = props.productref[0].category.slug;
gruporef.value = capitalizeFirstLetter(props.productref[0].group.name);
gruporefslug.value = props.productref[0].group.slug;
subgruporef.value = capitalizeFirstLetter(props.subgroup[0].name);
subgruporefslug.value = props.subgroup[0].slug;
id.value = props.producto.id;
filter_items.value = props.producto.filter_items;
valoration.value = props.producto.valoration;
specification.value = props.producto.specification;
nameproduct.value = props.producto.name;
stock.value = props.producto.stock;
sku.value = props.producto.sku;
partNumber.value = props.producto.part_number;
prodDescription.value = props.producto.description;
prodSpecification.value = props.producto.specification;
manufacturer_url.value = props.producto.manufacturer_url;
productespecificaciones.value = props.productSpecifications;
pricedolar.value = props.producto.price.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const precioEnSoles = computed(() => {
    pricesoles.value = (pricedolar.value * parseFloat(settingsGlobal.getDolarValue)).toFixed(2);
    return pricesoles.value;
});

// Función para capitalizar la primera letra de cada palabra
function capitalizeFirstLetter(string) {
    if (!string) return "";
    return string
        .toLowerCase()
        .split(" ")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
}

// Cargar imágenes del producto
const items = ref([]);
if (typeof props.productimages === "object" && Object.keys(props.productimages).length > 0) {
    items.value = Object.values(props.productimages).map((image) => ({
        src: `/storage/${image.file_path}/${image.file}`,
    }));
} else {
    console.error("No se han encontrado imágenes para el producto");
}

// Resaltar el título en el buscador con palabras clave estratégicas
const highlightTitle = (title) => {
    const keywords = [
        "comprar", 
        "oferta", 
        "envío gratis", 
        "mejor precio", 
        "Huánuco", 
        "tienda en Huánuco", 
        "productos en Huánuco", 
        "venta en Huánuco", 
        "entrega en Huánuco", 
        "descuentos en Huánuco", 
        "promociones en Huánuco", 
        "comprar en línea", 
        "compra segura", 
        "calidad garantizada", 
        "precios bajos", 
        "ofertas exclusivas", 
        "envío rápido", 
        "atención al cliente", 
        "pago seguro", 
        "productos originales", 
        "marcas reconocidas"
    ];

    let highlightedTitle = title;
    keywords.forEach(keyword => {
        const regex = new RegExp(`(${keyword})`, "gi");
        highlightedTitle = highlightedTitle.replace(regex, "<strong>$1</strong>");
    });
    return highlightedTitle;
};

nameproduct.value = highlightTitle(nameproduct.value);

// Función para agregar al carrito
const agregarAlCarrito = () => {
    if (stock.value === 0) {
        alert("Este producto no tiene stock disponible.");
        return;
    }
    togglePopup();
    const product = JSON.parse(localStorage.getItem("producto")) || [];
    const index = product.findIndex((item) => item.id === id.value);
    if (index !== -1) {
        const nuevaCantidad = product[index].quantity + quantity.value;
        if (nuevaCantidad > stock.value) {
            alert("No puedes agregar más de lo disponible en stock.");
            return;
        }
        product[index].quantity += quantity.value;
        product[index].subtotal = product[index].price * product[index].quantity;
        localStorage.setItem("producto", JSON.stringify(product));
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
            routeimage: routeimage.value,
            manufacturer_url: manufacturer_url.value,
        });
        localStorage.setItem("producto", JSON.stringify(product));
    }
};

// Funciones para incrementar y decrementar la cantidad
const incrementQuantity = () => {
    if (quantity.value < maxQuantity.value) {
        quantity.value++;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

// Cargar imágenes de productos y marcas
const cargarImagenesProductos = () => {
    if (props.images && props.images.length > 0) {
        products.value.forEach((producto) => {
            const imagenes = props.images.filter((imagen) => imagen.product_id === producto.id);
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

// Obtener imágenes del producto y la marca
const getImagenesProducto = (product_id) => {
    const imagenes = imagenesProducto.value.find((item) => item.product_id === product_id);
    return imagenes ? imagenes.imagenes : [];
};

const getImagenesBrand = (product_id) => {
    const brand = imagenesBrand.value.find(item => item.product_id === product_id);
    return brand ? brand.imagen : null;
};

// Funciones para el zoom de imágenes
const zoomed = ref(false);
const zoomedImageSrc = ref("");

const showZoomedImage = (src) => {
    zoomedImageSrc.value = src;
    zoomed.value = true;
};

const closeZoomedImage = () => {
    zoomed.value = false;
};

// Cálculo de productos por fila
const productosPorFila = computed(() => {
    const tamañoDelCart = 350;
    return Math.floor(window.innerWidth / tamañoDelCart);
});

// Enlace de WhatsApp
const whatsappLink = computed(() => {
    const encodedProductName = encodeURIComponent(`*${nameproduct.value}*`);
    return `https://wa.me/51991375813?text=¡Hola!%20,%20quiero%20comprar%20este%20producto:%0A${encodedProductName}`;
});

// Agrupación de productos relacionados
const productGroups = computed(() => {
    const groups = [];
    for (let i = 0; i < products.value.length; i += productosPorFila.value) {
        const productorelacionado = products.value.slice(i, i + productosPorFila.value);
        const firstWordProduct = productorelacionado[0].name.split(' ')[0].toLowerCase();
        const firstWordNameProduct = nameproduct.value.split(' ')[0].toLowerCase();

        if (firstWordProduct === firstWordNameProduct) {
            groups.push(productorelacionado);
        }
    }
    return groups;
});

// Filtrado de productos relacionados
const filteredProducts = computed(() => {
    const selectedProduct = nameproduct.value.toLowerCase();
    const firstWord = selectedProduct.split(' ')[0];

    const allProductsInGroups = productGroups.value.flat();
    const filtered = allProductsInGroups.filter(product =>
        product?.name?.toLowerCase().startsWith(firstWord)
    );
    return filtered;
});

// Observar cambios en el tamaño de la ventana
watch(
    () => window.innerWidth,
    () => {
        productosPorFila.value = Math.floor(window.innerWidth / tamañoDelCart);
    }
);

// Mostrar/ocultar productos adicionales
const showMore = ref(true);
const toggleMore = () => {
    showMore.value = !showMore.value;
};

// Obtener cantidad de pedidos y subtotal
const productos = ref([]);
const cantPedidos = ref(0);
const subtotalsoles = ref(0);
const showPopup = ref(false);

const obtenerCantidadPedidos = () => {
    productos.value = JSON.parse(localStorage.getItem('producto')) || [];
    cantPedidos.value = 0;
    subtotalsoles.value = 0;

    productos.value.forEach((item) => {
        cantPedidos.value += item.quantity;
        subtotalsoles.value += item.subtotalsoles;
    });
};

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const closePopup = () => {
    showPopup.value = false;
    obtenerCantidadPedidos();
};

// Función para actualizar el título de la página
const updatePageTitle = () => {
    setTimeout(() => {
        const newTitle = `${nameproduct.value} - ${partNumber.value} | SEKAI TECH`;
        document.title = newTitle;
        console.log("Título actualizado:", newTitle);
    }, 500); // Pequeño retraso para asegurarnos de que no se sobrescriba
};

// Llamar a updatePageTitle cuando el componente se monta
onMounted(() => {
    products.value = props.products;
    maxQuantity.value = stock.value;
    cargarImagenesProductos();
    toggleMore();
    routeimage.value = props.images && props.images.length > 0 ? `/storage/${props.images[0].file_path}/${props.images[0].file}` : '';
    obtenerCantidadPedidos();
    cargarImagenesBrands();
    updatePageTitle(); // Actualizar el título de la página
});

</script>

<template>
    <div class="flex flex-col md:gap-10">
        <div class="relative p-5 md:px-6 xl:px-20 2xl:px-32">
            <div class="py-5 text-md text-plomomediooscuro">
                <Link class="hover:text-azul" :href="route('web')"><i class="mdi mdi-home mr-1"></i>Inicio</Link>
                <span class="mx-1 font-medium">/</span>
                <Link class="hover:text-azul" :href="route('categoryid', { slug: categoriarefslug })">
                {{ categoriaref }}
                </Link>
                <span class="mx-1 font-medium">/</span>
                <Link class="hover:text-azul" :href="route('groupid', { slug: gruporefslug })">
                {{ gruporef }}
                </Link>
                <span class="mx-1 font-medium">/</span>
                <Link class="hover:text-azul" :href="route('subgroupid', { slug: subgruporefslug })">
                {{ subgruporef }}
                </Link>
                <span class="mx-1 font-medium">/</span>
                <span class="font-bold">{{ nameproduct }}</span>
            </div>
            <div class="flex flex-col lg:flex-row items-star w-full">
                <div class="flex flex-col lg:w-1/2">
                    <div class="flex flex-col justify-start bg-white p-1 w-full">
                        <div class="hidden md:block">
                            <v-carousel hide-delimiters style="height: 400px; width: 100%">
                                <v-carousel-item v-for="(item, i) in items" :key="i">
                                    <div class="h-full px-10 py-7">
                                        <img :src="item.src" :alt="`Imagen de ${nameproduct} - ${partNumber}`"
                                            class="w-full h-full object-contain hover:scale-105 zoom-effect"
                                            @click="showZoomedImage(item.src)" />
                                    </div>
                                </v-carousel-item>
                            </v-carousel>
                        </div>
                        <div class="block md:hidden">
                            <v-carousel hide-delimiters style="height: 350px; width: 100%">
                                <v-carousel-item v-for="(item, i) in items" :key="i">
                                    <div class="h-full p-5">
                                        <img :src="item.src" :alt="`Imagen de ${nameproduct} - ${partNumber}`"
                                            class="w-full h-full object-contain hover:scale-105 zoom-effect"
                                            @click="showZoomedImage(item.src)" />
                                    </div>
                                </v-carousel-item>
                            </v-carousel>
                        </div>
                        <div class="hidden md:flex justify-between p-5 h-1/4 overflow-x-auto">
                            <img class="p-1 border w-32 cursor-pointer rounded flex items-center transition-transform duration-300 transform-growth hover:scale-105"
                                v-for="(item, i) in items" :key="i" :src="item.src" :alt="`Imagen de ${nameproduct} - ${partNumber}`"
                                @click="showZoomedImage(item.src)" />
                        </div>
                    </div>
                    <div class="py-2 font-medium text-lg text-plomomedioclaro w-full text-center">
                        <p>Todas las imagenes son referenciales.</p>
                    </div>
                </div>
                <div class="flex flex-col lg:pl-5 gap-4 lg:w-1/2">
                    <div>
                        <h1 class="text-2xl font-bold">{{ nameproduct }} - {{ partNumber }} | SEKAI TECH</h1>
                        <p class="text-left">Número de parte: {{ partNumber }} - {{ prodDescription }}. Compra ahora y disfruta de envío rápido y seguro.</p>
                    </div>
                    <div>
                        <p class="font-bold">
                            Número de parte: <span class="font-medium">{{ partNumber }}</span>
                        </p>
                        <p class="font-bold">
                            <span class="font-medium">{{ sku }}</span>
                        </p>
                        <p class="font-bold">Filtros:</p>
                        <!-- Aquí empieza la sección de filtros -->
                        <div class="border-y py-2 text-xs">
                            <div v-for="filterItem in filter_items" :key="filterItem.id">
                                <h4 class="inline font-semibold">
                                    <i class="mdi mdi-circle" style="font-size: 10px"></i>
                                    {{ filterItem.filter.name }}<span class="mx-1 font-bold">:</span>
                                </h4>
                                <span>{{ filterItem.name }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col xl:flex-row gap-2 xl:gap-4 items-center p-2">
                            <p class="p-2 text-blue-500">
                                <a href="#especificaciones" class="hidden xl:block rounded-sm font-bold"> Ver mas
                                    especificaciones...</a>
                                <a href="#especificacionesmobil" class="xl:hidden rounded-sm font-bold"> Ver mas
                                    especificaciones...</a>
                            </p>
                            <p class="p-2 border rounded-lg hover:bg-blue-500 hover:text-white">
                                <a :href="manufacturer_url" target="_blank"
                                    class="hidden xl:block rounded-sm font-medium"><i
                                        class="mdi mdi-information-outline small-icon"></i> Información del
                                    Fabricante</a>
                                <a :href="manufacturer_url" class="xl:hidden rounded-sm font-medium"><i
                                        class="mdi mdi-information-outline small-icon"></i> Información del
                                    Fabricante</a>
                            </p>
                        </div>
                    </div>

                    <div class="w-full md:w-60 text-2xl">
                        <p class="font-bold text-rojo text-center w-full">$ {{ pricedolar }}</p>
                        <p
                            class="rounded-lg bg-encabezado p-2 text-white font-bold flex items-center justify-center w-full">
                            S/. {{ precioEnSoles.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) }}
                        </p>
                    </div>
                    <div class="text-sm text-plomomediooscuro font-medium">
                        <p><i class="mdi mdi-chevron-right"></i>Precio incluye IGV.</p>
                        <p><i class="mdi mdi-chevron-right"></i>Precio no incluye flete por envio.</p>
                        <p>
                            <i class="mdi mdi-chevron-right"></i>Precio y stock pueden variar sin previo
                            aviso.
                        </p>
                    </div>
                    <h2 class="font-bold text-xl">
                        <i class="mdi mdi-check text-3xl text-cremaoscuro"></i> {{ stock }} disponibles
                    </h2>

                    <div class="flex flex-col md:flex-row items-center w-full p-1 text-center">
                        <div class="bg-white p-2 border rounded-lg w-full md:w-40">
                            <div class="flex justify-center items-center">
                                <span
                                    class="px-3 py-1 rounded-full border hover:bg-black hover:text-white cursor-pointer text-lg"
                                    @click="decrementQuantity">-</span>
                                <input v-model="quantity" min="0" class="w-12 text-center px-2" readonly />
                                <span
                                    class="px-3 py-1 rounded-full border hover:bg-black hover:text-white cursor-pointer text-lg"
                                    @click="incrementQuantity">+</span>
                            </div>
                        </div>
                        <div class="my-2 md:mx-2 w-full">
                            <button @click="agregarAlCarrito"
                                class="rounded text-white p-3 text-md font-bold transition hover:scale-105 w-full"
                                style="background-color: #1E90FF;">
                                AÑADIR AL CARRITO
                            </button>
                        </div>
                        <div class="hidden xl:block" id="especificaciones">
                        </div>
                        <a target="_blank" :href="whatsappLink"
                            class="rounded bg-greenhad text-white p-3 text-md font-bold transition hover:scale-105 w-full">
                            CONSULTAR WHATSAPP
                        </a>
                    </div>

                    <div class="text-sm text-plomomediooscuro">
                        <p class="font-bold">¿Necesitas ayuda con tu compra?</p>
                        <p class="font-medium">
                            Para poder ayudarte en la eleccion de su producto llámanos o escríbanos de
                            lunes a sábado de 8:00 a.m. a 8:00 p.m. al 991 375 813
                        </p>
                    </div>
                </div>
            </div>
            <div class="block xl:hidden" id="especificacionesmobil">
            </div>
        </div>
        <div class="sm:mx-10 xl:mx-28">
            <div class="flex flex-col p-5 justify-center items-center gap-5">
                <h1 class="w-80 border-b-4 border-azul text-blue-800 text-center font-medium text-xl pb-2">
                    Especificaciones</h1>
                <div class="md:px-36 w-full especificaciones">
                    <table class="min-w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="(spec, index) in productespecificaciones" :key="index" class="border-b">
                                <td class="px-4 py-2">{{ spec.key }}</td>
                                <td class="px-4 py-2">{{ spec.value }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="sm:mx-10 xl:mx-28">
            <div class="text-2xl flex flex-col md:flex-row justify-between items-center p-5">
                <h1 class="font-bold text-left py-2">Productos Relacionados</h1>
                <button @click="toggleMore" v-if="!showMore"
                    class="relative w-full md:w-1/2 lg:w-1/4 xl:w-1/6 rounded-full bg-encabezado p-2 text-white font-bold transition hover:scale-105 flex items-center justify-center">
                    <p><i class="mdi mdi-chevron-right font-bold text-3xl"></i></p>
                    <p>Ver más</p>
                </button>
                <button @click="toggleMore" v-if="showMore"
                    class="relative w-full md:w-1/2 lg:w-1/4 xl:w-1/6 rounded-full bg-encabezado p-2 text-white font-bold transition hover:scale-105 flex items-center justify-center">
                    <p><i class="mdi mdi-chevron-left font-bold text-3xl"></i></p>
                    <p>Ver menos</p>
                </button>
            </div>
            <!-- Productos adicionales ocultos -->
            <div v-if="showMore" class="relative flex-row">
                <div class="flex flex-wrap justify-center px-5">
                    <CartMain v-for="product in filteredProducts" :key="product.product_id" :producto="product"
                        :images="getImagenesProducto(product.id)" :brand="getImagenesBrand(product.id)"
                        :totalimages="images" />
                </div>
            </div>
            <!-- Carrusel de Productos -->
            <div v-else class="">
                <v-carousel hide-delimiters style="height: 100%">
                    <v-carousel-item v-for="(productGroup, groupIndex) in productGroups" :key="groupIndex">
                        <div class="flex justify-center px-5 lg:px-16">
                            <template v-for="(product, productIndex) in productGroup" :key="productIndex">
                                <CartMain :producto="product" :images="getImagenesProducto(product.id)"
                                    :brand="getImagenesBrand(product.id)" :totalimages="images" />
                            </template>
                        </div>
                    </v-carousel-item>
                </v-carousel>
            </div>
        </div>
        <div v-if="zoomed"
            class="fixed inset-0 z-10 flex items-center justify-center bg-plomooscuro bg-opacity-75 xl:px-20 py-10">
            <i class="mdi mdi-close cursor-pointer text-2xl font-bold absolute text-white top-12 right-2 xl:right-24 px-1 bg-azul rounded-full"
                @click="closeZoomedImage"></i>
            <div class="bg-white px-10 md:rounded-xl w-full h-full">
                <v-carousel hide-delimiters style="height: 100%; width: 100%">
                    <v-carousel-item v-for="(item, i) in items" :key="i">
                        <div class="h-full w-full flex items-center justify-center">
                            <img :src="item.src" class="lg:h-full" @click="showZoomedImage(item.src)" />
                        </div>
                    </v-carousel-item>
                </v-carousel>
            </div>
        </div>
        <PapupCarrito v-if="showPopup" @close="closePopup" :images="images" />
    </div>
</template>
<style>
.zoom-effect {
    transition: transform 0.3s ease-in-out;
    cursor: zoom-in;
    /* Cambiar el cursor a una lupa */
}

.zoom-effect:hover {
    transform: scale(1.1);
}

.especificaciones {
    * {
        top: 0;
        text-transform: uppercase;
        text-align: left;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-weight: bold;
    }

    h1 {
        font-size: 1.5rem;
    }

    h2 {
        font-size: 1.25rem;
    }

    h3 {
        font-size: 1.125rem;
    }

    h4 {
        font-size: 1rem;
    }

    h5 {
        font-size: 0.875rem;
    }

    h6 {
        font-size: 0.75rem;
    }

    p {
        padding: 0.1rem;
    }

    ul {
        list-style: none;
        padding: 1.25rem;
    }

    a {
        text-decoration: none;
        color: #1E90FF;
        border: 1px solid #1E90FF;
        padding: 0.25rem 1rem;
        border-radius: 0.25rem;

        &:hover {
            background-color: #1E90FF;
            color: #fff;
        }
    }

    li {
        list-style: circle;
        padding: 0.2rem;
        margin: 0.1rem;
    }
}
</style>