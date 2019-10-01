<template>
	<div>
		<v-list-item
			:key="payment.id"
			v-for="payment in payments"
		>
			<v-list-item-content>
				<v-list-item-title @click="handlePaymentRemove(payment)">{{ payment.payment_type.name + ' - ' + payment.amount }}$</v-list-item-title>
			</v-list-item-content>
		</v-list-item>
		<v-row
			class="d-flex justify-space-between pa-2"
			no-gutters
		>
			<span>Total Paid</span>
			<span>$ {{totalPaid}}</span>
		</v-row>
		<v-divider />
		<v-row
			class="d-flex justify-space-between pa-2"
			no-gutters
		>
			<span>Total</span>
			<span>$ {{ total }}</span>
		</v-row>
		<v-divider />
		<v-row
			class="d-flex justify-space-between pa-2"
			no-gutters
		>
			<span>Remains</span>
			<span>$ {{ remainingAmount }}</span>
		</v-row>
		<v-divider />
		<v-row no-gutters>
			<v-text-field
				label="Amount"
				prefix="$"
				type="number"
				v-model="payment_amount"
			/>
		</v-row>
		<v-row no-gutters>
			<v-btn
				:disabled="remainingAmount <= 0"
				:type="payment_type.type"
				@click="handlePaymentButton(payment_type)"
				v-for="(payment_type, index) in payment_types"
				:key="index"
			>{{payment_type.name}}
			</v-btn>
		</v-row>
	</div>
</template>
<script>
	import { mapActions, mapGetters, mapMutations, mapState } from "vuex";

	export default {
		data() {
			return {
				payment_amount: this.payAmount | this.remainingAmount
			};
		},
		props: {
			totalAmount: Number,
			payAmount: Number
		},
		mounted() {
			this.getPaymentTypes();
			this.fetchPayments();
			this.setTotal(this.totalAmount);
		},
		computed: {
			remainingAmount: {
				get() {
					return this.total - this.totalPaid;
				},
				set(value) {
					if (value > this.remainingAmount) {
						this.payment_amount = this.remainingAmount;
					} else {
						this.payment_amount = value;
					}
				}
			},
			...mapState("payment", {
				payment_types: "payment_types",
				payments: "payments",
				total: "total"
			}),
			...mapGetters("payment", {
				totalPaid: "getTotalPaid"
			})
		},
		watch: {
			payment_amount(value) {
				if (value > this.remainingAmount) {
					this.payment_amount = this.remainingAmount;
				} else {
					this.payment_amount = value;
				}
			}
		},
		methods: {
			handlePaymentAmountInput(value) {
				this.payment_amount = value;
			},
			handlePaymentButton(payment_type) {
				this.addPayment({
					payment_type: payment_type.type,
					amount: this.payment_amount,
					cash_register_id: 1,
					order_id: 1,
					created_by: 1
				});
				this.payment_amount = this.remainingAmount;
			},
			handlePaymentRemove(payment) {
				this.removePayment(payment);
				this.payment_amount = this.remainingAmount;
			},
			...mapActions("payment", {
				getPaymentTypes: "getPaymentTypes",
				fetchPayments: "fetchPayments",
				removePayment: "removePayment",
				addPayment: "addPayment"
			}),
			...mapMutations("payment", {
				setTotal: "setTotal",
				setPayments: "setPayments"
			})
		}
	};
</script>