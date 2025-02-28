import { defineStore } from "pinia";

export const useOrderStore = defineStore("OrderStore", {
    state: () => ({
        order: {
            id: "",
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
        },
    }),
    actions: {
        addItem() {
            this.order.items.push({
                id: 0,
                uuid: crypto.randomUUID(),
                product_id: 0,
                amount: 0,
                unit_price: 0,
                unit_stock: 0,
                unit_name: "",
                total: 0
            });
        },
        removeItem(index: number) {
            this.order.items.splice(index, 1);
            console.log("Item removed: ", index);
        },
        setOrderItem_product_id(index: number, product_id: number) {
            this.order.items[index].product_id = product_id;
        },
        setOrderItem_amount(index: number, amount: number) {
            this.order.items[index].amount = amount;
        },
        setOrderItem_unit_price(index: number, unit_price: number) {
            this.order.items[index].unit_price = unit_price;
        },
        setOrderItem_unit_stock(index: number, unit_stock: number) {
            this.order.items[index].unit_stock = unit_stock;
        },
        setOrderItem_unit_name(index: number, unit_name: string) {
            this.order.items[index].unit_name = unit_name;
        },
        setOrderItem_total(index: number, total: number) {
            this.order.items[index].total = total;
        },
        addItems(items: any) {
            this.order.items = items;
        },
        clearOrderItems() {
            this.order.items = [];
        },
        sleep(ms: number) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
    getters: {
        getOrder_items() {
            return this.order.items;
        },
    },
});
