import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
// import './bootstrap'; // Ini biasanya berisi axios
// import './echo';      // <--- WAJIB ADA INI
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        host: true,
        hmr: {
            host: 'localhost'
        }
    },
});
