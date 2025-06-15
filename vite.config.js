import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import basicSsl from '@vitejs/plugin-basic-ssl'; // <--- IMPORT

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        basicSsl(), // <--- PLUGIN TUTAJ, użyte: npm install @vitejs/plugin-basic-ssl --save-dev

    ],
    server: {
        https: true, // Plugin sam się tym zajmie
        host: 'localhost',
    }
});
