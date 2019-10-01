// initial state
const state = {
	amount,
	payment_types: [],

	total: 0, // depricated
	payments: [] // depricated
}

// getters
const getters = {
	// depricated
	paymentEntries: (state) => {
		return state.payments
	},

	// depricated
	getTotalPaid: (state) => {
		return _.reduce(state.payments, (total, payment) => {
			return total + _.toNumber(payment.amount)
		}, 0)
	},

	// depricated
	getTotal: (state) => {
		return state.total
	}
}

// mutations
const mutations = {
	setPaymentTypes(state, payment_types) {
		state.payment_types = payment_types
	},

	// depricated
	addPayment(state, payment) {
		state.payments.push(payment)
	},

	// depricated
	removePayment(state, payment) {
		state.payments = state.payments.filter((paymentEntry) => { return paymentEntry !== payment })
	},

	// depricated
	setPayments(state, payments) {
		state.payments = payments
	},

	// depricated
	setTotal(state, total) {
		state.total = total
	}
},

// actions
const actions = {
	// depricated
	removePayment({ commit, state, dispatch }, payment) {
		let payload = {
			model: 'payments',
			id: payment.id
		}
		dispatch('delete', payload, { root: true })
			.then(() => {
				dispatch('fetchPayments')
			})
	},

	// depricated
	addPayment({ commit, dispatch }, payment) {
		let payload = {
			model: 'payments',
			data: payment
		}
		dispatch('create', payload, { root: true })
			.then(() => {
				dispatch('fetchPayments')
			})
	},

	// depricated
	fetchPayments({ dispatch }) {
		let payload = {
			model: 'payments',
			mutation: 'payment/setPayments',
			keyword: '1'
		}
		dispatch('search', payload, { root: true })
	},

	// depricated
	getPaymentTypes({ dispatch }) {
		let payload = {
			model: 'payment-types',
			mutation: 'payment/setPaymentTypes'
		}
		dispatch('getAll', payload, { root: true })
	}
}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}