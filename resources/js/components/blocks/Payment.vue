<template>
	<div>
		<v-row>
			<v-col cols="12">
				<h3 class="mb-3">Payment history</h3>
				<v-data-table
					:headers="headers"
					:items="paymentHistory()"
					class="elevation-1"
					disable-pagination
					disable-filtering
					hide-default-footer
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
					<v-btn v-for="(paymentType, index) in paymentTypes" :key="index">{{ paymentType.name }}</v-btn>
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
				<v-btn block>Send payment</v-btn>
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
			paymentTypes: [],
			payment: {
				type: null,
				amount: null
			},
			headers: [
				{
					text: "Store",
					value: "store"
				},
				{
					text: "Cashier",
					value: "cashier"
				},
				{
					text: "Operator",
					value: "operator"
				},
				{
					text: "Date",
					value: "created_at"
				},
				{
					text: "Type",
					value: "type"
				},
				{
					text: "Amount (USD)",
					value: "amount"
				}
			]
			// paymentHistory: [
			// 	{
			// 		store: "store name / address",
			// 		cashier: "cashier name/number",
			// 		operator: "user: first_name + last_name",
			// 		date: "12/31/2020 14:30 PM",
			// 		type: "Cash",
			// 		amount: 30
			// 	},
			// 	{
			// 		store: "store name / address",
			// 		cashier: "cashier name/number",
			// 		operator: "user: first_name + last_name",
			// 		date: "12/31/2020 14:30 PM",
			// 		type: "Credit card",
			// 		amount: 70
			// 	}
			// ]
		};
	},

	computed: {
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
		this.$store
			.dispatch("getAll", { model: "payment-types" })
			.then(response => {
				this.paymentTypes = response;
			});

		this.paymentHistory();
	},

	methods: {
		paymentHistory() {
			let payload = {
				model: "payments",
				keyword: "1"
			};

			this.search(payload).then(response => {
				console.log(response);
				return response;
			});
		}
	},

	...mapActions(["search"])
};
</script>