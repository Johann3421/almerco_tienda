import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const aliases = {
    "@assets": "/resources/assets",
    "@stores": "/resources/stores",
    "@utils": "/resources/utils",
    "@entities": "/resources/entities",
    "@": "/resources/js",
};

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: aliases,
    },

});
