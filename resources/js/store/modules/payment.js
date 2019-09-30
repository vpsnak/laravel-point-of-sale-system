// initial state
const state = {
  total: 0,
  payment_types: [],
  payments: []
}

// getters
const getters = {
  paymentEntries: (state) => {
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
  removePayment({commit, state, dispatch}, payment) {
    let payload = {
      model: 'payments',
      id: payment.id
    }
    dispatch('delete', payload, {root: true})
      .then(() => {
        dispatch('fetchPayments')
      })
  },
  addPayment({commit, dispatch}, payment) {
    let payload = {
      model: 'payments',
      data: payment
    }
    dispatch('create', payload, {root: true})
      .then(() => {
        dispatch('fetchPayments')
      })
  },
  fetchPayments({dispatch}) {
    let payload = {
      model: 'payments',
      mutation: 'payment/setPayments',
      keyword: '1'
    }
    dispatch('search', payload, {root: true})
  },
  getPaymentTypes({dispatch}) {
    let payload = {
      model: 'payment-types',
      mutation: 'payment/setPaymentTypes'
    }
    dispatch('getAll', payload, {root: true})
  }
}

// mutations
const mutations = {
  addPayment(state, payment) {
    state.payments.push(payment)
  },
  removePayment(state, payment) {
    state.payments = state.payments.filter((paymentEntry) => { return paymentEntry !== payment })
  },
  setPayments(state, payments) {
    state.payments = payments
  },
  setPaymentTypes(state, payment_types) {
    state.payment_types = payment_types
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