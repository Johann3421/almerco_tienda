import { defineStore } from "pinia";

export const useTitleHeaderStore = defineStore("counter", {
    state: () => {
        return { count: 2 };
    },
    actions: {
        increment() {
            this.count++;
        },
    },
});
