import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
import axios from 'axios'

axios.defaults.withCredentials = true;
// Pastikan proxy Vite kita tangani /api
axios.defaults.baseURL = '/api'; 

const app = createApp(App)
app.use(router)
app.mount('#app')
