import Vue from "vue";
import VueRouter from "vue-router";
import routes from "./routes";
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
        if (!store.state.token) {
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

    // guard login route if already logged in
    if (
        to.matched.some(record => record.name === "login") &&
        store.state.token
    ) {
        next({ path: "/" });
    }

    // guard sales page if no register is selected
    else if (
        to.matched.some(record => record.name === "sales") &&
        !store.getters.openedRegister
    ) {
        next({
            path: "/open-cash-register",
            query: { redirect: to.fullPath }
        });
    }

    // guard sales page if no register is selected
    else if (
        to.matched.some(record => record.name === "orders") &&
        !store.getters.openedRegister
    ) {
        next({
            path: "/open-cash-register",
            query: { redirect: to.fullPath }
        });
    }

    // guard open register route if already logged in
    else if (
        to.matched.some(record => record.name === "openCashRegister") &&
        store.getters.openedRegister
    ) {
        next({ path: "/" });
    } else {
        next();
    }
});

export default router;
