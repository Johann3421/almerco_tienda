import { defineStore } from "pinia";

export const useProductStore = defineStore("ProductStore", {
    state: () => ({
        products: [],
        loading: false,
    }),
    actions: {
        async fetchProducts() {
            this.loading = true;
            await this.sleep(1000);

            this.products = [
                {
                    id: 1,
                    name: "Product 1",
                    price: 1000,
                    stock: 10,
                },
                {
                    id: 2,
                    name: "Product 2",
                    price: 2000,
                    stock: 20,
                },
                {
                    id: 3,
                    name: "Product 3",
                    price: 3000,
                    stock: 30,
                },
                {
                    id: 4,
                    name: "Product 4",
                    price: 4000,
                    stock: 40,
                },
                {
                    id: 5,
                    name: "Product 5",
                    price: 5000,
                    stock: 50,
                },
                {
                    id: 6,
                    name: "Product 6",
                    price: 6000,
                    stock: 60,
                },
                {
                    id: 7,
                    name: "Product 7",
                    price: 7000,
                    stock: 70,
                },
            ];

            this.loading = false;
        },
        sleep(ms: number) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
    getters: {
        getProducts: (state) => state.products,
        getLoading: (state) => state.loading,
    },
});
