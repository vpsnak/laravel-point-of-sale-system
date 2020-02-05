<template>
	<data-table
		icon="mdi-file-document-box"
		title="Cash Register Reports"
		:headers="headers"
		data-url="cash-register-reports"
		tableBtnTitle="New Cash Register"
		:tableBtnDisable="true"
	></data-table>
</template>

<script>
import { mapMutations } from "vuex";

export default {
	data() {
		return {
			headers: [
				{ text: "#", value: "id" },
				{ text: "Report name", value: "report_name" },
				{ text: "Report type", value: "report_type" },
				{ text: "Cash register ID", value: "cash_register_id" },
				{ text: "Opening amount", value: "opening_amount" },
				{ text: "Closing amount", value: "closing_amount" },
				{ text: "Subtotal", value: "subtotal" },
				{ text: "Tax", value: "tax" },
				{ text: "Total", value: "total" },
				{ text: "Actions", value: "actions" }
			]
		};
	}
};
</script>
