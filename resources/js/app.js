require('./bootstrap');

window.Vue = require('vue').default;
Vue.component('new-sponsorship-page', require('./components/NewSponsorshipPage.vue').default);

const app = new Vue({
    el: '#app',
});
