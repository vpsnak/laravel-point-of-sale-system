const state = {
  inner_height: 0,
  verbose: process.env.NODE_ENV === "development" ? true : false,
  init_info: [],
  base_url: process.env.MIX_BASE_URL,
  app_env: process.env.NODE_ENV,
  app_name: process.env.MIX_APP_NAME,
  mas_env: "",
  menu_items: "",

  app_load: 0,

  Echo: {},
  Cookies: require("js-cookie"),
};

const mutations = {
  setInnerHeight(state, value) {
    state.inner_height = value;
  },
  setInitInfo(state, value) {
    const index = _.findIndex(state.init_info, { action: value.action });
    index === -1
      ? state.init_info.push(value)
      : (state.init_info[index] = value);
  },
  setMasEnv(state, value) {
    state.mas_env = value;
  },
  resetLoad(state) {
    state.app_load = 0;
  },
  addLoadPercent(state, value) {
    state.app_load += value;
  },
};

const actions = {
  initCookies(context) {
    context.state.Cookies.defaults = {
      secure: context.state.app_env !== "development" ? true : false,
      sameSite: "strict",
    };
  },
  getMenuItems(context) {
    context.state.verbose
      ? context.commit("setInitInfo", {
          action: "getMenuItems",
          status: "info",
          message: "LOADING",
        })
      : "";

    return new Promise((resolve, reject) => {
      const payload = {
        method: "get",
        url: "menu-items",
      };
      context
        .dispatch("requests/request", payload, { root: true })
        .then((response) => {
          context.commit("menu/setTopMenu", response.top_menu, {
            root: true,
          });

          context.commit("menu/setSideMenu", response.side_menu, {
            root: true,
          });

          context.state.verbose
            ? context.commit("setInitInfo", {
                action: "getMenuItems",
                status: "success",
                message: "OK",
              })
            : "";

          resolve(true);
        })
        .catch((error) => {
          context.state.verbose
            ? context.commit("setInitInfo", {
                action: "getMenuItems",
                status: "error",
                message: "FAILED",
              })
            : "";
          reject(error);
        });
    });
  },
  getMasEnv(context) {
    context.state.verbose
      ? context.commit("setInitInfo", {
          action: "getMasEnv",
          status: "info",
          message: "LOADING",
        })
      : "";

    return new Promise((resolve, reject) => {
      const payload = {
        method: "get",
        url: "mas/env",
      };
      context
        .dispatch("requests/request", payload, { root: true })
        .then((response) => {
          context.state.verbose
            ? context.commit("setInitInfo", {
                action: "getMasEnv",
                status: "success",
                message: "OK",
              })
            : "";

          context.commit("setMasEnv", response);

          resolve(true);
        })
        .catch((error) => {
          context.state.verbose
            ? context.commit("setInitInfo", {
                action: "getMasEnv",
                status: "error",
                message: "FAILED",
              })
            : "";
          reject(error);
        });
    });
  },
  setMasEnv(context, env) {
    return new Promise((resolve, reject) => {
      const payload = {
        method: "get",
        url: `mas/set/${env}`,
      };
      context
        .dispatch("requests/request", payload, { root: true })
        .then((response) => {
          context.commit("setMasEnv", response.payload);
          resolve(true);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  initWebSockets(context) {
    context.state.verbose
      ? context.commit("setInitInfo", {
          action: "initWebSockets",
          status: "info",
          message: "LOADING",
        })
      : "";

    return new Promise((resolve, reject) => {
      try {
        state.Echo = require("laravel-echo");
        state.Echo = new state.Echo.default({
          broadcaster: "pusher",
          key: process.env.MIX_PUSHER_APP_KEY,
          cluster: process.env.MIX_PUSHER_APP_CLUSTER,
          encrypted: true,
          auth: {
            headers: {
              Authorization: context.rootState.token,
            },
          },
        });

        context.state.verbose
          ? context.commit("setInitInfo", {
              action: "initWebSockets",
              status: "success",
              message: "OK",
            })
          : "";

        resolve(true);
      } catch (error) {
        console.error(error);
        context.state.verbose
          ? context.commit("setInitInfo", {
              action: "initWebSockets",
              status: "error",
              message: "FAILED",
            })
          : "";

        reject(error);
      }
    });
  },
  initChannels(context) {
    context.state.verbose
      ? context.commit("setInitInfo", {
          action: "initChannels",
          status: "info",
          message: "LOADING",
        })
      : "";
    return new Promise((resolve, reject) => {
      try {
        state.Echo.private(`user.${context.rootState.user.id}`)
          .listen("CashRegisterLogin", (e) => {
            e.mutations.forEach((mutation) => {
              context.commit(mutation.name, mutation.data, mutation.root);
            });

            context.commit("setNotification", e.notification, {
              root: true,
            });
          })
          .listen("UserDeauth", (e) => {
            e.actions.forEach((action) => {
              context.dispatch(action.name, action.data, action.root);
            });

            context.commit("setNotification", e.notification, {
              root: true,
            });
          });

        context.state.verbose
          ? context.commit("setInitInfo", {
              action: "initChannels",
              status: "success",
              message: "OK",
            })
          : "";

        resolve(true);
      } catch (error) {
        if (context.state.verbose) {
          context.commit("setInitInfo", {
            action: "initChannels",
            status: "error",
            message: "FAILED",
          });

          reject(error);
        } else {
          reject();
        }
      }
    });
  },
  retrieveCashRegister(context) {
    context.state.verbose
      ? context.commit("setInitInfo", {
          action: "retrieveCashRegister",
          status: "info",
          message: "LOADING",
        })
      : "";

    return new Promise((resolve, reject) => {
      const payload = {
        method: "get",
        url: "cash-register-logs/retrieve",
      };

      context
        .dispatch("requests/request", payload, { root: true })
        .then((response) => {
          context.commit("setCashRegister", response.cash_register, {
            root: true,
          });
          context.commit("menu/setStoreName", response.store_name, {
            root: true,
          });
          context.commit("cart/setTaxPercentage", response.tax_percentage, {
            root: true,
          });

          resolve(response.data);
        })
        .catch((error) => {
          if (context.state.verbose) {
            context.commit("setInitInfo", {
              action: "retrieveCashRegister",
              status: "error",
              message: "FAILED",
            });

            reject(error);
          } else {
            reject();
          }
        });
    });
  },
};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
};
