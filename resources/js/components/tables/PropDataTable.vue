<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field
				:disabled="loading"
				:search="search"
				prepend-icon="search"
				hide-details
				label="Search"
				single-line
				v-model="keyword"
				append-icon="mdi-close"
				@click:append="keyword=null, searchAction=null, paginate()"
				@click:prepend="search"
				@keyup.enter="search"
			></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn :disabled="btnDisable" color="primary" @click="createItemDialog">{{ this.btnTitle }}</v-btn>
		</v-card-title>
		<v-layout column>
			<v-flex md10 style="overflow: auto">
				<v-data-table
					disable-sort
					dense
					:disable-filtering="true"
					:headers="$store.state.datatable.headers"
					:items="rows"
					:loading="loading"
					:items-per-page="15"
					:page.sync="currentPage"
					:server-items-length="totalItems"
					@pagination="paginate"
					:footer-props="footerProps"
				>
					<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
						<slot :name="slot" v-bind="scope" />
					</template>
					<template
						v-if="dataUrl=== 'customers'"
						v-slot:item.customer.email="{ item }"
					>{{item.customer ? item.customer.email : 'Guest'}}</template>

					<template v-slot:item.action="{ item }">
						<!-- order actions -->
						<v-tooltip
							bottom
							v-if="
                                tableViewComponent === 'order' &&
                                    (item.status !== 'complete' &&
                                        item.status !== 'canceled')
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
									@click="rechargeGiftcardDialog(item) "
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
								<v-btn @click.stop="viewItemDialog(item)" class="my-1" v-on="on" icon>
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
			:fullscreen="dialog.fullscreen"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>

		<checkoutDialog :show="checkoutDialog" @close="resetCart()" :giftcard="true" />
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
				fullscreen: false,
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
			keyword: "",
			selectedItem: {},
			search_action: false
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
		searchAction: {
			get() {
				return this.search_action;
			},
			set(value) {
				this.search_action = value;
			}
		},
		footerProps() {
			return {
				"disable-pagination": this.loading,
				"disable-items-per-page": true,
				options: { itemsPerPage: 15, page: this.currentPage }
			};
		},
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
		resetCart(e) {
			console.log(e);
			this.$store.commit("cart/resetState");

			console.log("asd");
			this.paginate();
		},
		resetDialog() {
			this.dialog = {
				show: false,
				width: 600,
				fullscreen: false,
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
		rechargeGiftcardDialog(item) {
			this.dialog = {
				show: true,
				width: 600,
				title: `Recharge the giftcard #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-wallet-giftcard",
				component: "RechargeGiftCardToCart",
				model: item,
				persistent: true
			};

			this.action = "recharge";
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
		search(e, page) {
			if (this.keyword.length > 2 || this.searchAction) {
				this.setLoading(true);

				if (!page) {
					this.searchAction = this.keyword;
				} else {
					if (!this.keyword) {
						this.keyword = this.searchAction;
					}
				}

				let payload = {
					model: this.dataUrl,
					page: page || 1,
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
			}
		},

		paginate(e) {
			if (this.searchAction) {
				this.search(null, e.page);
			} else {
				this.searchAction = false;

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
			}
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
				fullscreen: this.tableViewComponent === "customer" ? true : false,
				width: 1000,
				title: `View item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-watch",
				component: this.tableViewComponent,
				model: item,
				persistent: true
			};
		},

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

		dialogEvent(event) {
			if (event) {
				switch (this.action) {
					case "cancelOrder":
						this.cancelOrder();
						break;
					case "recharge":
						this.checkoutDialog = true;
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
