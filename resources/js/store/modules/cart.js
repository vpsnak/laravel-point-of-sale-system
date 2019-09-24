export default {
    namespaced: true,
    state: {
        retail: false
    },

    getters: {},

    mutations: {
        toggleRetail(state) {
            state.retail = !state.retail;
        }
    },

    actions: {}
};
