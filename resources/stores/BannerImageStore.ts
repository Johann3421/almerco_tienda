import { defineStore } from "pinia";

export const useBannerImageStore = defineStore("BannerImageStore", {
    state: () => ({
        file: null,
        url: null,
        loading: false,
        working: false,
    }),
    actions: {
        setUrl(url: string | null) {
            this.url = url;
        },
        setFile(file: File | null) {
            this.file = file;
        },
        setLoading(loading: boolean) {
            this.loading = loading;

            if (loading) {
                this.setUrl(null);
                this.setFile(null);
            }
        },
        setWorking(working: boolean) {
            this.working = working;
        },
        $reset() {
            this.url = null;
            this.file = null;
            this.loading = false;
            this.working = false;
        },
        sleep(ms: number) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },
    },
    getters: {
        getFile: (state) => state.file,
        getUrl: (state) => state.url,
        getLoading: (state) => state.loading,
        getWorking: (state) => state.working,
    },
});
