<template>
	<data-table v-if="data_table.model">
		<template v-slot:item.actions="{ item }">
			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="data_table.loading"
						@click.stop="(item.form = form), editItem(item)"
						class="my-2"
						v-on="on"
						icon
					>
						<v-icon small>edit</v-icon>
					</v-btn>
				</template>
				<span>Edit</span>
			</v-tooltip>

			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="data_table.loading"
						@click.stop="(item.form = form), viewItem(item)"
						class="my-2"
						v-on="on"
						icon
					>
						<v-icon small>mdi-eye</v-icon>
					</v-btn>
				</template>
				<span>View</span>
			</v-tooltip>
		</template>
	</data-table>
</template>

<script>
import { mapMutations, mapState } from "vuex";

export default {
	mounted() {
		this.setDataTable({
			icon: "mdi-buffer",
			title: "Stores",
			headers: this.headers,
			model: "stores",
			component: this.form,
			newForm: this.form,
			btnTxt: "New Store",
			loading: true
		});
	},
	data() {
		return {
			form: "storeForm",
			headers: [
				{ text: "Id", value: "id" },
				{ text: "Name", value: "name" },
				{ text: "Phone", value: "phone" },
				{ text: "Street", value: "street" },
				{ text: "Postcode", value: "postcode" },
				{ text: "City", value: "city" },
				{ text: "Tax", value: "tax.name" },
				{ text: "Company", value: "company.name" },
				{ text: "Created by", value: "created_by" },
				{ text: "Actions", value: "actions" }
			]
		};
	},
	computed: {
		...mapState("datatable", ["data_table"])
	},
	methods: {
		...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
		...mapMutations("datatable", [
			"setLoading",
			"setDataTable",
			"resetDataTable"
		])
	}
};
</script>
