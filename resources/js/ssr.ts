import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from '@inertiajs/vite/helpers';
import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { App } from '@inertiajs/vue3';
import '@/assets/css/global.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => {
        return resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue'));
    },
    setup({ el, App, props }) {
        return createSSRApp({ render: () => h(App, props) });
    },
    progress: {
        color: '#4B5563',
    },
});