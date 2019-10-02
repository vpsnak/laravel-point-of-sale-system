<template>
	<div>
		<v-row>
			<v-col cols="12">
				<h3 class="mb-3">Payment history</h3>
				<v-data-table
					:headers="headers"
					:items="paymentHistory"
					class="elevation-1"
					disable-pagination
					disable-filtering
					hide-default-footer
					:loading="historyLoading"
				></v-data-table>
			</v-col>
		</v-row>
		<v-divider></v-divider>
		<v-row>
			<v-col cols="12">
				<h3>Order payment</h3>
			</v-col>
			<v-col>
				<v-toolbar prominent class="d-flex justify-space-between flex-column pt-5">
					<v-toolbar-title class="title">Remaining: ${{ remaining }}</v-toolbar-title>
					<v-btn-toggle v-model="paymentType" mandatory group dense>
						<v-btn
							v-for="(paymentType, index) in paymentTypes"
							:key="index"
							:value="paymentType.type"
						>{{ paymentType.name }}</v-btn>
					</v-btn-toggle>

					<!-- <div class="flex-grow-1"></div> -->
					<v-spacer></v-spacer>

					<v-text-field
						label="Amount"
						type="number"
						prepend-inner-icon="mdi-currency-usd"
						v-model="paymentAmount"
						style="max-width:300px"
					></v-text-field>

					<!-- <div class="flex-grow-1"></div> -->
					<v-spacer></v-spacer>
					<v-btn @click="sendPayment" :loading="paymentLoading" :disabled="paymentLoading">Send payment</v-btn>
					<v-spacer></v-spacer>
				</v-toolbar>
			</v-col>
		</v-row>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		order_id: Number
	},
	data() {
		return {
			historyLoading: false,
			paymentLoading: false,
			order: {},

			orderHistory: [],
			paymentTypes: [],
			payment: {
				type: null,
				amount: null
			},
			headers: [
				{
					text: "Operator",
					value: "created_by.name"
				},
				{
					text: "Date",
					value: "created_at"
				},
				{
					text: "Type",
					value: "payment_type.name"
				},
				{
					text: "Amount (USD)",
					value: "amount"
				}
			]
		};
	},

	computed: {
		remaining() {
			return this.order.total - this.order.total_paid;
		},
		paymentHistory: {
			get() {
				return this.orderHistory;
			},
			set(value) {
				this.orderHistory = value;
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
			this.getPaymentHistory();
			this.getPaymentTypes();
		},
		getOrder() {
			let payload = {
				model: "orders",
				data: { id: this.$props.order_id }
			};
			this.getOne(payload).then(response => {
				this.order = response;
				console.log("get order");
				console.log(response);
			});
		},
		sendPayment() {
			this.paymentLoading = true;

			let payload = {
				model: "payments",
				data: {
					payment_type: this.paymentType,
					amount: this.paymentAmount,
					order_id: this.$props.order_id,
					cash_register_id: 1
				}
			};

			this.create(payload)
				.then(response => {
					console.log("send payment");

					console.log(response);
					this.init();
				})
				.finally(() => {
					this.paymentAmount = null;
					this.paymentLoading = false;
				});
		},

		getPaymentHistory() {
			this.historyLoading = true;

			let paymentHistory;
			let payload = {
				model: "payments",
				keyword: this.$props.order_id
			};
			this.search(payload)
				.then(response => {
					console.log("paymentHistory");

					console.log(response);
					this.paymentHistory = response;
				})
				.finally(() => {
					this.historyLoading = false;
				});
		},

		getPaymentTypes() {
			this.getAll({ model: "payment-types" }).then(response => {
				this.paymentTypes = response;
				console.log(response);
			});
		},

		...mapActions(["search", "create", "getAll", "getOne"])
	}
};
</script>