import './bootstrap';
import '../css/app.css';
import '../css/menu.css';
import '../css/dashboard.css';
import '../css/income.css';
import '../css/loan.css';
import '../css/legal.css';
import '../css/landing.css';
import '../css/profile.css';
import '../css/topbar.css';
import '../css/calendar.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueApexCharts from "vue3-apexcharts";
import dayjs from 'dayjs';
import 'dayjs/locale/es';

// Format tokens used by Element Plus date pickers follow the app locale
dayjs.locale('es');

//element plus
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import locale from 'element-plus/dist/locale/es.mjs';
import 'element-plus/theme-chalk/dark/css-vars.css';
import '../css/auth.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ElementPlus, { locale })
            .use(VueApexCharts)
            .mount(el);
    },
    progress: {
        color: '#296A6B',
    },
});
