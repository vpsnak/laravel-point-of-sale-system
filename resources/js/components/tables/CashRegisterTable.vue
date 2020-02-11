<template>
	<data-table v-if="data_table.model">
		<template v-slot:item.is_open="{ item }">
			{{
			item.is_open ? "Yes" : "No"
			}}
		</template>
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
import { mapState, mapMutations } from "vuex";

export default {
	mounted() {
		this.setDataTable({
			icon: "mdi-cash-register",
			title: "Cash Registers",
			headers: this.headers,
			model: "cash-registers",
			component: this.form,
			newForm: this.form,
			btnTxt: "New Cash Register",
			loading: true
		});
	},
	data() {
		return {
			form: "cashRegisterForm",
			headers: [
				{ text: "#", value: "id" },
				{ text: "Name", value: "name" },
				{ text: "Store", value: "store.name" },
				{ text: "Barcode", value: "barcode" },
				{ text: "Is open", value: "is_open" },
				{ text: "Operator", value: "" },
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
