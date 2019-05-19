import './bootstrap';
import TemplateComponent from './components/TemplateComponent';
import Vue from 'vue';
import VueRouter from 'vue-router';
import router from './router';
import VueApexCharts from 'vue-apexcharts';
import DatatableFactory from 'vuejs-datatable';

Vue.use(VueRouter);

Vue.use(DatatableFactory);

Vue.component('apexchart', VueApexCharts);

Vue.component('template-component', TemplateComponent);

const app = new Vue({
    el: '#app',
    router: router,
    render: h => h('template-component')
});
