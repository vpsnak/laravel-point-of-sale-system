<template>
	<div>
		<prop-data-table
			:tableHeaders="headers"
			data-url="gift-cards"
			tableTitle="Gift Cards"
			tableBtnTitle="New Gift Card"
			tableForm="giftCardForm"
			tableViewComponent="giftCard"
		>
			<template v-slot:item.enabled="{ item }">{{ item.enabled ? "Yes" : "No" }}</template>

			<template v-slot:item.actions="{ item }">
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn @click="rechargeGiftcardDialog(item) " :loading="loading" class="my-1" icon v-on="on">
							<v-icon small>mdi-credit-card-plus</v-icon>
						</v-btn>
					</template>
					<span>Recharge</span>
				</v-tooltip>

				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn
							small
							:loading="loading"
							@click.stop="editItemDialog(item)"
							class="my-1"
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
							:loading="loading"
							@click.stop="viewItemDialog(item)"
							class="my-1"
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

		<checkoutDialog v-if="checkoutDialog" />
	</div>
</template>

<script>
import { mapMutations, mapState } from "vuex";

export default {
	data() {
		return {
			form: "giftCardForm",
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
					text: "Code",
					value: "code"
				},
				{
					text: "Enabled",
					value: "enabled"
				},
				{
					text: "Amount",
					value: "amount"
				},
				{
					text: "Actions",
					value: "actions",
					shortable: false
				}
			]
		};
	},
	computed: {
		...mapState("datatable", ["loading"]),
		...mapState("dialog", ["interactive_dialog"]),

		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		},
		checkoutDialog: {
			get() {
				return this.$store.state.cart.checkoutDialog;
			},
			set(value) {
				this.$store.commit("cart/setCheckoutDialog", value);
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
		...mapMutations("datatable", ["setLoading"]),
		...mapMutations("dialog", ["setDialog", "resetDialog"]),

		rechargeGiftcardDialog(item) {
			this.dialog = {
				show: true,
				width: 400,
				title: `Recharge the giftcard #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-wallet-giftcard",
				component: "RechargeGiftCardToCart",
				model: item,
				persistent: true
			};
		},
		dialogEvent(event) {
			if (event) {
				this.checkoutDialog = true;
			}

			this.resetDialog();
		},
		editItemDialog(item) {
			this.dialog = {
				show: true,
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
				width: 600,
				title: `View item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-watch",
				component: this.form,
				model: item,
				persistent: true
			};
		}
	}
};
</script>
