<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="gift-cards"
		tableTitle="Gift Cards"
		tableBtnTitle="New Gift Card"
		tableForm="giftCardForm"
		tableViewComponent="giftCard"
	>
		<template v-slot:item.enabled="{item}">{{(item.enabled? 'Yes' : 'No')}}</template>
	</prop-data-table>
</template>

<script>
import { mapMutations } from "vuex";

export default {
	data() {
		return {
			headers: [
				{
					text: "Id",
					value: "id"
				},
				{
					text: "Name",
					value: "name"
				},
				{
					text: "Code",
					value: "code"
				},
				{
					text: "Enabled",
					value: "enabled"
				},
				{
					text: "Amount",
					value: "amount"
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
