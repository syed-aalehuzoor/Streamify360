import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/css/jwplayer.css', 'resources/js/app.js', 'resources/js/jwplayer.js', 'resources/js/amodal.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@fortawesome': '/node_modules/@fortawesome',
        },
    },
});
