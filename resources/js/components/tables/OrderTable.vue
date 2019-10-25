<template>
	<div>
		<v-container>
			<v-row>
				<v-col cols="12">
					<prop-data-table
						:tableHeaders="headers"
						data-url="orders"
						tableTitle="Orders"
						tableBtnTitle="New Order"
						:tableBtnDisable="true"
						tableViewComponent="order"
					>
						<template
							v-slot:item.customer_id="{item}"
						>{{ item.customer_id ? item.customer_id.email : 'Guest' }}</template>
						<template v-slot:item.store_id="{item}">{{ item.store_id.name }}</template>
						<template v-slot:item.created_by="{item}">{{ item.created_by.email }}</template>
					</prop-data-table>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>
import { mapMutations } from "vuex";

export default {
	data() {
		return {
			headers: [
				{
					text: "ID",
					value: "id"
				},
				{
					text: "Customer",
					value: "customer_id"
				},
				{
					text: "Store",
					value: "store_id"
				},
				{
					text: "Status",
					value: "status"
				},
				{
					text: "Created by",
					value: "created_by"
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
					text: "tax",
					value: "tax"
				},
				{
					text: "Subtotal",
					value: "subtotal"
				},
				{
					text: "Created at",
					value: "created_at"
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
			setRows: "setRows",
			deleteRow: "deleteRow"
		})
	}
};
</script>
