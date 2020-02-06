const state = {
    app_env: process.env.NODE_ENV,
    app_name: process.env.MIX_APP_NAME,
    mas_production_mode: process.env.MIX_MAS_PRODUCTION_MODE,

    app_load: 0
};

const mutations = {
    resetLoad(state) {
        state.app_load = 0;
    },
    addLoadPercent(state, value) {
        state.app_load += value;
    }
};

export default {
    namespaced: true,
    state,
    mutations
};
