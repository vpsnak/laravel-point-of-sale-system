<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="cash-register-reports"
		tableTitle="Cash Register Reports"
		tableBtnTitle="New Cash Register"
		tableForm="cashRegisterForm"
		tableViewComponent="cashRegisterReports"
	/>
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
