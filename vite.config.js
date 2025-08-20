import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/index.jsx'],
            refresh: true,
        }),
        react({
            jsxRuntime: 'automatic',
            include: "**/*.{jsx,tsx}",
        }),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
