<template>
	<v-card>
		<v-card-title>
			{{ this.title }}
			<v-spacer></v-spacer>
			<v-text-field append-icon="search" hide-details label="Search" single-line v-model="search"></v-text-field>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-btn
				:disabled="btnDisable"
				color="primary"
				@click.stop="showCreateDialog = true"
			>{{this.btnTitle }}</v-btn>
		</v-card-title>
		<v-layout column>
			<v-flex md10 style="overflow: auto">
				<v-data-table
					dense
					:headers="headers"
					:items="rows"
					:loading="loading"
					:search="search"
					:items-per-page="15"
					:page.sync="currentPage"
					:server-items-length="totalItems"
					@pagination="paginate"
					:footer-props="{'disable-items-per-page':true, options: {itemsPerPage: 15, page: currentPage}}"
				>
					<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
						<slot :name="slot" v-bind="scope" />
					</template>
					<template v-slot:item.action="{ item }">
						<!-- order actions -->
						<v-tooltip
							bottom
							v-if="tableViewComponent === 'order' && (item.status === 'pending_payment' || item.status === 'pending')"
						>
							<template v-slot:activator="{ on }">
								<v-btn @click="checkout(item)" class="my-1" icon v-on="on">
									<v-icon small>mdi-currency-usd</v-icon>
								</v-btn>
							</template>
							<span>Continue checkout</span>
						</v-tooltip>
						<v-tooltip bottom v-if="tableViewComponent === 'order' && item.status !== 'canceled'">
							<template v-slot:activator="{ on }">
								<v-btn @click="selectedItem = item, cancelOrderDialog = true" class="my-1" icon v-on="on">
									<v-icon small>mdi-cancel</v-icon>
								</v-btn>
							</template>
							<span>Cancel order</span>
						</v-tooltip>

						<!-- gift card actions -->
						<v-tooltip bottom v-else-if="tableForm === 'giftCardForm'">
							<template v-slot:activator="{ on }">
								<v-btn
									@click="rechargeGiftcardDialog = true, selectedItem = item"
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
						<v-tooltip bottom v-if="tableForm != 'customerNewForm'">
							<template v-slot:activator="{ on }">
								<v-btn :disabled="btnDisable" @click.stop="editItem(item)" class="my-1" v-on="on" icon>
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
					<v-alert
						:value="true"
						color="error"
						icon="warning"
						slot="no-results"
					>Your search for "{{ search }}" found no results.</v-alert>
				</v-data-table>
			</v-flex>
		</v-layout>
		<!-- view/edit dialog -->
		<interactiveDialog
			v-if="showEditDialog"
			:show="showEditDialog"
			title="Edit item"
			:width="600"
			:component="form"
			:model="defaultObject"
			@action="result"
			persistent
			action="edit"
			titleCloseBtn
		></interactiveDialog>

		<interactiveDialog
			v-if="showViewDialog"
			:show="showViewDialog"
			title="View item"
			:fullscreen="false"
			:width="1000"
			:component="tableViewComponent"
			:model="viewId"
			@action="result"
			action="newItem"
			cancelBtnTxt="Close"
		></interactiveDialog>

		<interactiveDialog
			v-if="showCreateDialog"
			:show="showCreateDialog"
			:component="form"
			:title="btnTitle"
			:width="800"
			@action="result"
			cancelBtnTxt="Close"
			titleCloseBtn
		></interactiveDialog>

		<checkoutDialog :show="checkoutDialog" />

		<interactiveDialog
			v-if="rechargeGiftcardDialog"
			:show="rechargeGiftcardDialog"
			:model="selectedItem"
			:width="800"
			:title="'Recharge gift card #' + selectedItem.id"
			component="rechargeGiftcard"
			action="edit"
			@action="rechargeEvent"
		/>

		<interactiveDialog
			v-if="cancelOrderDialog"
			:show="cancelOrderDialog"
			action="confirmation"
			title="Cancel order?"
			content="Are you sure you want to <strong>cancel</strong> the selected order?"
			@action="cancelOrderConfirmation"
			actions
			persistent
		/>
	</v-card>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			current_page: 1,
			total_items: null,
			cancelOrderDialog: false,
			showCreateDialog: false,
			showEditDialog: false,
			showViewDialog: false,
			rechargeGiftcardDialog: false,
			defaultObject: {},
			viewId: null,
			search: "",
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
		})
	},
	methods: {
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
		cancelOrderConfirmation(event) {
			if (event) {
				let payload = {
					model: "orders",
					id: this.selectedItem.id
				};

				this.delete(payload).then(response => {
					this.paginate();
				});
			}
			this.closePrompt = false;
		},
		checkout(item) {
			this.$store.commit("cart/setOrder", item);
			this.$store.state.cart.checkoutSteps[0].completed = true;
			this.$store.state.cart.currentCheckoutStep = 2;

			this.checkoutDialog = true;
		},
		submitEvent(event) {
			if (event) {
				this.paginate();
			}
		},

		editItem(item) {
			this.defaultObject = item;
			this.action = "edit";
			this.showEditDialog = true;
		},

		viewItem(item) {
			this.viewId = { id: item.id };
			this.showViewDialog = true;
		},

		rechargeEvent(event) {
			this.rechargeGiftcardDialog = false;
		},

		result(event) {
			this.showCreateDialog = false;
			this.showEditDialog = false;
			this.showViewDialog = false;

			if (event) {
				this.paginate();
			}
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
			delete: "delete"
		})
	}
};
</script>
