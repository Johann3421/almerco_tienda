import { defineStore } from "pinia";

export const useImageStore = defineStore("ImageStore", {
    //state
    state: () => ({
        imageUrl: null,
        loading: false,
        working: false,
    }),
    //actions
    actions: {
        setImageUrl(url: string | null) {
            this.imageUrl = url;
        },
        setLoading(loading: boolean) {
            this.loading = loading;

            if (loading) {
                this.setImageUrl(null);
            }
        },
        setWorking(working: boolean) {
            this.working = working;
        },
        $reset() {
            this.imageUrl = null;
            this.loading = false;
            this.working = false;
        },
        sleep(ms: number) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
    //getters
    getters: {
        getImageUrl: (state) => state.imageUrl,
        getLoading: (state) => state.loading,
        getWorking: (state) => state.working,
    },
});
