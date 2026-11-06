import { createApp } from 'vue';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { createHead } from '@vueuse/head';
import App from './App.vue';
import router from './router';
import store from './store';
import axios from './axios';
import { formatCurrency } from './utils/formatters';


const app = createApp(App);
const head = createHead();
// app.use(VueMeta);
app.config.globalProperties.$currency = formatCurrency;
app.use(head);
app.use(store);
app.use(router);
const options = {
    timeout: 1500,
    toastClassName: 'custom-toast',
    bodyClassName: 'custom-toast-body',
};
app.use(Toast, options);
app.config.globalProperties.$axios = axios;

store.dispatch('auth/fetchUser').finally(() => {
  app.use(router).mount('#app');
});
// app.mount('#app');
