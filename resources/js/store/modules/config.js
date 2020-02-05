const state = {
    app_env: process.env.NODE_ENV,
    app_name: process.env.VUE_APP_NAME || "Plantshed Sales Management",

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
