import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
const __dirname = dirname(fileURLToPath(import.meta.url));
const _name = process.env.MODULE;
const _app = process.env.APP;

const CamelCase = (str) => {
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
                main: resolve(__dirname, `src/${CamelCase(_app)}/${_name}/main.js`),
            },
            output: {
                entryFileNames: `${CamelCase(_name)}.js`,
                assetFileNames: `${CamelCase(_name)}.[ext]`,
            },
            inject: {
                flatpickr: 'flatpickr',
                Choices: 'choices.js',
                Spanish: 'flatpickr/dist/l10n/es',
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
});
