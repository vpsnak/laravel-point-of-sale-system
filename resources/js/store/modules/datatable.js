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
        console.log(value);
        state.data_table.rows = value.rows || [];
        state.data_table.icon = value.icon || "";
        state.data_table.title = value.title || "";
        state.data_table.headers = value.headers || [];
        state.data_table.model = value.model || "";
        state.data_table.btnTxt = value.btnTxt || "";
        state.data_table.newForm = value.newForm || "";
        state.data_table.disableNewBtn = value.disableNewBtn || false;
        state.data_table.loading = value.loading || false;
        console.log(state.data_table);
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
