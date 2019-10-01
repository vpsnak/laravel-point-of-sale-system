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
				<h3 class="mb-3">Order payment</h3>
			</v-col>
			<v-col cols="8">
				<h4 class="mb-3">Payment Type</h4>
				<v-btn-toggle v-model="paymentType" mandatory>
					<v-btn
						v-for="(paymentType, index) in paymentTypes"
						:key="index"
						:value="paymentType.type"
					>{{ paymentType.name }}</v-btn>
				</v-btn-toggle>
			</v-col>
			<v-col cols="4">
				<v-text-field
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="paymentAmount"
				></v-text-field>
			</v-col>
		</v-row>
		<v-row>
			<v-col cols="12">
				<v-btn
					block
					@click="sendPayment"
					:loading="paymentLoading"
					:disabled="paymentLoading"
				>Send payment</v-btn>
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
		this.getPaymentHistory();
		this.getPaymentTypes();
	},

	methods: {
		sendPayment() {
			this.paymentLoading = true;

			let payload = {
				model: "payments",
				data: {
					payment_type: this.paymentType,
					amount: this.paymentAmount,
					order_id: 1,
					cash_register_id: 1
				}
			};

			this.create(payload)
				.then(response => {
					this.getPaymentHistory();
				})
				.finally(() => {
					this.paymentLoading = false;
				});
		},

		getPaymentHistory() {
			this.historyLoading = true;

			let paymentHistory;
			let payload = {
				model: "payments",
				keyword: "1"
			};
			this.search(payload)
				.then(response => {
					this.paymentHistory = response;
				})
				.finally(() => {
					this.historyLoading = false;
				});
		},

		getPaymentTypes() {
			this.$store
				.dispatch("getAll", { model: "payment-types" })
				.then(response => {
					this.paymentTypes = response;
					console.log(response);
				});
		},

		...mapActions(["search", "create"])
	}
};
</script>