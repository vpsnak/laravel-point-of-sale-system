const state = {
    visibility: true,

    top_menu: [],
    side_menu: []
};

const mutations = {
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
