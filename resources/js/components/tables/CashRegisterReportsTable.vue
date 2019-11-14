<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="cash-register-reports"
		tableTitle="Cash Register Reports"
		tableBtnTitle="New Cash Register"
		tableForm="cashRegisterForm"
		tableViewComponent="cashRegisterReports"
		:tableBtnDisable="true"
	></prop-data-table>
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
					text: "Report name",
					value: "report_name"
				},
				{
					text: "Report type",
					value: "report_type"
				},
				{
					text: "Cash register ID",
					value: "cash_register_id"
				},
				{
					text: "Opening amount",
					value: "opening_amount"
				},
				{
					text: "Closing amount",
					value: "closing_amount"
				},
				{
					text: "Subtotal",
					value: "subtotal"
				},
				{
					text: "Tax",
					value: "tax"
				},
				{
					text: "Total",
					value: "total"
				},
				{ text: "Actions", value: "action", sortable: false }
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
