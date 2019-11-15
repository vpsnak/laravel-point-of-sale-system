<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="addresses"
		tableTitle="Addresses"
		tableBtnTitle="New Address"
		tableForm="addressForm"
		tableViewComponent="Address"
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
					text: "First Name",
					value: "first_name"
				},
				{
					text: "Last Name",
					value: "last_name"
				},
				{
					text: "Street",
					value: "street"
				},
				{
					text: "Street 2",
					value: "street2"
				},
				{
					text: "City",
					value: "city"
				},
				{
					text: "Country ID",
					value: "country_id"
				},
				{
					text: "Region",
					value: "address_region.default_name"
				},
				{
					text: "Postcode",
					value: "postcode"
				},
				{
					text: "Phone",
					value: "phone"
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
