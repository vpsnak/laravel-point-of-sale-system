<template>
	<div>
		<paymentHistory
			:paymentHistory="paymentHistory"
			:loading="paymentHistoryLoading"
			v-if="history"
			@refund="refund"
		></paymentHistory>

		<v-divider></v-divider>

		<paymentActions
			:order_id="order_id"
			:types="paymentTypes"
			:remaining="remaining"
			:loading="paymentActionsLoading"
			title="Order payment"
			paymentBtnTxt="Send payment"
			v-if="actions"
			@sendPayment="sendPayment"
		></paymentActions>
	</div>
</template>

<script>
import { mapActions, mapMutations } from "vuex";

export default {
	props: {
		order_id: Number,
		history: Boolean,
		actions: Boolean
	},
	data() {
		return {
			order: {},
			order_remaining: undefined,
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
		remaining: {
			get() {
				return this.order_remaining;
			},
			set(value) {
				this.order_remaining = value;
			}
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

				this.remaining = this.order.total - this.order.total_paid;
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
					amount: event.paymentAmount ? event.paymentAmount : null,
					order_id: this.$props.order_id,
					cash_register_id: 1
				}
			};

			switch (event.paymentType) {
				default:
				case "cash":
					break;
				case "card":
					payload.data["card"] = event.card;
					break;
				case "coupon":
				case "giftcard":
					payload.data["code"] = event.code;
					break;
			}

			this.create(payload)
				.then(response => {
					this.order_remaining = response.total - response.total_paid;

					let notification = {
						msg: "Payment received",
						type: "success"
					};
					this.setNotification(notification);

					this.init();
				})
				.finally(() => {
					this.paymentAmount = null;
					this.paymentActionsLoading = false;
				});
		},
		refund(event) {
			let notification = {
				msg: event.msg,
				type: event.status
			};
			this.setNotification(notification);

			this.init();
		},

		...mapActions(["search", "create", "getAll", "getOne"]),
		...mapMutations(["setNotification"])
	}
};
</script>
