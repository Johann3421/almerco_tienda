<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import Favicon from "@assets/img/favicon.png";
import Notification from '@/Components/Notification.vue';
import SimpleHeader from '@/Components/SimpleHeader.vue';
import useFormRules from "@utils/UseFormRules";
import OrderItemRow from "@/Components/OrderItemRow.vue";
import { useOrderStore } from '@stores/OrderStore';
import debounce from 'lodash.debounce';
import * as XLSX from 'xlsx'; // Librería para exportar a Excel

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
});

const current_page = ref(1);
const orderStore = useOrderStore();
const orders = ref([]);
const fetchLoader = ref(false);
const totalOrders = ref(0);
const search = ref("");
const searchQuery = ref("");
const itemsPerPage = ref(2);
const fetchLoader_form = ref(false);
const showCanceledOrders = ref(false); // Controla si se muestran los pedidos cancelados

// Carga los pedidos desde la API
const loadOrders = async ({ page, itemsPerPage, sortBy }) => {
    fetchLoader.value = true;

    try {
        const response = await axios.get(route('orders.load', {
            page: page,
            itemsPerPage: itemsPerPage,
            sortBy: sortBy,
            sortDesc: 'asc',
            search: search.value
        }));

        current_page.value = response.data.orders.current_page;
        orders.value = response.data.orders.data;
        totalOrders.value = response.data.orders.total;
    } catch (error) {
        console.error(error);
    } finally {
        fetchLoader.value = false;
    }
};

// Filtra los pedidos según el estado de showCanceledOrders
const filteredOrders = computed(() => {
    if (!showCanceledOrders.value) {
        return orders.value.filter(order => order.order_status !== 'Cancelado');
    }
    return orders.value;
});

// Alterna la visibilidad de los pedidos cancelados
const toggleCanceledOrders = () => {
    showCanceledOrders.value = !showCanceledOrders.value;
};

// Exporta los datos a Excel
const exportToExcel = () => {
    const data = filteredOrders.value.map(order => {
        return {
            ID: order.id,
            Fecha: formatDate(order.created_at),
            Total: order.order_total,
            Estado: order.order_status,
            // Agrega más campos si es necesario
        };
    });

    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Pedidos");
    XLSX.writeFile(wb, "pedidos.xlsx");
};

// Formatea la fecha
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Búsqueda debounced
const performSearch = () => {
    searchQuery.value = search.value;
};

const debouncedSearch = debounce(performSearch, 250);

// Inicializa los headers
const headers = ref([]);
const headers_items = ref([]);

onMounted(async () => {
    headers.value = [
        { title: "# Pedido", key: "index", align: "center" },
        { title: "Código", key: "order_code", align: "center" },
        { title: "Cliente", key: "customer_name" },
        { title: "Fecha", key: "created_at", align: "center" },
        { title: "Total", key: "order_total", align: "center" },
        { title: "Estado", key: "order_status", align: "center" },
        { title: "Acciones", key: "actions", align: "center" },
    ];

    headers_items.value = [
        { title: "# Item", key: "index", sortable: false, align: "center" },
        { title: "Producto", key: "get_product.name", sortable: true },
        { title: "Cantidad", key: "amount", sortable: false, align: "center" },
        { title: "Precio Unitario", key: "unit_price", sortable: false, align: "center" },
        { title: "Total", key: "total_price", sortable: false, align: "center" },
    ];

    // Carga los pedidos al montar el componente
    await loadOrders({ page: 1, itemsPerPage: itemsPerPage.value });
});

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
});

const rules = useFormRules;

const dialog_create_order = ref(false);
const dialog_update_order = ref(false);
const dialog_delete_order = ref(false);

const form_create_order = ref(null);
const form_update_order = ref(null);
const form_delete_order = ref(null);

const storeOrder = async () => {
    const { valid } = await form_create_order.value.validate();

    if (valid) {
        try {
            if (orderStore.order.items.length === 0) {
                showAlert(true, "Debe agregar al menos un producto al pedido", "#F9D7DA", 3000);
                return;
            }

            if (order.value.order_status === "Completado" && order.value.delivery_date === "") {
                showAlert(true, "Debe ingresar la fecha de entrega", "#F9D7DA", 3000);
                return;
            }

            fetchLoader_form.value = true;

            if (order.value.order_status !== "Completado") {
                delete order.value.delivery_date;
            }

            if (order.value.observation === "") {
                delete order.value.observation;
            }

            const filteredItems = orderStore.order.items.filter(item => item.product_id !== 0);
            const total = filteredItems.reduce((acc, curr) => acc + curr.total, 0);
            order.value.order_total = total;

            const data = {
                ...order.value,
                items: filteredItems
            };

            const response = await axios.post(route('orders.store'), data);

            if (response.status === 201) {
                showAlert(true, response.data.message, "#D4E7C5", 2000);
                dialog_create_order.value = false;
                orderStore.clearOrderItems();
                await form_create_order.value.reset();
                await loadOrders({ page: 1, itemsPerPage: itemsPerPage.value });
            }
        } catch (error) {
            handleError(error, error.response.data.message);
        } finally {
            fetchLoader_form.value = false;
        }
    }
};

const updateOrder = async () => {
    try {
        if (orderStore.order.items.length === 0) {
            showAlert(true, "Debe agregar al menos un producto al pedido", "#F9D7DA", 3000);
            return;
        }

        if (order.value.order_status === "Completado" && order.value.delivery_date === "") {
            showAlert(true, "Debe ingresar la fecha de entrega", "#F9D7DA", 3000);
            return;
        }

        fetchLoader_form.value = true;

        if (order.value.order_status !== "Completado") {
            delete order.value.delivery_date;
        }

        const filteredItems = orderStore.order.items.filter(item => item.product_id !== 0);
        const total = filteredItems.reduce((acc, curr) => acc + curr.total, 0);
        order.value.order_total = total;

        const data = {
            ...order.value,
            items: filteredItems
        };

        const response = await axios.patch(route('orders.updateOrder', {
            code: order.value.order_code
        }), data);

        if (response.status === 200) {
            showAlert(true, response.data.message, "#D4E7C5", 2000);
            dialog_update_order.value = false;
            orderStore.clearOrderItems();
            form_update_order.value.reset();
            await loadOrders({ page: current_page.value, itemsPerPage: itemsPerPage.value });
        }
    } catch (error) {
        handleError(error, error.response.data.message);
    } finally {
        fetchLoader_form.value = false;
    }
};

const deleteOrder = async () => {
    const { valid } = await form_delete_order.value.validate();

    if (valid) {
        try {
            fetchLoader_form.value = true;

            const response = await axios.patch(route('orders.updateStatus', {
                code: order.value.order_code
            }), {
                order_status: order.value.order_status_update
            });

            if (response.status === 200) {
                showAlert(true, response.data.message, "#D4E7C5", 2000);
                dialog_delete_order.value = false;
                form_delete_order.value.reset();
                await loadOrders({ page: current_page.value, itemsPerPage: itemsPerPage.value });
            }
        } catch (error) {
            handleError(error, error.response.data.message);
        } finally {
            fetchLoader_form.value = false;
        }
    }
};

const Modal = (dialog, selected) => {
    orderStore.clearOrderItems();

    if (dialog === "create_order") {
        order.value = {
            id: 0,
            customer_name: "",
            customer_email: "",
            customer_phone: "",
            customer_address: "",
            customer_document_number: "",
            customer_document_type: 1,
            order_status: "Pendiente de Pago",
            order_total: 0,
            observation: "",
            delivery_date: "",
            active: true,
            items: []
        };

        dialog_create_order.value = true;
    } else if (dialog === "update_order") {
        dialog_update_order.value = true;
        order.value = selected;
        order.value.customer_document_type = Number(selected.customer_document_type);

        const items = selected.get_products.map((product) => {
            return {
                id: product.id,
                uuid: crypto.randomUUID(),
                product_id: product.product_id,
                amount: product.amount,
                unit_price: Number(product.unit_price),
                unit_stock: Number(product.get_product.stock) + Number(product.amount),
                unit_name: product.get_product.name,
                total: Number(product.total_price)
            };
        });

        orderStore.addItems(items);
    } else if (dialog === "delete_order") {
        dialog_delete_order.value = true;
        order.value.id = selected.id;
        order.value.customer_name = selected.customer_name;
        order.value.order_code = selected.order_code;
        order.value.order_status_update = 'Cancelado';
    }
};

const alert = ref({
    status: false,
    message: "",
    color: "#D4E7C5",
    timeout: 1000,
    vertical: true,
    location: "right top",
    rounded: "md"
});

const showAlert = (status, message, color, timeout) => {
    alert.value.status = status;
    alert.value.message = message;
    alert.value.color = color;
    alert.value.timeout = timeout;
};

const handleError = (error, errorMessage) => {
    console.error(error);
    showAlert(true, errorMessage, "#F9D7DA", 3000);
};
</script>

<template>

    <Head title="Pedidos">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0;">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_create_order" width="1000" persistent>
        <v-card max-width="1000" title="Guardar Pedido" prepend-icon="mdi-shopping">
            <v-form @submit.prevent="storeOrder" ref="form_create_order" fast-fail>
                <v-card-text class="px-8 py-8">

                    <div class="flex gap-4">

                        <v-text-field density="comfortable" v-model="order.customer_name" label="Nombre del Cliente (*)"
                            color="primary" variant="outlined" required hint="Ejemplo: Jhon Doe"
                            :rules="[rules.required]">
                        </v-text-field>

                        <v-text-field density="comfortable" v-model="order.customer_email"
                            label="Correo Electrónico (*)" color="primary" variant="outlined" required
                            hint="Ejemplo: jhondoe@gmail.com" :rules="[rules.required, rules.email]">
                        </v-text-field>

                    </div>

                    <div class="flex gap-4">

                        <v-text-field density="comfortable" v-model="order.customer_phone" label="Teléfono (*)"
                            color="primary" variant="outlined" required hint="Ejemplo: 1234567890"
                            :rules="[rules.required, rules.phone]">
                        </v-text-field>

                        <v-text-field density="comfortable" v-model="order.customer_address" label="Dirección (*)"
                            color="primary" variant="outlined" required hint="Ejemplo: Calle 123"
                            :rules="[rules.required, rules.address]">
                        </v-text-field>

                    </div>

                    <div class="flex gap-4">
                        <div class="flex-2">
                            <v-autocomplete dense v-model="order.customer_document_type" label="Tipo de Documento (*)"
                                item-title="name" item-value="value" chips density="comfortable" color="primary"
                                variant="outlined" required :rules="[rules.required]"
                                :items="[{ name: 'DNI', value: 1 }, { name: 'RUC', value: 2 }]">

                                <template v-slot:chip="{ props, item }">
                                    <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                                </template>

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                                </template>

                                <template v-slot:no-data>
                                    <v-list-item>
                                        <v-list-item-title>
                                            No hay filtros disponibles
                                        </v-list-item-title>
                                    </v-list-item>
                                </template>
                            </v-autocomplete>
                        </div>

                        <div class="flex-1">
                            <v-text-field density="comfortable" v-model="order.customer_document_number"
                                :label="(order.customer_document_type === 1) ? 'Dni (*)' : 'Ruc (*)'" type="text"
                                :rules="(order.customer_document_type === 1) ? [rules.required, rules.dni] : [rules.required, rules.ruc]"
                                color="primary" variant="outlined" required hint="Ejemplo: 00099900"
                                :maxlength="(order.customer_document_type === 1) ? 8 : 11">
                            </v-text-field>
                        </div>
                    </div>

                    <div class="flex gap-4">

                        <v-autocomplete dense v-model="order.order_status" label="Estado del Pedido (*)"
                            item-title="name" item-value="value" chips density="comfortable" color="primary"
                            variant="outlined" required :rules="[rules.required]"
                            :items="[{ name: 'Pendiente de Pago', value: 'Pendiente de Pago' }, { name: 'Pendiente de Entrega', value: 'Pendiente de Entrega' }, { name: 'Completado', value: 'Completado' }]">

                            <template v-slot:chip="{ props, item }">
                                <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                            </template>

                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                            </template>

                            <template v-slot:no-data>
                                <v-list-item>
                                    <v-list-item-title>
                                        No hay filtros disponibles
                                    </v-list-item-title>
                                </v-list-item>
                            </template>
                        </v-autocomplete>

                        <v-text-field density="comfortable" v-model="order.delivery_date"
                            :label="(order.order_status === 'Completado') ? 'Fecha de Entrega (*)' : 'Fecha de Entrega'"
                            type="date" :disabled="(order.order_status !== 'Completado')"
                            :rules="(order.order_status === 'Completado') ? [rules.required] : []"
                            :model-value="(order.order_status === 'Completado') ? order.delivery_date : ''"
                            color="primary" variant="outlined" required hint="Ejemplo: 2021-10-10">

                        </v-text-field>

                    </div>

                    <v-textarea v-model="order.observation" label="Observación" color="primary" variant="outlined"
                        hint="Ejemplo: Falta el pago de manera presencial" rows="3" clearable density="comfortable"
                        counter maxlength="255" no-resize>
                    </v-textarea>

                    <v-checkbox label="Activo" v-model="order.active" color="primary" :rules="[rules.boolean]">

                    </v-checkbox>

                    <div class="flex gap-4 w-100 justify-between align-center mb-6">
                        <h2 class="text-xl font-weight-medium tracking-normal">
                            Productos
                        </h2>

                        <v-btn color="primary" density="comfortable" @click="orderStore.addItem">
                            <v-icon left>mdi-plus</v-icon>
                        </v-btn>
                    </div>

                    <div class="grid grid-cols-1 divide-y">
                        <OrderItemRow v-for="(item, index) in orderStore.getOrder_items" :products="props.products"
                            :product_id="item.product_id" :key="item.uuid" :index="index" :amount="item.amount"
                            :unit_price="item.unit_price" :unit_stock="item.unit_stock" :unit_name="item.unit_name"
                            :total="item.total">
                        </OrderItemRow>
                    </div>

                    <small class="text-caption text-medium-emphasis mt-6 block">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_create_order = false">
                        Cancelar
                    </v-btn>

                    <v-btn type="submit" color="primary" :loading="fetchLoader_form">
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_update_order" width="1000" persistent>
        <v-card max-width="1000" :title="'Actualizar Pedido: ' + order.order_code" prepend-icon="mdi-shopping">
            <v-form @submit.prevent="updateOrder" ref="form_update_order" fast-fail>
                <v-card-text class="px-8 py-8">
                    <div class="flex gap-4">

                        <v-text-field density="comfortable" v-model="order.customer_name" label="Nombre del Cliente (*)"
                            color="primary" variant="outlined" hint="Ejemplo: Jhon Doe" :rules="[rules.required]">
                        </v-text-field>

                        <v-text-field density="comfortable" v-model="order.customer_email"
                            label="Correo Electrónico (*)" color="primary" variant="outlined"
                            hint="Ejemplo: jhondoe@gmail.com" :rules="[rules.required, rules.email]">
                        </v-text-field>

                    </div>

                    <div class="flex gap-4">

                        <v-text-field density="comfortable" v-model="order.customer_phone" label="Teléfono (*)"
                            color="primary" variant="outlined" hint="Ejemplo: 1234567890"
                            :rules="[rules.required, rules.phone]">
                        </v-text-field>

                        <v-text-field density="comfortable" v-model="order.customer_address" label="Dirección (*)"
                            color="primary" variant="outlined" hint="Ejemplo: Calle 123"
                            :rules="[rules.required, rules.address]">
                        </v-text-field>

                    </div>

                    <div class="flex gap-4">
                        <div class="flex-2">
                            <v-autocomplete dense v-model="order.customer_document_type" label="Tipo de Documento (*)"
                                item-title="name" item-value="value" chips density="comfortable" color="primary"
                                variant="outlined" :rules="[rules.required]"
                                :items="[{ name: 'DNI', value: 1 }, { name: 'RUC', value: 2 }]">

                                <template v-slot:chip="{ props, item }">
                                    <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                                </template>

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                                </template>

                                <template v-slot:no-data>
                                    <v-list-item>
                                        <v-list-item-title>
                                            No hay filtros disponibles
                                        </v-list-item-title>
                                    </v-list-item>
                                </template>
                            </v-autocomplete>
                        </div>

                        <div class="flex-1">
                            <v-text-field density="comfortable" v-model="order.customer_document_number"
                                :label="(order.customer_document_type === 1) ? 'Dni (*)' : 'Ruc (*)'" type="text"
                                :rules="(order.customer_document_type === 1) ? [rules.required, rules.dni] : [rules.required, rules.ruc]"
                                color="primary" variant="outlined" required hint="Ejemplo: 00099900"
                                :maxlength="(order.customer_document_type === 1) ? 8 : 11">
                            </v-text-field>
                        </div>
                    </div>

                    <div class="flex gap-4">

                        <v-autocomplete dense v-model="order.order_status" label="Estado del Pedido (*)"
                            item-title="name" item-value="value" chips density="comfortable" color="primary"
                            variant="outlined" :rules="[rules.required]"
                            :items="[{ name: 'Pendiente de Pago', value: 'Pendiente de Pago' }, { name: 'Pendiente de Entrega', value: 'Pendiente de Entrega' }, { name: 'Completado', value: 'Completado' }]">

                            <template v-slot:chip="{ props, item }">
                                <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                            </template>

                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                            </template>

                            <template v-slot:no-data>
                                <v-list-item>
                                    <v-list-item-title>
                                        No hay filtros disponibles
                                    </v-list-item-title>
                                </v-list-item>
                            </template>
                        </v-autocomplete>

                        <v-text-field density="comfortable" v-model="order.delivery_date"
                            :label="(order.order_status === 'Completado') ? 'Fecha de Entrega (*)' : 'Fecha de Entrega'"
                            type="date" :disabled="(order.order_status !== 'Completado')"
                            :rules="(order.order_status === 'Completado') ? [rules.required] : []"
                            :model-value="(order.order_status === 'Completado') ? order.delivery_date : ''"
                            color="primary" variant="outlined" required hint="Ejemplo: 2021-10-10">

                        </v-text-field>

                    </div>

                    <v-textarea v-model="order.observation" label="Observación" color="primary" variant="outlined"
                        hint="Ejemplo: Falta el pago de manera presencial" rows="3" clearable density="comfortable"
                        counter maxlength="255" no-resize>
                    </v-textarea>

                    <v-checkbox label="Activo" v-model="order.active" color="primary" :rules="[rules.boolean]">

                    </v-checkbox>

                    <div class="flex gap-4 w-100 justify-between align-center mb-6">
                        <h2 class="text-xl font-weight-medium tracking-normal">
                            Productos
                        </h2>

                        <v-btn color="primary" density="comfortable" @click="orderStore.addItem">
                            <v-icon left>mdi-plus</v-icon>
                        </v-btn>
                    </div>

                    <div class="grid grid-cols-1 divide-y">
                        <OrderItemRow v-for="(item, index) in orderStore.getOrder_items" :products="props.products"
                            :product_id="item.product_id" :key="item.uuid" :index="index" :amount="item.amount"
                            :unit_price="item.unit_price" :unit_stock="item.unit_stock" :unit_name="item.unit_name"
                            :total="item.total"></OrderItemRow>
                    </div>

                    <small class="text-caption text-medium-emphasis mt-6 block">
                        (*) Campos requeridos
                    </small>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_update_order = false">
                        Cancelar
                    </v-btn>

                    <v-btn color="primary" :loading="fetchLoader_form" type="submit">
                        Actualizar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialog_delete_order" width="500" persistent>
        <v-card max-width="500" :title="'Pedido: ' + order.order_code" prepend-icon="mdi-delete">
            <v-form @submit.prevent="deleteOrder" ref="form_delete_order" fast-fail>
                <v-card-text class="px-8 py-8">
                    <v-row>
                        <v-col cols="12">
                            <v-autocomplete dense v-model="order.order_status_update" label="Acciones (*)"
                                item-title="name" item-value="value" chips density="comfortable" color="primary"
                                variant="outlined" required :rules="[rules.required]"
                                :items="[{ name: 'Cancelar', value: 'Cancelado' }, { name: 'Reembolzar', value: 'Reembolsado' }]">

                                <template v-slot:chip="{ props, item }">
                                    <v-chip v-bind="props" :text="item.raw.name" color="primary"></v-chip>
                                </template>

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                                </template>

                                <template v-slot:no-data>
                                    <v-list-item>
                                        <v-list-item-title>
                                            No hay filtros disponibles
                                        </v-list-item-title>
                                    </v-list-item>
                                </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions>
                    <v-divider></v-divider>
                    <v-btn @click="dialog_delete_order = false">
                        Cancelar
                    </v-btn>

                    <v-btn color="primary" :loading="fetchLoader_form" type="submit">
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <AuthenticatedLayout>
        <div class="py-6 h-full">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
                <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">
                    <section class="bg-gris-300 w-full grid place-items-center">
                        <div class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg">
                            <SimpleHeader title="Gestiona tus Pedidos" breadcrumb="Pedidos" :breadcrumb_link="route('orders.index')" />

                            <article class="mt-4">
                                <v-card variant="flat">
                                    <template v-slot:title>
                                        <v-row align="center" justify="space-between" density="comfortable" class="p-2">
                                            <v-col cols="12" md="6">
                                                <h2 class="text-lg md:text-xl lg:text-xl text-gray-900">Pedidos</h2>
                                            </v-col>
                                            <v-col cols="12" md="6" class="text-right flex justify-end items-center">
                                                <v-btn color="primary" @click="Modal('create_order')">
                                                    <v-icon left>mdi-plus</v-icon>
                                                    Agregar Pedido
                                                </v-btn>
                                                <v-btn color="secondary" @click="toggleCanceledOrders" class="ml-2">
                                                    <v-icon left>mdi-eye-off</v-icon>
                                                    {{ showCanceledOrders ? 'Mostrar Cancelados' : 'Ocultar Cancelados' }}
                                                </v-btn>
                                                <v-btn color="success" @click="exportToExcel" class="ml-2">
                                                    <v-icon left>mdi-microsoft-excel</v-icon>
                                                    Exportar a Excel
                                                </v-btn>
                                            </v-col>
                                        </v-row>
                                    </template>

                                    <template v-slot:text>
                                        <v-text-field v-model="search" label="Buscar Pedido" prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line density="comfortable" color="primary" @input="debouncedSearch">
                                        </v-text-field>

                                        <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers" :items="filteredOrders" :items-length="totalOrders" :loading="fetchLoader" :search="searchQuery" item-value="id" @update:options="loadOrders" loading-text="Cargando Pedidos" no-data-text="Actualmente no hay pedidos disponibles" items-per-page-text="Pedidos por página" show-expand>
                                            <template v-slot:loader="{ isActive }">
                                                <v-progress-linear :active="isActive" color="primary" height="3" indeterminate>
                                                </v-progress-linear>
                                            </template>

                                            <template v-slot:item.index="{ index }">
                                                {{ index + 1 }}
                                            </template>

                                            <template v-slot:item.created_at="{ item }">
                                                {{ formatDate(item.created_at) }}
                                            </template>

                                            <template v-slot:item.total="{ item }">
                                                <v-chip color="green" label>
                                                    {{ item.total }}
                                                </v-chip>
                                            </template>

                                            <template v-slot:item.order_status="{ item }">
                                                <template v-if="item.order_status === 'Completado'">
                                                    <v-chip color="green" label>
                                                        {{ item.order_status }}
                                                    </v-chip>
                                                </template>

                                                <template v-else-if="item.order_status === 'Cancelado'">
                                                    <v-chip color="red" label>
                                                        {{ item.order_status }}
                                                    </v-chip>
                                                </template>

                                                <template v-else-if="item.order_status === 'Reembolsado'">
                                                    <v-chip color="black" label>
                                                        {{ item.order_status }}
                                                    </v-chip>
                                                </template>

                                                <template v-else>
                                                    <v-chip color="orange" label>
                                                        {{ item.order_status }}
                                                    </v-chip>
                                                </template>
                                            </template>

                                            <template v-slot:item.actions="{ item }">
                                                <v-icon class="cursor-pointer" color="#0e7490" style="margin-right: .5rem;" @click="Modal('update_order', item)" :disabled="(item.order_status === 'Completado' || item.order_status === 'Cancelado' || item.order_status === 'Reembolsado')">
                                                    mdi-pencil
                                                </v-icon>
                                                <v-icon class="cursor-pointer" color="#0e7490" style="margin-left: .5rem;" @click="Modal('delete_order', item)" :disabled="(item.order_status === 'Completado' || item.order_status === 'Cancelado' || item.order_status === 'Reembolsado')">
                                                    mdi-delete
                                                </v-icon>
                                            </template>

                                            <template v-slot:expanded-row="{ columns, item }">
                                                <tr>
                                                    <td :colspan="columns.length" style="padding: 1rem 5rem 1rem 5rem;">
                                                        <v-data-table :headers="headers_items" :items="item.get_products" no-data-text="Actualmente no hay items en el pedido" items-per-page="5" style=" border: 2px solid #adadad; border-radius: .5rem;">
                                                            <template v-slot:item.index="{ index }">
                                                                {{ index + 1 }}
                                                            </template>

                                                            <template v-slot:item.get_product.name="{ item }">
                                                                <div class="truncated-cell">
                                                                    {{ item.get_product.name }}
                                                                </div>
                                                            </template>
                                                        </v-data-table>
                                                    </td>
                                                </tr>
                                            </template>
                                        </v-data-table-server>
                                    </template>
                                </v-card>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>



<style scoped>
.truncated-cell {
    max-width: 35rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
