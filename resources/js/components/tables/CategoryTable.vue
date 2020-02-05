<template>
	<data-table
		icon="mdi-inbox-multiple"
		title="Categories"
		:headers="headers"
		data-url="categories"
		btnTxt="New Category"
		:newForm="form"
		tableForm="categoryForm"
	>
		<template v-slot:item.in_product_listing="{ item }">{{ item.in_product_listing ? "Yes" : "No" }}</template>

		<template v-slot:item.actions="{ item }">
			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="disableActions"
						@click.stop="item.form=form,editItem(item)"
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
						:disabled="disableActions"
						@click.stop="item.form=form, viewItem(item)"
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
	data() {
		return {
			form: "categoryForm",
			headers: [
				{ text: "#", value: "id" },
				{ text: "Name", value: "name" },
				{ text: "In product listing", value: "in_product_listing" },
				{ text: "Actions", value: "actions" }
			]
		};
	},

	computed: {
		...mapState("dialog", ["interactive_dialog"]),
		...mapState("datatable", ["loading"]),

		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		},

		disableActions: {
			get() {
				return this.loading;
			},
			set(value) {
				this.setLoading(value);
			}
		}
	},

	methods: {
		...mapMutations("dialog", ["editItem", "viewItem"]),
		...mapMutations("datatable", ["setLoading"])
	}
};
</script>
