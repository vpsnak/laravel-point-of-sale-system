<template>
	<div>
		<v-row>
			<v-col cols="12">
				<h3>Payment history</h3>
				<v-data-table :headers="headers" :items="desserts" :items-per-page="5" class="elevation-1"></v-data-table>
			</v-col>
		</v-row>
		<v-divider></v-divider>
		<v-row>
			<v-col cols="6">
				<v-select
					prepend-icon="mdi-cash-multiple"
					label="Payment type"
					v-model="paymentType"
					:items="paymentTypes"
					item-text="name"
					item-value="type"
				></v-select>
			</v-col>
			<v-col cols="6">
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
export default {
	props: {},
	data() {
		return {
			paymentTypes: [],
			payment: {
				type: null,
				amount: null
			},
			headers: ["Store", "Cashier", "Operator", "Date", "Type", "Amount (USD)"],
			items: [
				{
					store: "store name / address",
					cashier: "cashier name/number",
					operator: "user: first_name + last_name",
					date: Date.now,
					type: "Cash",
					amount: 30
				},
				{
					store: "store name / address",
					cashier: "cashier name/number",
					operator: "user: first_name + last_name",
					date: Date.now,
					type: "Credit card",
					amount: 70
				}
			]
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
				console.log(response);
				this.paymentTypes = response;
			});
	}
};
</script>