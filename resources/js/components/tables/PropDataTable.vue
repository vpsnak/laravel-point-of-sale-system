<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field
				:disabled="loading"
				:search="search"
				append-icon="search"
				hide-details
				label="Search"
				single-line
				v-model="keyword"
				@click:append="search"
				@keyup.enter="search"
			></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn :disabled="btnDisable" color="primary" @click="createItemDialog">{{ this.btnTitle }}</v-btn>
		</v-card-title>
		<v-layout column>
			<v-flex md10 style="overflow: auto">
				<v-data-table
					dense
					:disable-filtering="true"
					:headers="$store.state.datatable.headers"
					:items="rows"
					:loading="loading"
					:items-per-page="15"
					:page.sync="currentPage"
					:server-items-length="totalItems"
					@pagination="paginate"
					:footer-props="{
                        'disable-items-per-page': true,
                        options: { itemsPerPage: 15, page: currentPage }
                    }"
				>
					<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
						<slot :name="slot" v-bind="scope" />
					</template>
					<template
						v-slot:item.customer.email="{ item }"
					>{{item.customer ? item.customer.email : 'Guest'}}</template>
					<template v-slot:item.action="{ item }">
						<!-- order actions -->
						<v-tooltip
							bottom
							v-if="
                                tableViewComponent === 'order' &&
                                    (item.status === 'pending_payment' ||
                                        item.status === 'pending')
                            "
						>
							<template v-slot:activator="{ on }">
								<v-btn @click="checkout(item)" class="my-1" icon v-on="on">
									<v-icon small>mdi-currency-usd</v-icon>
								</v-btn>
							</template>
							<span>Continue checkout</span>
						</v-tooltip>
						<v-tooltip
							bottom
							v-if="
                                tableViewComponent === 'order' &&
                                    item.status !== 'canceled'
                            "
						>
							<template v-slot:activator="{ on }">
								<v-btn
									@click="cancelOrderDialog(item)"
									class="my-1"
									icon
									v-on="on"
									:disabled="cancelOrderDisabled(item)"
								>
									<v-icon small>mdi-cancel</v-icon>
								</v-btn>
							</template>
							<span>Cancel order</span>
						</v-tooltip>

						<!-- gift card actions -->
						<v-tooltip bottom v-else-if="tableForm === 'giftCardForm'">
							<template v-slot:activator="{ on }">
								<v-btn
									@click="
                                        (rechargeGiftcardDialog = true),
                                            (selectedItem = item)
                                    "
									:disabled="btnDisable"
									class="my-1"
									icon
									v-on="on"
								>
									<v-icon small>mdi-credit-card-plus</v-icon>
								</v-btn>
							</template>
							<span>Recharge</span>
						</v-tooltip>
						<!-- user roles actions -->
						<v-tooltip bottom v-if="tableForm === 'userForm'">
							<template v-slot:activator="{ on }">
								<v-btn @click="roleDialog(item)" class="my-1" v-on="on" icon>
									<v-icon small>mdi-account-key</v-icon>
								</v-btn>
							</template>
							<span>Edit Role</span>
						</v-tooltip>
						<v-tooltip bottom v-if="tableForm === 'userForm'">
							<template v-slot:activator="{ on }">
								<v-btn @click="changePasswordDialog(item)" class="my-1" v-on="on" icon>
									<v-icon small>mdi-lock-reset</v-icon>
								</v-btn>
							</template>
							<span>Change {{ item.name }} password</span>
						</v-tooltip>

						<v-tooltip bottom v-if="tableForm != 'customerNewForm'">
							<template v-slot:activator="{ on }">
								<v-btn :disabled="btnDisable" @click="editItemDialog(item)" class="my-1" v-on="on" icon>
									<v-icon small>edit</v-icon>
								</v-btn>
							</template>
							<span>Edit</span>
						</v-tooltip>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn @click.stop="viewItem(item)" class="my-1" v-on="on" icon>
									<v-icon small>watch</v-icon>
								</v-btn>
							</template>
							<span>View</span>
						</v-tooltip>
					</template>
					<v-alert :value="true" color="error" icon="warning" slot="no-results">
						Your search for "{{ keyword }}" found no
						results.
					</v-alert>
				</v-data-table>
			</v-flex>
		</v-layout>

		<interactiveDialog
			v-if="dialog.show"
			:show="dialog.show"
			:title="dialog.title"
			:titleCloseBtn="dialog.titleCloseBtn"
			:icon="dialog.icon"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>

		<checkoutDialog :show="checkoutDialog" />
	</v-card>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			dialog: {
				show: false,
				width: 600,
				icon: "",
				title: "",
				titleCloseBtn: false,
				component: "",
				content: "",
				model: "",
				persistent: false
			},
			current_page: 1,
			total_items: null,
			action: "",
			defaultObject: {},
			viewId: null,
			keyword: "",
			selectedItem: {}
		};
	},
	props: [
		"tableTitle",
		"tableHeaders",
		"dataUrl",
		"tableBtnTitle",
		"tableForm",
		"tableViewComponent",
		"tableBtnDisable"
	],
	mounted() {
		this.setHeaders(this.tableHeaders);
		this.setTitle(this.tableTitle);
		this.setBtnTitle(this.tableBtnTitle);
		this.setBtnDisable(this.tableBtnDisable);
		this.setForm(this.tableForm);
	},
	computed: {
		totalItems: {
			get() {
				return this.total_items;
			},
			set(value) {
				this.total_items = value;
			}
		},
		currentPage: {
			get() {
				return this.current_page;
			},
			set(value) {
				this.current_page = value;
			}
		},
		checkoutDialog: {
			get() {
				return this.$store.state.checkoutDialog;
			},
			set(value) {
				if (!value) {
					this.paginate();
				}

				this.$store.state.checkoutDialog = value;
			}
		},
		...mapState("datatable", {
			title: "title",
			headers: "headers",
			rows: "rows",
			loading: "loading",
			btnTitle: "btnTitle",
			form: "form",
			btnDisable: "btnDisable"
		}),
		user() {
			return this.$store.state.user;
		},
		role() {
			return this.$store.getters.role;
		}
	},
	methods: {
		resetDialog() {
			this.dialog = {
				show: false,
				width: 600,
				title: "",
				titleCloseBtn: false,
				icon: "",
				component: "",
				content: "",
				model: "",
				persistent: false
			};

			this.action = "";
		},
		cancelOrderDialog(item) {
			this.dialog = {
				show: true,
				width: 600,
				title: `Verify your password to cancel order #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-lock-alert",
				component: "passwordForm",
				model: { action: "verify" },
				persistent: true
			};

			this.selectedItem = item;

			this.action = "cancelOrder";
		},
		cancelOrderDisabled(item) {
			if (this.role == "admin" || item.created_by.id === this.user.id) {
				return false;
			} else {
				return true;
			}
		},
		cashierDisabled() {
			if (this.role == "admin" || this.role == "store_manager") {
				return false;
			} else {
				return true;
			}
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
		search() {
			if (this.keyword) {
				this.setLoading(true);

				let payload = {
					model: this.dataUrl,
					page: 1,
					keyword: this.keyword,
					dataTable: true
				};

				this.searchRequest(payload)
					.then(response => {
						this.setRows(response.data);

						if (response.total !== this.totalItems) {
							this.totalItems = response.total;
						}
					})
					.finally(() => {
						this.setLoading(false);
					});
			} else {
				this.paginate();
			}
		},

		paginate(e) {
			this.setLoading(true);

			this.getAll({
				model: this.dataUrl,
				page: e ? e.page : this.currentPage,
				dataTable: true
			})
				.then(response => {
					this.setRows(response.data);

					if (response.total !== this.totalItems) {
						this.totalItems = response.total;
					}
				})
				.finally(() => {
					this.setLoading(false);
				});
		},

		cancelOrder() {
			let payload = {
				model: "orders",
				id: this.selectedItem.id
			};

			this.delete(payload).then(response => {
				this.paginate();
			});
		},

		checkout(item) {
			this.$store.commit("cart/setOrder", item);
			this.$store.state.cart.checkoutSteps[0].completed = true;
			this.$store.state.cart.currentCheckoutStep = 2;

			this.checkoutDialog = true;
		},

		editItemDialog(item) {
			this.dialog = {
				show: true,
				width: 600,
				title: `Edit item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-pencil",
				component: this.form,
				model: item,
				persistent: true
			};
		},

		createItemDialog() {
			this.dialog = {
				show: true,
				width: 600,
				title: `${this.btnTitle}`,
				titleCloseBtn: true,
				component: this.form,
				persistent: true
			};
		},

		viewItemDialog(item) {
			this.dialog = {
				show: true,
				width: 1000,
				title: `View item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-account-key",
				component: "tableViewComponent",
				model: item.id,
				persistent: true
			};
		},

		roleDialog(item) {
			this.dialog = {
				show: true,
				width: 600,
				title: `Edit role for: ${item.name}`,
				titleCloseBtn: true,
				icon: "mdi-account-key",
				component: "userRoleForm",
				model: item,
				persistent: true
			};
		},

		dialogEvent(event) {
			if (event) {
				switch (this.action) {
					case "cancelOrder":
						this.cancelOrder();
						break;
					default:
						break;
				}
				this.paginate();
			}

			this.resetDialog();
		},

		...mapActions("datatable", {
			deleteRow: "deleteRow"
		}),

		...mapMutations("datatable", {
			setHeaders: "setHeaders",
			setTitle: "setTitle",
			setBtnTitle: "setBtnTitle",
			setForm: "setForm",
			setBtnDisable: "setBtnDisable",
			setRows: "setRows",
			setLoading: "setLoading"
		}),

		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete",
			searchRequest: "search"
		})
	}
};
</script>
