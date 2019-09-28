<template>
	<prop-data-table :tableHeaders="headers" data-url="taxes" tableTitle="Taxes" />
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
						text: "Percentage",
						value: "percentage"
					},
					{
						text: "Default",
						value: "is_default"
					},
					{
						text: "Created at",
						value: "created_at"
					},
					{
						text: "Updated at",
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
				setRows: "setRows"
			})
		}
	};
</script>
