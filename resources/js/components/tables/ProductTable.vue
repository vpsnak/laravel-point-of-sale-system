<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="products"
		tableTitle="Products"
		tableBtnTitle="New Product"
		tableForm="productForm"
		:tableBtnDisable="cashierDisabled()"
		tableViewComponent="product"
	/>
</template>
<script>
import { mapMutations } from "vuex";

export default {
	data() {
		return {
			headers: [
				{ text: "Id", value: "id" },
				{ text: "Name", value: "name" },
				{ text: "Sku", value: "sku" },
				// { text: "Photo url", value: "photo_url" },
				// { text: "Price", value: "price.amount" },
				{ text: "Price", value: "final_price" },
				{ text: "Stock", value: "stock" },
				{ text: "Actions", value: "action", sortable: false }
			]
		};
	},
	mounted() {
		this.setRows([]);
	},
	computed: {
		role() {
			return this.$store.getters.role;
		}
	},
	methods: {
		cashierDisabled() {
			if (this.role == "admin" || this.role == "store_manager") {
				return false;
			} else {
				return true;
			}
		},
		...mapMutations("datatable", {
			setRows: "setRows",
			deleteRow: "deleteRow"
		})
	}
};
</script>
