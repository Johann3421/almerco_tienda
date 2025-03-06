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
    build: {
        rollupOptions: {
            output: {
                format: 'iife', // Usar formato compatible con navegadores
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
    },
});
