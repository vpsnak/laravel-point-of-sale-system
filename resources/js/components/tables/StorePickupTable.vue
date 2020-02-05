<template>
	<data-table
		icon="mdi-storefront"
		title="Store pickups"
		:headers="headers"
		data-url="store-pickups"
		btnTxt="New Store Pickup"
		:newForm="form"
		:disableNewBtn="false"
	>
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
						@click.stop="item.form=form,viewItem(item)"
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
	data() {
		return {
			form: "storePickupForm",
			headers: [
				{ text: "#", value: "id" },
				{ text: "Name", value: "name" },
				{ text: "Street", value: "street" },
				{ text: "Street 1", value: "street1" },
				{ text: "Country ID", value: "country_id" },
				{ text: "Region", value: "region.default_name" },
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
