require('./bootstrap');
import { createApp } from 'vue'
import App from './components/App.vue'

createApp({
    components: { App }
}).mount('#app')