/*

/!**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 *!/

require('./bootstrap');

window.Vue = require('vue');

/!**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 *!/

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});
*/

import './assets/css/style2.css'; //^1.1.10, CSS has been extracted as one file, so you can easily update it.

import Vue from 'vue';
import Vuex from 'vuex';
//import store from './store'

import VueRouter from 'vue-router';

import axios from 'axios';
import VueAxios from 'vue-axios';
//import YMaps from 'vue-yandex-maps';

//import Hello from './views/Hello';
//import FAQ from './views/FAQ';
//import Speech from './views/Speech';
//import Service from './views/Service';
//import Register from './views/Register';
import Login from './views/Login';
//import Dashboard from './views/Dashboard';
//import Admin from './views/admin';
//import Verify from './views/Verify';
//import ApplicationIndex from './views/ApplicationIndex.vue';
//import Client from './views/Client';
//import PersonalData from './views/PersonalData.vue';
//import Offer from './views/offer';
//import RegisterAlienWorker from './views/RegisterAlienWorker';
//import ConfirmWorker from './views/ConfirmWorker';
//import ConfirmWorkerOccXpTime from './views/ConfirmWorkerOccXpTime';
//import Citizen from './views/Citizen.vue';
//import WorkerList from './views/WorkerList';
//import AccCreatePayment from './views/AccCreatePayment';
//import AccPayments from './views/AccPayments';
//import SkillLevels from './views/SkillLevels';
//import SkillsHowTo from './views/SkillsHowTo';
//import Rules from './views/Rules';
//import ImageUploader from 'vue-image-upload-resize';
//import Calendar from './components/Calendar';
/*import FunctionalCalendar from 'vue-functional-calendar';
Vue.use(FunctionalCalendar, {
    dayNames: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']
});*/
//import Calendar from 'vue2-event-calendar'
//Vue.component('Calendar', Calendar)
//import VCalendar from 'v-calendar';
//import 'v-calendar/lib/v-calendar.min.css';
//Vue.use(VCalendar);

import App from './views/App';
import Home from './views/Home';
import About from './views/About';
import ApplicationCreateOrEdit from './views/ApplicationCreateOrEdit.vue';
import ApplicationCard from './views/ApplicationCard.vue';
import AccountancyCreate from './views/AccountancyCreate';
import AccDetailDay from './views/AccDetailDay';
import AccDetailMonth from './views/AccDetailMonth';
import Applications from './views/Applications';
import AICreate from './views/Accountancy/Instagram/Create';
import AIDetailDay from './views/Accountancy/Instagram/DetailDay';
import PrivilegeErrorPage from './views/PrivilegeErrorPage';

import 'vue-tel-input/dist/vue-tel-input.css';
import {Checkbox, Radio} from 'vue-checkbox-radio';
import vueEventCalendar from 'vue-event-calendar';
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css'
import VueClipboard from 'vue-clipboard2'
import VueTheMask from 'vue-the-mask'


Vue.use(VueTheMask);
Vue.use(VueClipboard);
Vue.use(VueGoodTablePlugin);
// locale can be 'zh' , 'en' , 'es', 'pt-br', 'ja', 'ko', 'fr', 'it', 'ru', 'de', 'vi', 'ua', 'no, 'no-nn'
Vue.use(vueEventCalendar, {locale: 'ru', color:'green'});
Vue.component('checkbox', Checkbox);
Vue.component('radio', Radio);
//Vue.component('calendar', Calendar);

Vue.prototype.$axios = axios;
Vue.prototype.$window = window;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(Vuex);
//Vue.use(YMaps);
//Vue.use(ImageUploader);


//var bcrypt = require('bcryptjs');
//Vue.use(bcrypt);


const VueInputMask = require('vue-inputmask').default;
Vue.use(VueInputMask);

var VueScrollTo = require('vue-scrollto');
Vue.use(VueScrollTo);

// You can also pass in the default options
Vue.use(VueScrollTo, {
    container: "body",
    duration: 500,
    easing: "ease",
    offset: 0,
    force: true,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});


axios.defaults.baseURL = 'http://arbait.in/api';

var Object = {
    counter: 0
};

const router = new VueRouter(   {
    mode: 'history',
    routes: [
        {
            path: '/error',
            name: 'error',
            component: PrivilegeErrorPage
        },
        {
            path: '/',
            name: 'home',
            component: Home,
        },
        {
            path: '/about',
            name: 'about',
            component: About
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                auth: false
            }
        },
        {
            path: '/app/show/:id',
            component: ApplicationCard,
            name: 'applicationCard',
            meta: { label: 'Карточка заявки', title: 'Карточка заявки' }
        },
        {
            path: '/app/create',
            component: ApplicationCreateOrEdit,
            name: 'applicationCreateOrEdit',
            meta: { label: 'Создать заявку', title: 'Создать заявку' }
        },
        {
            path: '/app/create/:dispatcher_name',
            component: ApplicationCreateOrEdit,
            name: 'applicationCreate',
            meta: { label: 'Создать заявку', title: 'Создать заявку' }
        },
        {
            path: '/app/edit/:id',
            component: ApplicationCreateOrEdit,
            name: 'applicationEdit',
            meta: { label: 'Редактировать заявку', title: 'Редактировать заявку' }
        },
        {
            path: '/apps',
            component: Applications,
            name: 'applications',
            meta: { label: 'Заявки', title: 'Заявки' }
        },

        {
            path: '/zayavki',
            component: Applications,
            name: 'zayavki',
            meta: { label: 'Заявки Насти', title: 'Заявки Насти' }
        },
        {
            path: '/zayavka/create/:dispatcher_name',
            component: ApplicationCreateOrEdit,
            name: 'zayavkaCreate',
            meta: { label: 'Создать заявку', title: 'Создать заявку' }
        },
        {
            path: '/zayavka/edit/:id',
            component: ApplicationCreateOrEdit,
            name: 'zayavkaEdit',
            meta: { label: 'Редактировать заявку', title: 'Редактировать заявку' }
        },

        {
            path: '/apps/:page',
            component: Applications,
            name: 'applications_with_page',
            meta: { label: 'Заявки', title: 'Заявки' }
        },

        {
            path: '/apps/account/:page',
            component: Applications,
            name: 'account_applications_with_page',
            meta: { label: 'Заявки на РС', title: 'Заявки на РС' }
        },

        {
            path: '/accountancy/create',
            component: AccountancyCreate,
            name: 'accountancy',
            meta: { label: 'Отчет: арбайт', title: 'Отчет: арбайт' }
        },
        {
            path: '/accountancy/create/:dispatcher_name',
            component: AccountancyCreate,
            name: 'accountancy',
            meta: { label: 'Отчет: арбайт', title: 'Отчет: арбайт' }
        },
        {
            path: '/accountancy/detail/day',
            component: AccDetailDay,
            name: 'accDetailDay',
            meta: {label: 'Детализация за день: арбайт', title: 'Детализация за день: арбайт'}
        },
        {
            path: '/accountancy/detail/day/:date',
            component: AccDetailDay,
            name: 'accDetailDay',
            meta: {label: 'Детализация за день: арбайт', title: 'Детализация за день: арбайт'}
        },
        {
            path: '/accountancy/detail/month',
            component: AccDetailMonth,
            name: 'accDetailMonth',
            meta: {label: 'Детализация за месяц: арбайт', title: 'Детализация за месяц: арбайт'}
        },

        /* INSTAGRAM */
        {
            path: '/accountancy/instagram/create',
            component: AICreate,
            name: 'accountancyInstagram',
            meta: { label: 'Заполнить отчет в Instagram', title: 'Отчет: Instagram' }
        },
        {
            path: '/accountancy/instagram/create/:public_name',
            component: AICreate,
            name: 'accountancyInstagram',
            meta: { label: 'Заполнить отчет в Instagram', title: 'Отчет: Instagram' }
        },
        {
            path: '/accountancy/instagram/detail/day',
            component: AIDetailDay,
            name: 'accInstagramDetailDay',
            meta: {label: 'Детализация за день: Instagram', title: 'Детализация за день: Instagram'}
        },
        {
            path: '/accountancy/instagram/detail/day/:date',
            component: AIDetailDay,
            name: 'accInstagramDetailDay',
            meta: {label: 'Детализация за день: Instagram', title: 'Детализация за день: Instagram'}
        },
    ],
});

let downloadAppList = true, compName = 'none';

router.beforeEach((to, from, next) => {

    if (to.name == 'applications') {
        if (from.name == 'applicationCreate' ||
            from.name == 'applicationCard' ||
            from.name == 'applicationEdit' ||
            from.name == 'accountancy' ||
            from.name == 'accountancyInstagram')
        {
            downloadAppList= false;
        }
        compName = 'application';
    }

    const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

    if (nearestWithTitle) {
        document.title = nearestWithTitle.meta.title;
        document.downloadAppList = downloadAppList;
    }


    next();
});

Vue.router = router;
if (compName == 'application') {

}

Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
});

const store = new Vuex.Store({
    state: {
        count: 1
    },
    mutations: {
        initialiseStore(state) {
            // Check if the ID exists
            if(localStorage.getItem('store')) {
                // Replace the state object with the stored item
                this.replaceState(
                    Object.assign(state, JSON.parse(localStorage.getItem('store')))
                );
            }
        }
    },
    getters: {}
});

store.subscribe((mutation, state) => {
    // Store the state object as a JSON string
    localStorage.setItem('store', JSON.stringify(state));
});

App.router = Vue.router;

const app = new Vue({
    el: '#app',
    components:
        { App },
        router,
        store,
    data: {
        display: true,
    },
    beforeCreate() {
        this.$store.commit('initialiseStore');
    },
    computed: {
        formattedDate() {
            //return dateFns.format(this.curr, 'MM/DD/YYYY');
        }
    },
});
