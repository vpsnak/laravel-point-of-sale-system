import Vue from "vue";
import Vuex from "vuex";

import "es6-promise/auto";
import Cookies from "js-cookie";
import router from "../plugins/router";
//modules
import requests from "./modules/requests";
import config from "./modules/config";
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
    icons
  },
  state: {
    user: Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null,
    token: Cookies.get("token") || null,

    cashRegister: null,

    // notification
    notification: {
      msg: "",
      type: ""
    },

    productList: [],
    storeList: []
  },
  getters: {
    auth: state => {
      if (state.user && state.token) {
        return true;
      } else {
        return false;
      }
    },
    role: state => {
      if (state.user) {
        return state.user.roles[0].name;
      } else {
        return false;
      }
    }
  },
  mutations: {
    setProductList(state, value) {
      state.productList = value;
    },
    logout(state) {
      if (_.has(state.user, "id")) {
        state.config.echo.leave(`user.${state.user.id}`);
      }

      Cookies.remove("user");
      Cookies.remove("token");
      Cookies.remove("store");
      Cookies.remove("cash_register");

      state.user = null;
      state.token = null;
      state.cashRegister = null;

      state.cart.tax_percentage = 0;

      state.menu.store_name = "";
      state.menu.top_menu = [];
      state.menu.side_menu = [];

      if (router.currentRoute.name !== "login") {
        router.push({ name: "login" });
      }
    },
    setCashRegister(state, cashRegister) {
      state.cashRegister = cashRegister;
    },
    setUser(state, user) {
      if (user) {
        state.user = user;
        Cookies.set("user", user, {
          secure: state.config.app_env !== "development" ? true : false,
          sameSite: "strict"
        });
      } else {
        state.user = null;
        Cookies.remove("user");
      }
    },
    setToken(state, token) {
      if (token) {
        state.token = token;

        Cookies.set("token", state.token, {
          secure: state.config.app_env !== "development" ? true : false,
          sameSite: "strict"
        });
      } else {
        state.token = null;
        window.axios.defaults.headers.common["Authorization"] = null;

        Cookies.remove("token");
      }
    },
    setNotification(state, notification) {
      state.notification = [];
      state.notification = notification;
    }
  },
  actions: {
    login(context, payload) {
      return new Promise((resolve, reject) => {
        context
          .dispatch("requests/request", {
            method: "post",
            url: "auth/login",
            data: payload,
            no_error_notification: true
          })
          .then(response => {
            window.axios.defaults.headers.common["Authorization"] =
              response.token;

            context.commit("setToken", response.token);
            context.commit("setUser", response.user);

            resolve(response);
          })
          .catch(error => {
            context.commit("setNotification", {
              msg: "Invalid credentials",
              type: "error"
            });

            reject(error);
          });
      });
    },
    logout(context) {
      return new Promise((resolve, reject) => {
        const payload = {
          method: "get",
          url: "auth/logout"
        };
        context
          .dispatch("requests/request", payload)
          .then(() => {
            resolve(true);
          })
          .catch(error => {
            reject(error);
          })
          .finally(() => {
            context.commit("config/resetLoad");
            context.commit("logout");
          });
      });
    },
    getAll(context, payload) {
      return new Promise((resolve, reject) => {
        let page = payload.page ? "?page=" + payload.page : "";

        axios
          .get(`${context.state.config.base_url}/${payload.model}${page}`)
          .then(response => {
            if (_.has(payload, "mutation")) {
              context.commit(
                payload.mutation,
                response.data.data || response.data
              );
            }

            if (payload.dataTable) {
              resolve(response.data);
            } else {
              resolve(response.data.data || response.data);
            }
          })
          .catch(error => {
            if (_.has(error, "response.data.errors")) {
              context.commit("setNotification", {
                msg: error.response.data.errors,
                type: "error"
              });
            } else {
              console.error(error);
            }
            reject(error);
          });
      });
    },
    getOne(context, payload) {
      return new Promise((resolve, reject) => {
        axios
          .get(
            `${context.state.config.base_url}/${payload.model}/get/${payload.data.id}`
          )
          .then(response => {
            if (_.has(payload, "mutation")) {
              context.commit(payload.mutation, response.data, {
                root: true
              });
            }
            resolve(response.data.data || response.data);
          })
          .catch(error => {
            if (_.has(error, "response.data.errors")) {
              context.commit("setNotification", {
                msg: error.response.data.errors,
                type: "error"
              });
            } else {
              console.error(error);
            }
            reject(error);
          });
      });
    },
    search(context, payload) {
      return new Promise((resolve, reject) => {
        let page = payload.page ? "?page=" + payload.page : "";

        axios
          .post(
            `${context.state.config.base_url}/${payload.model}/search${page}`,
            payload
          )
          .then(response => {
            if (_.has(payload, "mutation")) {
              context.commit(
                payload.mutation,
                response.data.data || response.data
              );
            }
            if (payload.dataTable) {
              resolve(response.data);
            } else {
              resolve(response.data.data || response.data);
            }
          })
          .catch(error => {
            if (_.has(error, "response.data.errors")) {
              context.commit("setNotification", {
                msg: error.response.data.errors,
                type: "error"
              });
            } else {
              console.error(error);
            }
            reject(error);
          });
      });
    },
    openCashRegister(context, payload) {
      return new Promise((resolve, reject) => {
        payload.method = "post";
        payload.url = "cash-register-logs/open";
        context
          .dispatch("requests/request", payload)
          .then(response => {
            context.commit("setCashRegister", response.cash_register);
            context.commit("menu/setStoreName", response.store_name);
            context.commit("cart/setTaxPercentage", response.tax_percentage);

            resolve(response.data);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    cashRegisterLogout(context) {
      return new Promise((resolve, reject) => {
        context
          .dispatch("requests/request", {
            method: "get",
            url: "cash-register-logs/logout"
          })
          .then(response => {
            if (["sales", "orders"].indexOf(router.currentRoute.name) >= 0) {
              router.replace({ name: "dashboard" });
            }

            context.commit("setCashRegister", null);
            context.commit("cart/setTaxPercentage", null);

            resolve(true);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    closeCashRegister(context, payload) {
      return new Promise((resolve, reject) => {
        payload.method = "post";
        payload.url = "cash-register-logs/close";
        context
          .dispatch("requests/request", payload)
          .then(response => {
            if (["sales", "orders"].indexOf(router.currentRoute.name) >= 0) {
              router.push({ name: "dashboard" });
            }

            context.commit("setCashRegister", null);
            context.commit("menu/setStoreName", null);

            resolve(response.data);
          })
          .catch(error => {
            reject(error);
          });
      });
    },

    create(context, payload) {
      return new Promise((resolve, reject) => {
        let options;
        if (payload.data instanceof FormData) {
          options = {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          };
        }
        axios
          .post(
            `${context.state.config.base_url}/${payload.model}/create`,
            payload.data,
            options
          )
          .then(response => {
            if (_.has(payload, "mutation")) {
              context.commit(
                payload.mutation,
                response.data.data || response.data
              );
            }
            resolve(response.data.data || response.data);
          })
          .catch(error => {
            if (_.has(error, "response.data.errors")) {
              context.commit("setNotification", {
                msg: error.response.data.errors,
                type: "error"
              });
            } else {
              console.error(error);
            }

            reject(error);
          });
      });
    }
  }
});

export const store = new Vuex.Store();
