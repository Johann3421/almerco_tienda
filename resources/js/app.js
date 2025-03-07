import './bootstrap';
import '../css/app.css';
import axios from 'axios';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
import vuetify from './Plugins/Vuetify';

const pinia = createPinia()

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => {
        if (!title || title === "Web") {
            return "Mi Producto"; // Título por defecto si no se recibe uno válido
        }
        return `${title} | Mi Producto`;
    },
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {

        const app = createApp({
            render: () => h(App, props),
        });

        app.use(pinia);
        app.use(plugin);
        app.use(vuetify);
        app.use(ZiggyVue, Ziggy);

        app.config.globalProperties.$route = route
        
        app.mount(el)

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
