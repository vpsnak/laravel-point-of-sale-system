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
    if (!store.getters.auth) {
      store.commit("logout");
    } else {
      next();
    }
  } else {
    next();
  }

  // guard login route if already logged in
  if (
    to.matched.some(record => record.name === "login") &&
    store.getters.auth
  ) {
    next({ name: "dashboard" });
  }

  // guard sale page if no register is selected
  else if (
    to.matched.some(record => record.name === "sale") &&
    !store.state.cashRegister
  ) {
    next({
      path: "/open-cash-register",
      query: { redirect: to.fullPath }
    });
  }

  // guard sale page if no register is selected
  else if (
    to.matched.some(record => record.name === "orders") &&
    !store.state.cashRegister
  ) {
    next({
      path: "/open-cash-register",
      query: { redirect: to.fullPath }
    });
  }

  // guard open register route if already logged in
  else if (
    to.matched.some(record => record.name === "openCashRegister") &&
    store.state.cashRegister
  ) {
    next({ name: "dashboard" });
  } else {
    next();
  }
});

export default router;
