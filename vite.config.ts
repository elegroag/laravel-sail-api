import inertia from '@inertiajs/vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import { env } from 'node:process';

const vite_url = env.VITE_HOST + ':' + env.VITE_PORT || 'http://0.0.0.0:5176';

export default defineConfig(({ command }) => {
    return {
        define:
            command === 'serve'
                ? { __PROJECT_ROOT__: JSON.stringify(process.cwd()) }
                : {},
        mode: env.NODE_ENV || 'development',
        root: '.',
        server: {
            port: 5176,
            host: '0.0.0.0',
            proxy: {
                '/api': {
                    target: vite_url,
                    changeOrigin: true,
                },
            },
            hmr: {
                overlay: false,
            },
        },
        plugins: [
            laravel({
                input: 'resources/js/app.ts',
                refresh: true,
            }),
            inertia(),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            tailwindcss(),
        ],
        build: {
            sourcemap: true,
            chunkSizeWarningLimit: 500,
            emptyOutDir: false,
            minify: false,
        },
        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/js'),
                '~': resolve(__dirname, 'resources/js'),
                '~~': resolve(__dirname, '.'),
                '@components': resolve(__dirname, 'resources/js/components'),
                '@composables': resolve(__dirname, 'resources/js/composables'),
                '@pages': resolve(__dirname, 'resources/js/pages'),
                '@shared': resolve(__dirname, 'resources/js/shared'),
            },
        },
    };
});