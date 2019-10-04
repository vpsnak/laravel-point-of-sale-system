<template>
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
				:loading="loading"
			></v-data-table>
		</v-col>
	</v-row>
</template>

<script>
export default {
	props: {
		paymentHistory: Array,
		loading: Boolean
	},
	data() {
		return {
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
	}
};
</script>