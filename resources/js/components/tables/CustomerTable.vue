<template>
	<data-table v-if="data_table.model">
		<template v-slot:item.email="{ item }">
			<a :href="'mailto:' + item.email">{{ item.email }}</a>
		</template>

		<template v-slot:item.phone="{ item }">
			<a :href="'tel:' + item.phone">{{ item.phone }}</a>
		</template>

		<template v-slot:item.no_tax="{ item }">
			{{
			item.no_tax ? "Yes" : "No"
			}}
		</template>

		<template v-slot:item.house_account_status="{ item }">
			{{
			item.house_account_status ? "Yes" : "No"
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
			icon: "mdi-account-group",
			title: "Customers",
			headers: this.headers,
			model: "customers",
			component: this.form,
			newForm: "customerNewForm",
			btnTxt: "New Customer",
			loading: true
		});
	},
	data() {
		return {
			form: "customer",
			headers: [
				{ text: "#", value: "id" },
				{ text: "First name", value: "first_name" },
				{ text: "Last name", value: "last_name" },
				{ text: "E-mail", value: "email" },
				{ text: "No tax", value: "no_tax" },
				{ text: "House Account", value: "house_account_status" },
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
