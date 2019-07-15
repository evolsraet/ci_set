define(function (require) {
    let Vue = require('/assets/lib/vue.js');
    let VueRouter = require('/assets/lib/vue-router.js');

    const routes = [
        { path: '/admin/v/foo', component: Foo },
        { path: '/admin/v/bar', component: Bar },
        { path: '/admin/v/user/:id', component: User, props: true },
        { path: '*', component: NotFoundComponent }
    ];

    const router = new VueRouter({
        mode: 'history',
        routes: routes
    });

    require('components/greeting');

    new Vue({
        el: '#app_wrapper',
        data: {
            msg: 'Hello',
        }
    });
});