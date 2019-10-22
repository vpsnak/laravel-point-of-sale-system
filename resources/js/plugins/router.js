import Vue from "vue";
import VueRouter from "vue-router";
import routes from "../routes";
import store from "../store/store";

Vue.use(VueRouter);

export const router = new VueRouter({
    // mode: "history",
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!store.state.user.token) {
            next({
                path: "/login",
                query: { redirect: to.fullPath }
            });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
