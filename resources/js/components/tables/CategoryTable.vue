<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="categories"
		tableTitle="Categories"
		tableBtnTitle="New Category"
		tableForm="categoryForm"
		tableViewComponent="category"
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
				{
					text: "In product listing",
					value: "in_product_listing"
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
