// initial state
const state = {};

// getters
const getters = {};

// actions
const actions = {
    getAll(context, payload) {
        return new Promise((resolve, reject) => {
            axios
                .get(this.state.baseUrl + payload.model)
                .then(response => {
                    if (_.has(payload, "mutation")) {
                        context.commit(payload.mutation, response.data, {
                            root: true
                        });
                    }
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
};

// mutations
const mutations = {};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};
