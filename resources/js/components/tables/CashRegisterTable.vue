<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="cash-registers"
		tableTitle="Cash Registers"
		tableBtnTitle="New Cash Register"
		tableForm="cashRegisterForm"
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
