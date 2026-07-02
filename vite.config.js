import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    /*
     * Base path is injected by the GitHub Actions build step:
     *   VITE_APP_BASE=/tap/  npm run build   → assets prefixed /tap/
     *   VITE_APP_BASE=/woc/  npm run build   → assets prefixed /woc/
     *
     * Falls back to '/' for local development so you don't need to
     * set the env variable locally.
     */
    base: process.env.VITE_APP_BASE ?? '/',
});