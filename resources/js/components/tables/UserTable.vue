<template>
	<div>
		<prop-data-table
			:tableHeaders="headers"
			data-url="users"
			tableTitle="Users"
			tableBtnTitle="New User"
			tableForm="userForm"
			:tableBtnDisable="false"
			tableViewComponent="user"
		>
			<template v-slot:item.email="{ item }">
				<a :href="'mailto:' + item.email">{{ item.email }}</a>
			</template>
			<template v-slot:item.phone="{ item }">
				<a :href="'tel:' + item.phone">{{ item.phone }}</a>
			</template>

			<template v-slot:item.actions="{ item }">
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn small :disabled="disableActions" @click="roleDialog(item)" class="my-2" v-on="on" icon>
							<v-icon small>mdi-account-key</v-icon>
						</v-btn>
					</template>
					<span>Edit Role</span>
				</v-tooltip>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn
							small
							:disabled="disableActions"
							@click="changePasswordDialog(item)"
							class="my-1"
							v-on="on"
							icon
						>
							<v-icon small>mdi-lock-reset</v-icon>
						</v-btn>
					</template>
					<span>Change {{ item.name }} password</span>
				</v-tooltip>
			</template>
		</prop-data-table>

		<interactiveDialog
			v-if="dialog.show"
			:show="dialog.show"
			:title="dialog.title"
			:titleCloseBtn="dialog.titleCloseBtn"
			:icon="dialog.icon"
			:fullscreen="dialog.fullscreen"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>
	</div>
</template>

<script>
import { mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			form: "userForm",
			headers: [
				{
					text: "#",
					value: "id"
				},
				{
					text: "Name",
					value: "name"
				},
				{
					text: "Email",
					value: "email"
				},
				{
					text: "Phone",
					value: "phone"
				},
				{
					text: "Role",
					value: "roles[0].name"
				},
				{
					text: "Created_at",
					value: "created_at"
				},
				{
					text: "Updated_at",
					value: "updated_at"
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

		disableActions: {
			get() {
				return this.loading;
			},
			set(value) {
				this.setLoading(value);
			}
		},
		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		}
	},

	methods: {
		...mapMutations("dialog", ["setDialog", "resetDialog"]),
		...mapMutations("datatable", ["setLoading"]),

		roleDialog(item) {
			this.dialog = {
				show: true,
				width: 450,
				title: `Edit role for: ${item.name}`,
				titleCloseBtn: true,
				icon: "mdi-account-key",
				component: "userRoleForm",
				model: item,
				persistent: true
			};
		},

		changePasswordDialog(item) {
			this.selectedItem = item;
			this.selectedItem["action"] = "change";

			this.dialog = {
				show: true,
				width: 600,
				title: `Change password for user: ${item.name}`,
				titleCloseBtn: true,
				icon: "mdi-lock-reset",
				component: "passwordForm",
				model: item,
				persistent: true
			};
		},

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
		},

		dialogEvent(event) {
			this.resetDialog();
		}
	}
};
</script>
