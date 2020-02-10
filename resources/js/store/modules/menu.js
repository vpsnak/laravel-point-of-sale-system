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
        console.log("alva");
        state.side_menu = value;
    },
    setTopMenu(state, value) {
        console.log("nos");
        state.top_menu = value;
    }
};

export default {
    namespaced: true,
    state,
    mutations
};
