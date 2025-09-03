import inject from '@rollup/plugin-inject';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
const __dirname = dirname(fileURLToPath(import.meta.url));
const _name = process.env.MODULE;

export default defineConfig({
    mode: 'development', // o production
    root: '.',
    base: './',
    build: {
        chunkSizeWarningLimit: 300,
        sourcemap: true,
        outDir: resolve(__dirname, `../resources/mercurio/build`),
        rollupOptions: {
            input: {
                main: resolve(__dirname, `src/Mercurio/${_name}/main.js`),
            },
            output: {
                entryFileNames: `${_name.toLowerCase()}.js`,
                assetFileNames: `${_name.toLowerCase()}.[ext]`,
            },
        },
    },
    resolve: {
        alias: {
            '@/': resolve(__dirname, 'src'),
            'src/': resolve(__dirname, 'src'),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
            },
        },
    },
    plugins: [
        inject({
            flatpickr: 'flatpickr',
            Choices: 'choices.js',
            Spanish: 'flatpickr/dist/l10n/es',
        }),
    ],
});
