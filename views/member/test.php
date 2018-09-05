<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Demo</title>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
</head>
<body>
<div id="app">
    <h1>Hello App!</h1>
    <p>
        <!-- use router-link component for navigation. -->
        <!-- specify the link by passing the `to` prop. -->
        <!-- `<router-link>` will be rendered as an `<a>` tag by default -->
        <router-link to="/member/test/foo">Go to Foo</router-link>
        <router-link to="/member/test/bar">Go to Bar</router-link>
        <router-link to="/member/test/user/1234">Go to user</router-link>
    </p>
    <!-- route outlet -->
    <!-- component matched by the route will render here -->
    <router-view></router-view>
</div>

<script>
    // 0. If using a module system (e.g. via vue-cli), import Vue and VueRouter and then call `Vue.use(VueRouter)`.

    // 1. Define route components.
    // These can be imported from other files
    const Foo = { template: '<div>Foo</div>' };
    const Bar = { template: '<div>Bar</div>' };
    const User = {
        props: ['id'],
        template: '<div>User {{ id }}</div>'
    };
    const NotFoundComponent = { template: '<div>Not found</div>' };

    // 2. Define some routes
    // Each route should map to a component. The "component" can
    // either be an actual component constructor created via
    // `Vue.extend()`, or just a component options object.
    // We'll talk about nested routes later.
    const routes = [
        { path: '/member/test/foo', component: Foo },
        { path: '/member/test/bar', component: Bar },
        { path: '/member/test/user/:id', component: User, props: true },
        { path: '*', component: NotFoundComponent }
    ];

    // 3. Create the router instance and pass the `routes` option
    // You can pass in additional options here, but let's
    // keep it simple for now.
    const router = new VueRouter({
        mode: 'history',
        routes: routes
    });

    //Vue.use(require('vue-chartist'));

    // 4. Create and mount the root instance.
    // Make sure to inject the router with the router option to make the
    // whole app router-aware.
    const app = new Vue({
        router: router
    }).$mount('#app');

    // Now the app has started!
</script>
</body>
</html>