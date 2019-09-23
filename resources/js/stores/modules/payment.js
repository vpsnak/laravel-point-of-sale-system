// initial state
const state = {
    type: 'cash',
    amount: 10,
    total: 100,
    payments: [
        { id: 1, name: 'Cash', amount: 10 },
        { id: 2, name: 'Cash', amount: 11 },
        { id: 3, name: 'Cash', amount: 12 },
        { id: 4, name: 'Cash', amount: 13 },
        { id: 5, name: 'Cash', amount: 14 },
        { id: 6, name: 'Cash', amount: 15 },
    ]
}

// getters
const getters = {
    paymentEntries: (state, getters) => { 
        console.log('removePayment getter');
        return state.payments
    },
    getTotalPaid: (state, getters) => {
        return _.reduce(state.payments, (total, payment) => {
            return total + payment.amount
         }, 0 )
    },
    getTotal: (state, getters) => {
        return state.total
    }
}

// actions
const actions = {
    removePayment({ commit, state }, payment) {
        console.log('removePayment action');
        commit('removePayment', payment)
    }
}

// mutations
const mutations = {
    addPayment(state, payment) {
        state.payments.push(payment)
    },
    removePayment(state, payment) {
        console.log('removePayment mutation');
        state.payments = state.payments.filter((value, index) => { return value != payment })
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}