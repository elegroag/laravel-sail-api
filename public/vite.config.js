import inject from '@rollup/plugin-inject';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
const __dirname = dirname(fileURLToPath(import.meta.url));
const _name = process.env.MODULE;
const _app = process.env.APP;

const camelCase = (str) => {
    return str.replace(/\b\w/g, (l) => l.toUpperCase());
};

export default defineConfig({
    mode: 'development', // o production
    root: '.',
    base: './',
    build: {
        chunkSizeWarningLimit: 300,
        sourcemap: true,
        emptyOutDir: false,
        outDir: resolve(__dirname, `${_app}/build/`),
        rollupOptions: {
            input: {
                main: resolve(__dirname, `src/${camelCase(_app)}/${_name}/main.js`),
            },
            output: {
                entryFileNames: `${camelCase(_name)}.js`,
                assetFileNames: `${camelCase(_name)}.[ext]`,
            },
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
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
