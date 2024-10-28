import "./bootstrap";

import { createApp } from "vue";
import App from "./App.vue";
import ToastPlugin from 'vue-toast-notification';

import 'vue-toast-notification/dist/theme-bootstrap.css';

createApp(App).use(ToastPlugin).mount("#app");
