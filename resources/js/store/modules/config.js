const state = {
    app_env: process.env.NODE_ENV,
    app_name: process.env.MIX_APP_NAME,
    mas_env: "",

    app_load: 0
};

const mutations = {
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
        return new Promise((resolve, reject) => {
            axios
                .get(`${context.rootState.base_url}/mas/env`)
                .then(response => {
                    context.commit(
                        "setNotification",
                        {
                            msg: response.data.info,
                            type: "info"
                        },
                        { root: true }
                    );
                    context.commit("setMasEnv", response.data);
                    resolve(true);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },
    setMasEnv(context, payload) {
        return new Promise((resolve, reject) => {
            axios
                .get(`${context.rootState.base_url}/mas/set/${payload}`)
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
    }
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
};
