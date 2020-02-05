// initial state
const state = {
    title: "",
    btnTitle: "",
    rows: [],
    loading: false
};

// getters
const getters = {};

// actions
const actions = {
    deleteRow({ dispatch, commit }, payload) {
        commit("setLoading", true);
        dispatch("deleteRequest", payload, { root: true })
            .then(result => {
                commit("setLoading", false);
                if (result === 1) {
                    commit("deleteRow", payload.data.id);
                }
            })
            .catch(error => {
                commit("setLoading", false);
            });
    }
};

// mutations
const mutations = {
    setLoading(state, loading) {
        state.loading = loading;
    },
    setRows(state, rows) {
        state.rows = rows;
    },
    deleteRow(state, id) {
        state.rows = _.filter(state.rows, row => {
            return row.id !== id;
        });
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
