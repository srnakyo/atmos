import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

const pagesDir = path.resolve(__dirname, 'resources/js/pages');
const pageScripts = fs.existsSync(pagesDir)
    ? fs.readdirSync(pagesDir)
        .filter(file => file.endsWith('.js'))
        .map(file => `resources/js/pages/${file}`)
    : [];

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
                ...pageScripts,
            ],
            refresh: true,
        }),
    ],
});