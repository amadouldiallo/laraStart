
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
import VueRouter from 'vue-router'
import moment from 'moment'
import VueProgressBar from 'vue-progressbar'
import swal from 'sweetalert2'
import Gate from './Gate'

Vue.prototype.$gate = new Gate(window.user)

window.swal = swal

// creating custom event
window.Fire = new Vue()


const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton:false,
    timer: 3000
});

window.toast = toast


Vue.component(AlertError.name, AlertError)
Vue.component(HasError.name, HasError)

Vue.use(VueProgressBar,{
    color: 'rgb(149,62,89)',
    failedColor:'red',
    height: '2px'
})


Vue.use(VueRouter)


Vue.filter('upText',function(text){
    return text.charAt(0).toUpperCase()+ text.slice(1)
})
Vue.filter('myDate', function(created){
    return moment(created).format('MMMM Do YYYY');
});


let routes = [{
        path: '/dashboard',
        component: require('./components/Dashboard.vue')
    },
    {
        path: '/profile',
        component: require('./components/Profile.vue')
    },
    {
        path: '/developper',
        component: require('./components/Developper.vue')
    },
    {
        path: '/users',
        component: require('./components/Users.vue')
    },
    {
        path: '*',
        component: require('./components/NotFound.vue')
    }
]

let router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);
Vue.component(
    'not-found',
    require('./components/NotFound.vue')
);

Vue.component('pagination', require('laravel-vue-pagination'));

Vue.component('example-component', require('./components/ExampleComponent.vue'));

// const files = require.context('./', true, /\.vue$/i)

// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data: {
        search: ''
    },
    methods:{

    }
});
