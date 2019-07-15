// Vue.js

Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");
Vue.http.headers.common['csrf_token'] = document.head.querySelector("[name=csrf_token]").content;


// These can be imported from other files
// import Foo from '/views/admin_vue/foo.js';
// const Foo = { template: '<div>Foo</div>' };

// const Foo = app.components.Foo;
const Bar = { template: '<div>Bar</div>' };
const User = {
    props: ['id'],
    template: '<div>User {{ id }}</div>'
};
const NotFoundComponent = { template: '<div>Not found</div>' };

// route
const routes = [
    { path: '/admin/v/foo', component: Foo },
    { path: '/admin/v/bar', component: literals },
    { path: '/admin/v/user/:id', component: User, props: true },
    { path: '*', component: NotFoundComponent }
];
// route
// const routes = [
//     { path: 'foo', component: Foo },
//     { path: 'bar', component: literals },
//     { path: 'user/:id', component: User, props: true },
//     { path: '*', component: NotFoundComponent }
// ];

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

// app
const App = new Vue({
    router: router,
    data: {
    	is_login: true,
    	nav: nav
    }
}).$mount('#app_wrapper');