import Vue from "vue";
import PortalVue from 'portal-vue'

require('./bootstrap');

window.Vue = require('vue').default;
Vue.use(PortalVue)

Vue.component('new-sponsorship-page', require('./components/NewSponsorshipPage.vue').default);

const app = new Vue({
    el: '#app',
});
