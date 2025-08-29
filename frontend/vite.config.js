import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react({
            jsxRuntime: 'automatic',
            include: "**/*.{jsx,tsx}",
        }),
    ],
    root: '.',
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
    },
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: {
                main: './index.html',
            },
        },
    },
    resolve: {
        alias: {
            '@': '/src',
        },
    },
});
