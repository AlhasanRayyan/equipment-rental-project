import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
       resolve: {
        alias: {
            'uikit-scss': 'uikit/src/scss/uikit-theme.scss',
            'uikit-js': 'uikit/dist/js/uikit.min.js',
            'uikit-icons-js': 'uikit/dist/js/uikit-icons.min.js',
        },
    },
});
