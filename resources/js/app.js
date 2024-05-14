import { createApp } from 'vue';
import App from './app/App.vue';
import { createPinia } from 'pinia';
import router from './app/router';

//createApp(App).mount('#app');
const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');
