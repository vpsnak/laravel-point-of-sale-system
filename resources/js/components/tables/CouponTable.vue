<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="coupons"
		tableTitle="Coupons"
		tableBtnTitle="New Coupon"
		tableForm="couponForm"
		tableViewComponent="coupon"
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
					text: "Code",
					value: "code"
				},
				{
					text: "Uses",
					value: "uses"
				},
				{
					text: "Discount ID",
					value: "discount_id"
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
