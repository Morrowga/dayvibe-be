import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import vuetify from './vuetify';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import VueGoodTablePlugin from 'vue-good-table-next';
import { VueGoodTable } from "vue-good-table-next";
import 'vue-good-table-next/dist/vue-good-table-next.css'
import VueDatePicker from '@vuepic/vue-datepicker';
import { library } from '@fortawesome/fontawesome-svg-core';
import {fas} from "@fortawesome/free-solid-svg-icons";


library.add(fas)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueGoodTablePlugin)
            .use(ZiggyVue)
            .use(vuetify)
            .mixin({
                components:{
                    VueGoodTable,
                  FontAwesomeIcon,
                  VueDatePicker
                }
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
