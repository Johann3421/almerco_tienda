<script setup>
import { defineProps, ref, watch } from 'vue'
import { useOrderStore } from '@stores/OrderStore';

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    product_id: {
        type: Number,
        required: true
    },
    index: {
        type: Number,
        required: true
    },
    amount: {
        type: Number,
        required: true
    },
    unit_price: {
        type: Number,
        required: true
    },
    unit_stock: {
        type: Number,
        required: true
    },
    unit_name: {
        type: String,
        required: true
    },
    total: {
        type: Number,
        required: true
    }
})

const orderStore = useOrderStore();

const product_id = ref()

if (props.product_id !== 0) {
    product_id.value = props.product_id
}

const amount = ref(props.amount)

const unit_price = ref(props.unit_price)

const unit_stock = ref()

if (props.unit_stock !== 0) {
    unit_stock.value = props.unit_stock
}

const total = ref(props.total)

const required = ref([
    value => {
          if (value) return true

          return 'El campo es requerido'
    },
])

const decimal = ref([
    value => {
        if (/^\d+(\.\d{1,2})?$/.test(value)) return true

        return 'Debe ser un valor decimal'
    }
])

watch(product_id, (newProductId, oldProductId) => {

    const product = props.products.find(product => product.id === newProductId)

    orderStore.setOrderItem_product_id(props.index, product.id)

    if (product) {
        unit_price.value = Number(product.price)

        unit_stock.value = Number(product.stock)

        amount.value = unit_stock.value

        orderStore.setOrderItem_unit_stock(props.index, unit_stock.value)

        orderStore.setOrderItem_unit_price(props.index, unit_price.value)

        orderStore.setOrderItem_unit_name(props.index, product.name)

        total.value = Number(amount.value * unit_price.value)

        orderStore.setOrderItem_amount(props.index, Number(amount.value))

        orderStore.setOrderItem_total(props.index, total.value)
    }

})

watch([amount, unit_price], ([newAmount, newUnitPrice], [oldAmount, oldUnitPrice]) => {

    if (unit_stock.value !== 0) {
        if (newAmount > unit_stock.value) {
            amount.value = unit_stock.value
        }
    }

    total.value = Number(newAmount * newUnitPrice)

    orderStore.setOrderItem_amount(props.index, Number(newAmount))
    orderStore.setOrderItem_unit_price(props.index, Number(newUnitPrice))
    orderStore.setOrderItem_total(props.index, total.value)
})

const removeRow = () => {
    orderStore.removeItem(Number(props.index))
}
</script>

<template>
    <div class="flex justify-between items-center ">
        <v-container>
            <v-row class="justify-center align-center">
                <v-col
                    cols="12"
                    sm="1"
                >
                    # {{ index + 1 }}
                </v-col>

                <v-col
                    cols="12"
                    sm="4"
                >
                    <v-autocomplete label="Producto (*)"
                        item-title="name" item-value="id" density="compact"
                        color="primary" variant="outlined" hide-details
                        :items="products" v-model="product_id"
                        :rules="required"
                    >

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

                <v-col
                    cols="12"
                    sm="2"
                >
                    <v-text-field
                    label="Precio por unidad"
                    color="primary"
                    variant="outlined"
                    maxlength="10"
                    v-model="unit_price"
                    counter
                    hide-details
                    :rules="decimal"
                    density="compact"
                    type="number"
                    ></v-text-field>
                </v-col>

                <v-col
                    cols="12"
                    sm="2"
                >
                    <v-text-field
                        label="Cantidad"
                        color="primary"
                        variant="outlined"
                        maxlength="10"
                        v-model="amount"
                        counter
                        hide-details
                        :rules="decimal"
                        density="compact"
                        type="number"
                    ></v-text-field>
                </v-col>

                <v-col
                    cols="12"
                    sm="2"
                >
                    <v-text-field
                        label="Total"
                        color="primary"
                        variant="outlined"
                        maxlength="10"
                        v-model="total"
                        counter
                        hide-details
                        :rules="decimal"
                        disabled
                        density="compact"
                        type="number"
                    >
                    </v-text-field>
                </v-col>

                <v-col
                    cols="12"
                    sm="1"
                    class="text-center"
                >
                    <v-btn
                        color="error"
                        @click="removeRow"
                        hide-details
                        density="compact"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>

                </v-col>
            </v-row>
        </v-container>
    </div>
</template>
