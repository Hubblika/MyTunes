import '../css/app.css';

import { createApp, h, type DefineComponent, computed } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import i18n from './i18n';
import { createPinia } from 'pinia';
import { usePlayerStore } from './stores/player';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);

        vueApp.use(PrimeVue, {
            theme: {
                preset: Aura
            }
        });

        const pinia = createPinia()
        vueApp.use(pinia)

        vueApp.use(i18n);

        const player = usePlayerStore(pinia);

        window.addEventListener('keydown', (e) => {
            const active = document.activeElement as HTMLElement | null;

            const isTyping =
                active?.tagName === 'INPUT' ||
                active?.tagName === 'TEXTAREA' ||
                active?.isContentEditable;

            const hasTrack = computed(() => !!player.currentTrack)

            if (e.code === 'Space' && !isTyping && !hasTrack) {
                e.preventDefault();
                player.togglePlay();
            }
        });

        vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
