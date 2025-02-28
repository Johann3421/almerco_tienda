import { defineStore } from "pinia";

export const useOrderItemRowRemove = defineStore("OrderItemRowRemove", {
    state: () => ({
        index: 0,
    }),
    actions: {
        sleep(ms: number) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
    getters: {
        orderItemRowRemove() {
            return this.index;
        },
        
    },
});
