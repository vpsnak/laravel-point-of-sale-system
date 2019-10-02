<template>
	<div>
		<paymentHistory :paymentHistory="paymentHistory" :loading="paymentHistoryLoading" v-if="history"></paymentHistory>

		<v-divider></v-divider>

		<paymentActions
			:order_id="order_id"
			:types="paymentTypes"
			:remaining="remaining"
			:loading="paymentActionsLoading"
			v-if="actions"
			@sendPayment="sendPayment"
		></paymentActions>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		order_id: Number,
		history: Boolean,
		actions: Boolean
	},
	data() {
		return {
			order: {},
			paymentHistory: [],
			orderHistory: [],
			paymentTypes: [],

			paymentHistoryLoading: false,
			paymentActionsLoading: false,

			payment: {
				type: null,
				amount: null
			}
		};
	},

	computed: {
		remaining() {
			return this.order.total - this.order.total_paid;
		},
		paymentType: {
			get() {
				return this.payment.type;
			},
			set(value) {
				this.payment.type = value;
			}
		},
		paymentAmount: {
			get() {
				return this.payment.amount;
			},
			set(value) {
				this.payment.amount = value;
			}
		}
	},

	mounted() {
		this.init();
	},

	methods: {
		init() {
			this.getOrder();

			if (this.$props.history) {
				this.getPaymentHistory();
			}

			if (this.$props.actions) {
				this.getPaymentTypes();
			}
		},
		getOrder() {
			let payload = {
				model: "orders",
				data: { id: this.$props.order_id }
			};
			this.getOne(payload).then(response => {
				this.order = response;
			});
		},
		getPaymentHistory() {
			this.paymentHistoryLoading = true;

			let payload = {
				model: "payments",
				keyword: this.$props.order_id
			};
			this.search(payload)
				.then(response => {
					this.paymentHistory = response;
				})
				.finally(() => {
					this.paymentHistoryLoading = false;
				});
		},

		getPaymentTypes() {
			this.getAll({ model: "payment-types" }).then(response => {
				this.paymentTypes = response;
			});
		},

		sendPayment(event) {
			this.paymentActionsLoading = true;

			let payload = {
				model: "payments",
				data: {
					payment_type: event.paymentType,
					amount: event.paymentAmount,
					order_id: this.$props.order_id,
					cash_register_id: 1
				}
			};

			this.create(payload)
				.then(response => {
					this.init();
				})
				.finally(() => {
					this.paymentAmount = null;
					this.paymentActionsLoading = false;
				});
		},

		...mapActions(["search", "create", "getAll", "getOne"])
	}
};
</script>