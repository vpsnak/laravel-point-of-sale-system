<template>
	<div>
		<data-table
			icon="mdi-buffer"
			title="Orders"
			:headers="headers"
			data-url="orders"
			:disableNewBtn="true"
		>
			<template v-slot:item.customer="{ item }">{{ item.customer ? item.customer.email : "Guest" }}</template>
			<template v-slot:item.total="{ item }">$ {{ item.total.toFixed(2) }}</template>
			<template v-slot:item.total_paid="{ item }">$ {{ item.total_paid.toFixed(2) }}</template>

			<template v-slot:item.status="{ item }">
				<span :class="statusColor(item.status)">
					<b>{{ parseStatusName(item.status) }}</b>
				</span>
			</template>

			<template v-slot:item.actions="{ item }">
				<v-tooltip bottom v-if="item.status !== ('complete' || 'canceled')">
					<template v-slot:activator="{ on }">
						<v-btn
							:ref="item.id"
							small
							:disabled="disableActions"
							@click.stop="checkout(item.id)"
							class="my-2"
							icon
							v-on="on"
						>
							<v-icon small>mdi-currency-usd</v-icon>
						</v-btn>
					</template>
					<span>Continue checkout</span>
				</v-tooltip>

				<v-tooltip bottom v-if="item.status !== 'canceled'" color="red">
					<template v-slot:activator="{ on }">
						<v-btn
							icon
							small
							color="red"
							:disabled="disableActions"
							@click.stop="cancelOrderDialog(item)"
							class="my-2"
							v-on="on"
						>
							<v-icon small>mdi-cancel</v-icon>
						</v-btn>
					</template>
					<span>Cancel order</span>
				</v-tooltip>

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

		<checkoutDialog v-if="checkoutDialog" />
	</div>
</template>

<script>
import { EventBus } from "../../plugins/event-bus";
import { mapMutations, mapActions, mapState } from "vuex";

export default {
	mounted() {
		EventBus.$on("dialog", event => {
			if (
				event.component === "passwordForm" &&
				event.payload &&
				this.selectedItem
			) {
				this.disableActions = true;
				this.cancelOrder().then(() => {
					EventBus.$emit("data-table", { action: "paginate" });
				});
			}
		});
	},

	beforeDestroy() {
		EventBus.$off();
	},

	data() {
		return {
			form: "order",
			selectedItem: null,
			headers: [
				{ text: "#", value: "id" },
				{ text: "Customer", value: "customer" },
				{ text: "Store", value: "store.name" },
				{ text: "Status", value: "status" },
				{ text: "Total", value: "total" },
				{ text: "Total paid", value: "total_paid" },
				{ text: "Created by", value: "created_by.name" },
				{ text: "Created at", value: "created_at" },
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
		...mapMutations("dialog", ["setDialog", "editItem", "viewItem"]),
		...mapMutations("datatable", ["setLoading"]),
		...mapActions(["getOne", "delete"]),

		parseStatusName(value) {
			return _.upperFirst(value.replace("_", " "));
		},
		statusColor(status) {
			switch (status) {
				case "canceled":
					return "red--text";
				case "pending":
					return "primary--text";
				case "pending_payment":
					return "primary--text";
				case "paid":
					return "cyan--text";
				case "complete":
					return "green--text";
				default:
					return "";
			}
		},

		checkout(id) {
			this.disableActions = true;
			this.getOne({
				model: "orders",
				data: { id },
				mutation: "cart/setCustomer"
			})
				.then(response => {
					this.$store.commit("cart/resetState");
					this.$store.state.cart.checkoutSteps[0].completed = true;
					this.$store.state.cart.currentCheckoutStep = 2;
					this.$store.commit("cart/setOrder", response);
					this.checkoutDialog = true;
				})
				.catch(error => {
					// unhandled error
					console.log(error.response);
				})
				.finally(() => {
					this.disableActions = false;
				});
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
				persistent: true,
				eventChannel: "dialog"
			};

			this.selectedItem = item;
		},
		cancelOrder() {
			return new Promise((resolve, reject) => {
				let payload = {
					model: "orders",
					id: this.selectedItem.id
				};

				this.delete(payload)
					.then(() => {
						resolve(true);
					})
					.catch(error => {
						// unhandled error
						console.log(error.response);
						reject(error);
					})
					.finally(() => {
						this.selectedItem = null;
					});
			});
		}
	}
};
</script>
