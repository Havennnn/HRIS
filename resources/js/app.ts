import { initializeTheme } from '@/composables/useAppearance';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePage, setAppPages } from 'piacore/resolve-pages';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Load app pages and set them in the resolver
const appPages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
setAppPages(appPages);

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: resolvePage,
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
