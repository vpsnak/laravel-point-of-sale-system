const state = {
    verbose: process.env.NODE_ENV === "development" ? true : false,
    init_info: [],
    base_url: process.env.MIX_BASE_URL,
    app_env: process.env.NODE_ENV,
    app_name: process.env.MIX_APP_NAME,
    mas_env: "",

    app_load: 0
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
                              message: error.response.data
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
                Echo.private(`user.${context.rootState.user.id}`).listen(
                    "SendKickNotification",
                    e => {
                        console.log(e);
                    }
                );

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
                          message: error.response.data
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
