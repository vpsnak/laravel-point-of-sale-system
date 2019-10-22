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
				<v-data-table dense :headers="headers" :items="rows" :loading="loading" :search="search">
					<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
						<slot :name="slot" v-bind="scope" />
					</template>
					<template v-slot:item.action="{ item }">
						<v-tooltip bottom v-if="tableViewComponent === 'order'">
							<template v-slot:activator="{ on }">
								<v-btn
									@click="checkout(item)"
									class="my-1"
									icon
									v-on="on"
									:disabled="item.status !== 'pending_payment'"
								>
									<v-icon small>mdi-currency-usd</v-icon>
								</v-btn>
							</template>
							<span>Checkout</span>
						</v-tooltip>
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
						<v-tooltip bottom>
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
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn
									:disabled="btnDisable"
									@click="deleteConfirmation = true, selectedItem = item"
									class="my-1"
									v-on="on"
									icon
								>
									<v-icon small>delete</v-icon>
								</v-btn>
							</template>
							<span>Delete</span>
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
			cancelBtnTxt="Close"
		></interactiveDialog>

		<interactiveDialog
			v-if="showCreateDialog"
			:show="showCreateDialog"
			:component="form"
			:title="btnTitle"
			@action="result"
			cancelBtnTxt="Close"
		></interactiveDialog>

		<interactiveDialog
			v-if="deleteConfirmation"
			:show="deleteConfirmation"
			title="Confirm delete"
			content="Are you sure you want to delete this item?"
			action="confirmation"
			cancelBtnTxt="No"
			confirmationBtnTxt="Yes"
			@action="deleteEvent"
		/>

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
	</v-card>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			showCreateDialog: false,
			showEditDialog: false,
			showViewDialog: false,
			deleteConfirmation: false,
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
		this.getRows({
			url: this.dataUrl
		});
		this.setTitle(this.tableTitle);
		this.setBtnTitle(this.tableBtnTitle);
		this.setBtnDisable(this.tableBtnDisable);
		this.setForm(this.tableForm);
	},
	computed: {
		checkoutDialog: {
			get() {
				if (this.$store.state.checkoutDialog === false) {
					this.getRows({
						url: this.dataUrl
					});
				}
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
		})
	},
	methods: {
		checkout(item) {
			this.$store.commit("cart/setOrder", item);
			this.$store.state.cart.checkoutSteps[1].completed = true;
			this.$store.state.cart.currentCheckoutStep = 2;

			this.checkoutDialog = true;
		},
		submitEvent(event) {
			if (event) {
				this.getRows({
					url: this.dataUrl
				});
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

		deleteEvent(event) {
			if (event) {
				this.deleteItem();
			}
			this.deleteConfirmation = false;
		},
		deleteItem() {
			this.deleteRow({
				url: this.dataUrl + "/" + this.selectedItem.id,
				data: {
					id: this.selectedItem.id
				}
			});
		},
		result(event) {
			this.showCreateDialog = false;
			this.showEditDialog = false;
			this.showViewDialog = false;
			this.showDeleteDialog = false;
		},

		...mapActions("datatable", {
			getRows: "getRows",
			deleteRow: "deleteRow"
		}),
		...mapMutations("datatable", {
			setHeaders: "setHeaders",
			setTitle: "setTitle",
			setBtnTitle: "setBtnTitle",
			setForm: "setForm",
			setBtnDisable: "setBtnDisable"
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
