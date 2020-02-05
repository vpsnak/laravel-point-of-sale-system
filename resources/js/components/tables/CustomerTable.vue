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
		<template v-slot:item.email="{ item }">
			<a :href="'mailto:' + item.email">{{ item.email }}</a>
		</template>

		<template v-slot:item.phone="{ item }">
			<a :href="'tel:' + item.phone">{{ item.phone }}</a>
		</template>

		<template v-slot:item.no_tax="{ item }">{{ item.no_tax ? "Yes" : "No" }}</template>

		<template
			v-slot:item.house_account_status="{ item }"
		>{{ item.house_account_status ? "Yes" : "No" }}</template>

		<template v-slot:item.actions="{ item }">
			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="disableActions"
						@click.stop="editItemDialog(item)"
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
						@click.stop="viewItemDialog(item)"
						class="my-2"
						v-on="on"
						icon
					>
						<v-icon small>watch</v-icon>
					</v-btn>
				</template>
				<span>View</span>
			</v-tooltip>
		</template>
	</prop-data-table>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
	data() {
		return {
			form: "customerNewForm",
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
					value: "email"
				},
				{
					text: "No tax",
					value: "no_tax"
				},
				{
					text: "House Account",
					value: "house_account_status"
				},
				{
					text: "Actions",
					value: "actions"
				}
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
		...mapMutations("dialog", ["setDialog", "resetDialog"]),
		...mapMutations("datatable", ["setLoading"]),

		editItemDialog(item) {
			this.dialog = {
				show: true,
				fullscreen: false,
				width: 600,
				title: `Edit item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-pencil",
				component: this.form,
				model: _.cloneDeep(item),
				persistent: true
			};
		},
		viewItemDialog(item) {
			this.dialog = {
				show: true,
				fullscreen: false,
				width: 1000,
				title: `View item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-watch",
				component: this.form,
				model: item,
				persistent: false
			};
		}
	}
};
</script>
