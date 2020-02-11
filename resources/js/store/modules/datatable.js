// initial state
const state = {
    data_table: {
        rows: [],
        icon: "",
        title: "",
        headers: [],
        model: "",
        btnTxt: "",
        newForm: "",
        disableNewBtn: false,
        loading: false
    }
};

// mutations
const mutations = {
    setDataTable(state, value) {
        state.data_table = { ...state.data_table, ...value };
    },
    setRows(state, value) {
        state.data_table.rows = value;
    },
    deleteRow(state, value) {
        state.data_table.rows = _.filter(state.data_table.rows, row => {
            return row.id !== value;
        });
    },
    setLoading(state, value) {
        state.data_table.loading = value;
    }
};

const actions = {
    deleteRow(context, payload) {
        context.commit("setLoading", true);
        context
            .dispatch("deleteRequest", payload, { root: true })
            .then(result => {
                context.commit("setLoading", false);
                if (result === 1) {
                    context.commit("deleteRow", payload.data.id);
                }
            })
            .catch(error => {
                context.commit("setLoading", false);
            });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions
};
