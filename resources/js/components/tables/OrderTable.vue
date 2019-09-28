<template>
	<prop-data-table :tableHeaders="headers" data-url="taxes" tableTitle="Taxes" />
</template>

<script>
	import { mapMutations } from "vuex";

	export default {
		data() {
			return {
				headers: [
					{
						text: "Customer id",
						value: "customer_id"
					},
					{
						text: "Store id",
						value: "store_id"
					},
					{
						text: "Created_by",
						value: "created_by"
					},
					{
						text: "Items",
						value: "items"
					},
					{
						text: "Discount type",
						value: "discount_type"
					},
					{
						text: "Discount amount",
						value: "discount_amount"
					},
					{
						text: "Shipping type",
						value: "shipping_type"
					},
					{
						text: "Shipping cost",
						value: "shipping_cost"
					},
					{
						text: "tax",
						value: "tax"
					},
					{
						text: "Subtotal",
						value: "subtotal"
					},
					{
						text: "Note",
						value: "note"
					},
					{
						text: "Actions",
						value: "action",
						sortable: false
					}
				]
			};
		},
		mounted() {
			this.setRows([]);
		},
		methods: {
			...mapMutations("datatable", {
				setRows: "setRows"
			})
		}
	};
</script>
