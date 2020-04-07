import Vue from "vue";
import Vuex from "vuex";

import "es6-promise/auto";

import router from "../plugins/router";
//modules
import requests from "./modules/requests";
import config from "./modules/config";
import Cookies from "js-cookie";
import menu from "./modules/menu";
import cart from "./modules/cart";
import datatable from "./modules/datatable";
import dialog from "./modules/dialog";
import icons from "./modules/icons";

Vue.use(Vuex);

export default new Vuex.Store({
  strict: false,
  modules: {
    requests,
    config,
    menu,
    cart,
    datatable,
    dialog,
    icons,
  },
  state: {
    user: Cookies.getJSON("user"),
    token: Cookies.get("token"),

    cashRegister: null,

    // notification
    notification: {
      msg: "",
      type: "",
    },

    productList: [],
    storeList: [],
  },
  getters: {
    auth: (state) => {
      if (state.user && state.token) {
        return true;
      } else {
        return false;
      }
    },
    role: (state) => {
      if (state.user) {
        return state.user.roles[0].name;
      } else {
        return false;
      }
    },
  },
  mutations: {
    updateUserSetting(state, setting) {
      const index = _.findIndex(state.user.settings, {
        key: setting.key,
      });
      state.user.settings[index] = setting;
      state.config.Cookies.set("user", state.user);
    },
    setProductList(state, value) {
      state.productList = value;
    },
    logout(state) {
      if (_.has(state.user, "id")) {
        state.config.Echo.leave(`user.${state.user.id}`);
      }

      state.config.Cookies.remove("user");
      state.config.Cookies.remove("token");

      state.user = null;
      state.token = null;
      state.cashRegister = null;

      state.cart.tax_percentage = 0;

      state.menu.store_name = "";
      state.menu.top_menu = [];
      state.menu.side_menu = [];

      if (router.currentRoute.name !== "login") {
        router.replace({ name: "login" });
      }
    },
    setCashRegister(state, cashRegister) {
      state.cashRegister = cashRegister;

      if (
        !cashRegister &&
        ["sale", "orders"].indexOf(router.currentRoute.name) !== -1
      ) {
        router.replace({ name: "dashboard" });
      }
    },
    setUser(state, user) {
      if (user) {
        state.user = user;
        state.config.Cookies.set("user", user);
      } else {
        state.user = null;
        state.config.Cookies.remove("user");
      }
    },
    setToken(state, token) {
      if (token) {
        state.token = token;
        state.config.Cookies.set("token", state.token);
      } else {
        state.token = null;
        state.config.Cookies.remove("token");
      }
    },
    setNotification(state, notification) {
      state.notification = [];
      state.notification = notification;
    },
  },
  actions: {
    login(context, data) {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "post",
          url: "auth/login",
          data: data,
          no_error_notification: true,
        };
        context
          .dispatch("requests/request", payload)
          .then((response) => {
            context.commit("setToken", response.token);
            context.commit("setUser", response.user);

            resolve(response);
          })
          .catch((error) => {
            context.commit("setNotification", {
              msg: "Invalid credentials",
              type: "error",
            });

            reject(error);
          });
      });
    },
    logout(context) {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "get",
          url: "auth/logout",
        };
        context
          .dispatch("requests/request", payload)
          .then(() => {
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          })
          .finally(() => {
            context.commit("config/resetLoad");
            context.commit("logout");
          });
      });
    },
    openCashRegister(context, payload) {
      return new Promise((resolve, reject) => {
        payload.method = "post";
        payload.url = "cash-register-logs/open";
        context
          .dispatch("requests/request", payload)
          .then((response) => {
            context.commit("setCashRegister", response.cash_register);
            context.commit("menu/setStoreName", response.store_name);
            context.commit("cart/setTaxPercentage", response.tax_percentage);

            resolve(response.data);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    cashRegisterLogout(context) {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "get",
          url: "cash-register-logs/logout",
        };
        context
          .dispatch("requests/request", payload)
          .then((response) => {
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          })
          .finally(() => {
            context.commit("setCashRegister", null);
            context.commit("menu/setStoreName", null);
            context.commit("cart/setTaxPercentage", null);
          });
      });
    },
    closeCashRegister(context, payload) {
      return new Promise((resolve, reject) => {
        payload.method = "post";
        payload.url = "cash-register-logs/close";
        context
          .dispatch("requests/request", payload)
          .then((response) => {
            context.commit("setCashRegister", null);
            context.commit("menu/setStoreName", null);

            resolve(response.data);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  },
});

export const store = new Vuex.Store();
