// initial state
const state = {
    title: 'DataTable',
    headers: [],
    data: [],
    loading: false,
}

// getters
const getters = {}

// actions
const actions = {
    getData ({dispatch, commit}) {
        commit('setLoading', true)
        let payload = {
            model: 'customers',
            // mutation: 'setUserList'
        }
        dispatch('getAll', payload)
            .then(result => {
                commit('setLoading', false)
                commit(result.data)
            })
            .catch(error => {
                commit('setLoading', false)
                console.log(error)
            })
    },
    initiateLoadingSearchResults (loading) {
        if (loading) {
            this.loader = true
        } else {
            this.loader = false
        }
    },
}

// mutations
const mutations = {
    setHeaders (state, headers) {
        state.headers = headers
    },
    setLoading (state, loading) {
        state.loading = loading
    },
    setData (state, data) {
        state.data = data
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
