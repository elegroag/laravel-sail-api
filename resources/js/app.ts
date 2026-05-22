import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from '@inertiajs/vite/helpers';
import { createSSRApp, h, type SSRContext } from 'vue';
import { App } from '@inertiajs/vue3';
import '@/assets/css/global.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => {
        return resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue'));
    },
    setup({ el, App, props }) {
        const app = createSSRApp({ render: () => h(App, props) });
        app.config.errorHandler = (err, instance, info) => {
            console.error('Vue Error:', err, info);
        };
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});