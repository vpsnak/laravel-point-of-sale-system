const state = {
    visibility: true,

    store_name: "",

    top_menu: [],
    side_menu: []
};

const mutations = {
    setStoreName(state, value) {
        state.store_name = value;
    },
    setVisibility(state, value) {
        state.visibility = value;
    },
    setSideMenu(state, value) {
        state.side_menu = value;
    },
    setTopMenu(state, value) {
        state.top_menu = value;
    }
};

export default {
    namespaced: true,
    state,
    mutations
};
