// initial state
const state = {
  total: 0,
  payments: [
    {id: 1, type: 'Cash', amount: 10},
    {id: 2, type: 'Cash', amount: 11},
    {id: 3, type: 'Cash', amount: 12},
    {id: 4, type: 'Cash', amount: 13},
    {id: 5, type: 'Cash', amount: 14},
    {id: 6, type: 'Cash', amount: 15}
  ]
}

// getters
const getters = {
  paymentEntries: (state) => {
    console.log('removePayment getter')
    return state.payments
  },
  getTotalPaid: (state) => {
    return _.reduce(state.payments, (total, payment) => {
      return total + _.toNumber(payment.amount)
    }, 0)
  },
  getTotal: (state) => {
    return state.total
  }
}

// actions
const actions = {
  removePayment({commit, state}, payment) {
    console.log('removePayment action')
    commit('removePayment', payment)
  },
  addPayment({commit}, payment) {
    commit('addPayment', payment)
  },
  fetchPayments({dispatch}) {
    let payload = {
      model: 'payments',
      mutation: 'payment/setPayments'
    }
    dispatch('endpoints/getAll', payload, {root: true})
  }
}

// mutations
const mutations = {
  addPayment(state, payment) {
    state.payments.push(payment)
  },
  removePayment(state, payment) {
    console.log('removePayment mutation')
    state.payments = state.payments.filter((paymentEntry) => { return paymentEntry !== payment })
  },
  setPayments(state, payments) {
    console.log('removePayment mutation')
    state.payments = payments
  },
  setTotal(state, total) {
    state.total = total
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}