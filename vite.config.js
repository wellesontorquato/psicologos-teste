import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            output: {
                manualChunks: undefined,
            },
        },
        manifestTransforms: [
            (manifest) => {
                for (const key in manifest) {
                    if (manifest[key].file) {
                        manifest[key].file = manifest[key].file.replace('.vite/', '');
                    }
                    if (manifest[key].src) {
                        manifest[key].src = manifest[key].src.replace('.vite/', '');
                    }
                }
                return manifest;
            },
        ],
    },
    publicDir: false,
});
