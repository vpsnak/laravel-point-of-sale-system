// initial state
const state = {
    title: "DataTable",
    btnTitle: "New Item",
    headers: [],
    rows: [],
    loading: false,
    form: "Form",
    btnDisable: false,
};

// getters
const getters = {};

// actions
const actions = {
    getRows({ dispatch, commit }, payload) {
        commit("setLoading", true);
        dispatch("getRequest", payload, { root: true })
            .then(result => {
                commit("setLoading", false);
                commit("setRows", result);
            })
            .catch(error => {
                commit("setLoading", false);
                console.log(error);
            });
    },
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
                console.log(error);
            });
    }
};

// mutations
const mutations = {
    setHeaders(state, headers) {
        state.headers = headers;
    },
    setLoading(state, loading) {
        state.loading = loading;
    },
    setTitle(state, title) {
        state.title = title;
    },
    setBtnTitle(state, btnTitle) {
        state.btnTitle = btnTitle;
    },
    setForm(state, form) {
        state.form = form;
    },
    setBtnDisable(state, btnDisable) {
        state.btnDisable = btnDisable;
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
