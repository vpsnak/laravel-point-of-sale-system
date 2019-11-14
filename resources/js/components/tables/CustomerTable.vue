<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="customers"
		tableTitle="Customers"
		tableBtnTitle="New Customer"
		tableForm="customerNewForm"
		:tableBtnDisable="false"
		tableViewComponent="customer"
	>
		<template v-slot:item.email="{item}">
			<a :href="'mailto:' + item.email">{{ item.email }}</a>
		</template>
		<template v-slot:item.phone="{item}">
			<a :href="'tel:' + item.phone">{{ item.phone }}</a>
		</template>
		<template v-slot:item.no_tax="{item}">{{(item.no_tax? 'Yes' : 'No')}}</template>
		<template v-slot:item.house_account_status="{item}">{{(item.house_account_status? 'Yes' : 'No')}}</template>
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
					text: "First name",
					value: "first_name"
				},
				{
					text: "Last name",
					value: "last_name"
				},
				{
					text: "E-mail",
					value: "email",
					sortable: false
				},
				{
					text: "No tax",
					value: "no_tax",
					sortable: false
				},
				{
					text: "House Account",
					value: "house_account_status",
					sortable: false
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
