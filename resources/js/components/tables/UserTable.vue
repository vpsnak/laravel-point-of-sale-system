<template>
	<prop-data-table
		:tableHeaders="headers"
		data-url="users"
		tableTitle="Users"
		tableBtnTitle="New User"
		tableForm="userForm"
		:tableBtnDisable="false"
	>
		<template v-slot:item.email="{item}">
			<a :href="'mailto:' + item.email">{{ item.email }}</a>
		</template>
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
					text: "email",
					value: "email"
				},
				{
					text: "email_verified_at",
					value: "email_verified_at"
				},
				{
					text: "created_at",
					value: "created_at"
				},
				{
					text: "updated_at",
					value: "updated_at"
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
