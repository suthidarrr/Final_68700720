import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js' 

import App from './App.vue'
import router from './router'
import store from './store'

import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

createApp(App).use(store).use(router).mount('#app')



