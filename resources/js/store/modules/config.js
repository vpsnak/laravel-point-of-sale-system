const state = {
    verbose: process.env.NODE_ENV === "development" ? true : false,
    init_info: [],
    base_url: process.env.MIX_BASE_URL,
    app_env: process.env.NODE_ENV,
    app_name: process.env.MIX_APP_NAME,
    mas_env: "",
    menu_items: "",

    app_load: 0,

    echo: {}
};

const mutations = {
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
    }
};

const actions = {
    getMenuItems(context) {
        context.state.verbose
            ? context.commit("setInitInfo", {
                  action: "getMenuItems",
                  status: "info",
                  message: "LOADING"
              })
            : "";

        return new Promise((resolve, reject) => {
            axios
                .get(`${context.state.base_url}/menu-items`)
                .then(response => {
                    context.commit("menu/setTopMenu", response.data.top_menu, {
                        root: true
                    });

                    context.commit(
                        "menu/setSideMenu",
                        response.data.side_menu,
                        {
                            root: true
                        }
                    );

                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "getMenuItems",
                              status: "success",
                              message: "OK"
                          })
                        : "";

                    resolve(true);
                })
                .catch(error => {
                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "getMenuItems",
                              status: "error",
                              message: "FAILED"
                          })
                        : "";
                    reject(error.response);
                });
        });
    },
    getMasEnv(context) {
        context.state.verbose
            ? context.commit("setInitInfo", {
                  action: "getMasEnv",
                  status: "info",
                  message: "LOADING"
              })
            : "";

        return new Promise((resolve, reject) => {
            axios
                .get(`${context.state.base_url}/mas/env`)
                .then(response => {
                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "getMasEnv",
                              status: "success",
                              message: "OK"
                          })
                        : "";

                    context.commit("setMasEnv", response.data);

                    context.commit(
                        "setNotification",
                        {
                            msg: response.data.info,
                            type: "info"
                        },
                        { root: true }
                    );

                    resolve(true);
                })
                .catch(error => {
                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "getMasEnv",
                              status: "error",
                              message: "FAILED"
                          })
                        : "";
                    reject(error.response);
                });
        });
    },
    setMasEnv(context, payload) {
        return new Promise((resolve, reject) => {
            axios
                .get(`${context.state.base_url}/mas/set/${payload}`)
                .then(response => {
                    context.commit(
                        "setNotification",
                        {
                            msg: response.data.info,
                            type: "success"
                        },
                        { root: true }
                    );
                    context.commit("setMasEnv", response.data.payload);
                    resolve(true);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },
    initWebSockets(context) {
        context.state.verbose
            ? context.commit("setInitInfo", {
                  action: "initWebSockets",
                  status: "info",
                  message: "LOADING"
              })
            : "";

        return new Promise((resolve, reject) => {
            try {
                state.echo = require("laravel-echo");
                state.echo = new state.echo.default({
                    broadcaster: "pusher",
                    key: process.env.MIX_PUSHER_APP_KEY,
                    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                    encrypted: true,
                    auth: {
                        headers: {
                            Authorization: context.rootState.token
                        }
                    }
                });

                context.state.verbose
                    ? context.commit("setInitInfo", {
                          action: "initWebSockets",
                          status: "success",
                          message: "OK"
                      })
                    : "";

                resolve(true);
            } catch (error) {
                console.error(error);
                context.state.verbose
                    ? context.commit("setInitInfo", {
                          action: "initWebSockets",
                          status: "error",
                          message: "FAILED"
                      })
                    : "";

                reject(error);
            }
        });
    },
    initPrivateChannels(context) {
        context.state.verbose
            ? context.commit("setInitInfo", {
                  action: "initPrivateChannels",
                  status: "info",
                  message: "LOADING"
              })
            : "";
        return new Promise((resolve, reject) => {
            try {
                state.echo
                    .private(`user.${context.rootState.user.id}`)
                    .listen("CashRegisterLogin", e => {
                        if (_.has(e, "mutations")) {
                            e.mutations.forEach(mutation => {
                                context.commit(
                                    mutation.name,
                                    mutation.data,
                                    mutation.root
                                );
                            });
                        }

                        if (_.has(e, "notification")) {
                            context.commit("setNotification", e.notification, {
                                root: true
                            });
                        }
                    });

                context.state.verbose
                    ? context.commit("setInitInfo", {
                          action: "initPrivateChannels",
                          status: "success",
                          message: "OK"
                      })
                    : "";

                resolve(true);
            } catch (error) {
                context.state.verbose
                    ? context.commit("setInitInfo", {
                          action: "initPrivateChannels",
                          status: "error",
                          message: "FAILED"
                      })
                    : "";

                reject(error);
            }
        });
    },
    retrieveCashRegister(context) {
        context.state.verbose
            ? context.commit("setInitInfo", {
                  action: "retrieveCashRegister",
                  status: "info",
                  message: "LOADING"
              })
            : "";

        return new Promise((resolve, reject) => {
            axios
                .get(`${context.state.base_url}/cash-register-logs/retrieve`)
                .then(response => {
                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "retrieveCashRegister",
                              status: "success",
                              message: "OK"
                          })
                        : "";

                    if (_.has(response, "data.cashRegister")) {
                        context.commit(
                            "setNotification",
                            {
                                msg: response.data.info,
                                type: "info"
                            },
                            { root: true }
                        );

                        context.commit(
                            "setCashRegister",
                            response.data.cashRegister.cash_register,
                            { root: true }
                        );
                        context.commit(
                            "setStore",
                            response.data.cashRegister.cash_register.store,
                            { root: true }
                        );
                    }
                    resolve(true);
                })
                .catch(error => {
                    context.state.verbose
                        ? context.commit("setInitInfo", {
                              action: "retrieveCashRegister",
                              status: "error",
                              message: "FAILED"
                          })
                        : "";

                    reject(error);
                });
        });
    }
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
};
