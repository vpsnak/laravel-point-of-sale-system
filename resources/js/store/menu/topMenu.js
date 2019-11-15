export default {
    namespaced: true,
    state: {
        mini: false
    },

    getters: {},

    mutations: {
        toggleMini(state) {
            state.mini = !state.mini;
        }
    },

    actions: {}
};
