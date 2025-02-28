<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SimpleHeader from '@/Components/SimpleHeader.vue';
import Notification from '@/Components/Notification.vue';
import { Link, Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, watch } from "vue";
import Favicon from "@assets/img/favicon.png";
import debounce from 'lodash.debounce';

const products = ref([]);

const fetchLoader = ref(false);

const totalProducts = ref(0);

const search = ref("");

const searchQuery = ref("");

const itemsPerPage = ref(10);

const fetchLoader_form = ref(false);

const loadProducts = async ({ page, itemsPerPage, sortBy }) => {
    fetchLoader.value = true;

    try {
      const response = await axios.get(route('products.load', {
          page: page,
          itemsPerPage: itemsPerPage,
          sortBy: sortBy,
          sortDesc: 'asc',
          search: search.value
      }))
      products.value = response.data.products.data
      totalProducts.value = response.data.products.total;
    } catch (error) {
      console.error(error);
    } finally {
      fetchLoader.value = false;
    }

};

const performSearch = () => {
    searchQuery.value = search.value;
};

const debouncedSearch = debounce(performSearch, 250);

const headers = ref([]);

onMounted(async () => {
    headers.value = [
        {
            title: "ID",
            key: "id",
            align: "center",
            sortable: false,
        },
        {
            title: "Producto",
            key: "name",
            align: "start",
            sortable: true,
        },
        {
            title: "Precio Anterior",
            key: "previous_price",
            align: "center",
            sortable: false,
        },
        {
            title: "Precio",
            key: "price",
            align: "center",
            sortable: false,
        },
        {
            title: "En Tienda",
            key: "on_sale",
            align: "center",
            sortable: false,
        },
        {
            title: "Stock",
            key: "stock",
            align: "center",
            sortable: false,
        },
        {
            title: "Acciones",
            key: "actions",
            align: "center",
            sortable: false,
        },
    ];
});

const product = ref({
    id: 0,
    name: "",
    previous_price: 0,
    price: 0,
    on_sale: false,
    stock: 0,
});

const dialog_delete = ref(false);

const destroyProduct = async () => {
    try {
        fetchLoader_form.value = true;

        const response = await axios.delete(route("products.destroy", product.value.id));

        if (response.status === 200) {
            products.value = products.value.filter((p) => p.id !== product.value.id);
            showAlert(true, response.data.message, "#D4E7C5", 1000);
        }
    } catch (error) {
        handleError(error, "Ha ocurrido un error al eliminar el producto");
    } finally {
        fetchLoader_form.value = false;

        closeDelete();
    }
};

const closeDelete = () => {
    dialog_delete.value = false;

    product.value = {
        id: 0,
        name: "",
        previous_price: 0,
        price: 0,
        on_sale: false,
        stock: 0,
    };
};

const updateVisibility = async (id, visibility) => {
    fetchLoader.value = true;

    try {
        const response = await axios.patch(route("products.updateVisibility"), {
            id: id,
            visibility: visibility,
        });

        if (!response.request.status === 200) {
            products.value = products.value.map((product) => {
                if (product.id === id) {
                    product.on_sale = !visibility;
                }
                return product;
            });
        } else {
            showAlert(true, response.data.message, "#D4E7C5", 1000);
        }
    } catch (error) {
        console.error(error);

        products.value = products.value.map((product) => {
            if (product.id === id) {
                product.on_sale = !visibility;
            }
            return product;
        });

        handleError(error, "Ha ocurrido un error al actualizar la visibilidad del producto");
    } finally {
        fetchLoader.value = false;
    }
};

const Modal = (dialog, item) => {
    if (dialog === "delete_product") {
        product.value = item;
        dialog_delete.value = true;
    }
};

const alert = ref({
    status: false,
    message: "",
    color: "#D4E7C5",
    timeout: 1000,
    vertical: true,
    location: "right top",
    rounded: "md",
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

    <Head title="Productos">
        <link rel="icon" :href="Favicon" type="image/png" sizes="16x16" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
    </Head>

    <Notification v-model="alert.status" :message="alert.message" :color="alert.color" :timeout="alert.timeout"
        :vertical="alert.vertical" :location="alert.location" :rounded="alert.rounded" style="margin: 6.5rem 2rem 0 0">
        {{ alert.message }}
    </Notification>

    <v-dialog v-model="dialog_delete" max-width="700" persistent>
        <v-card class="bg-white rounded-lg flex flex-col md:ml-auto w-full mt-10 md:mt-0 relative z-10 shadow-md py-6">
            <v-card-title class="text-gray-900 text-lg mb-1 font-medium title-font text-center">
                ¿ Estás seguro de que quieres borrar el producto {{ product.name }} ?
                <v-card-subtitle class="leading-relaxed text-gray-600 my-2">
                    Esta acción no se puede deshacer
                </v-card-subtitle>
            </v-card-title>
            <v-card-actions class="mx-52">
                <v-btn color="red" variant="tonal" @click="destroyProduct(product)" :loading="fetch_loader_form">
                    Eliminar
                </v-btn>

                <v-spacer></v-spacer>

                <v-btn color="primary" variant="tonal" @click="closeDelete"> Cancelar </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <AuthenticatedLayout>
        <div class="py-6 h-full">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-6">
                <div class="overflow-hidden sm:rounded-sm lg:rounded-lg grid place-items-center">
                    <section class="bg-gris-300 w-full grid place-items-center">
                        <div
                            class="max-w-screen-2xl w-full px-4 py-4 sm:px-6 sm:py-4 lg:px-12 lg:py-12 bg-white-200 rounded-lg">
                            <SimpleHeader title="Gestiona tus Productos" breadcrumb="Productos"
                                :breadcrumb_link="route('products.index')" />

                            <article class="grid place-items-center w-12/12 mt-4">
                                <v-card flat class="w-full">
                                    <template v-slot:title>
                                        <v-row align="center" justify="space-between" density="comfortable" class="p-2">
                                            <v-col cols="12" md="6">
                                                <h2 class="text-lg md:text-xl lg:text-xl text-gray-900">
                                                    Productos
                                                </h2>
                                            </v-col>
                                            <v-col cols="12" md="6" class="text-right flex justify-end items-center">
                                                <Link :href="route('products.create')" type="button">
                                                <v-btn color="primary">
                                                    <v-icon left>mdi-plus</v-icon>
                                                    Agregar Producto
                                                </v-btn>
                                                </Link>
                                            </v-col>
                                        </v-row>
                                    </template>

                                    <template v-slot:text>
                                        <v-text-field v-model="search" label="Buscar Producto"
                                            prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line
                                            density="comfortable" color="primary" @input="debouncedSearch">
                                        </v-text-field>

                                        <v-data-table-server v-model:items-per-page="itemsPerPage" :headers="headers"
                                            :items="products" :items-length="totalProducts" :loading="fetchLoader"
                                            :search="searchQuery" item-value="name"
                                            @update:options="loadProducts"
                                            loading-text="Cargando Productos"
                                            no-data-text="Actualmente no hay productos disponibles"
                                            items-per-page-text="Productos por página"
                                            >

                                            <template v-slot:loader="{ isActive }">
                                                <v-progress-linear
                                                  :active="isActive"
                                                  color="primary"
                                                  height="3"
                                                  indeterminate
                                                >
                                                </v-progress-linear>
                                            </template>

                                            <template v-slot:item.index="{ index }">
                                                {{ index + 1 }}
                                            </template>

                                            <template v-slot:item.previous_price="{ item }">
                                                <v-chip color="gray" class="text-uppercase" label size="small">
                                                  $ {{ item.previous_price }}
                                                </v-chip>
                                            </template>

                                            <template v-slot:item.price="{ item }">
                                                <v-chip color="primary" class="text-uppercase" label size="small">
                                                  $ {{ item.price }}
                                                </v-chip>
                                            </template>

                                            <template v-slot:item.on_sale="{ item }">
                                                <v-switch
                                                  v-model="item.on_sale"
                                                  color="#0e7490"
                                                  style="justify-content: center"
                                                  @change="updateVisibility(item.id, item.on_sale)"
                                                >
                                                  <template v-slot:label>
                                                    <span class="text-gray-900 font-medium">
                                                      {{ item.on_sale ? "Sí" : "No" }}
                                                    </span>
                                                  </template>
                                                </v-switch>
                                            </template>

                                            <template v-slot:item.stock="{ item }">
                                                <v-chip v-if="item.stock" color="green" label size="small">
                                                  {{ item.stock }}
                                                </v-chip>
                                                <v-chip v-else color="red" label size="small"> Agotado </v-chip>
                                              </template>

                                            <template v-slot:item.actions="{ item }">
                                                <Link
                                                  :href="route('products.edit', item.id)"
                                                  class="inline-block me-2 transition hover:scale-125"
                                                >
                                                  <v-icon size="small" class="me-2" color="primary">
                                                    mdi-pencil
                                                  </v-icon>
                                                </Link>
                                                <v-icon
                                                  size="small"
                                                  @click="Modal('delete_product', item)"
                                                  color="#f44336"
                                                  class="cursor-pointer transition hover:scale-125"
                                                >
                                                  mdi-delete
                                                </v-icon>
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
