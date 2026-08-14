import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            // detectTls debe ir en las opciones del plugin, no en server:
            // usa los certificados de Herd/Valet para servir el dev server por HTTPS.
            detectTls: 'finanzas.test',
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
    // Escuchar en IPv4 (127.0.0.1) para evitar que Node se bindee solo a [::1]
    server: {
        host: '127.0.0.1',
    },
});