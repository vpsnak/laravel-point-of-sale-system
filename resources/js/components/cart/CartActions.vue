<template>
	<div>
		<v-divider />

		<v-btn
			block
			class="my-2"
			@click="checkout"
			:disabled="disabled"
		>Checkout</v-btn>

		<v-divider />

		<div class="d-flex align-center justify-center pa-2">
			<v-btn
				icon
				@click="showRestoreOnHoldCartDialog"
				class="flex-grow-1"
				tile
			>
				<v-icon>fa-recycle</v-icon>
				<v-badge
					overlap
					color="purple"
					style="position: absolute; top: 0;right:38%;"
				>
					<template v-slot:badge>
						<span>{{ cartOnHoldSize }}</span>
					</template>
				</v-badge>
			</v-btn>
			<v-btn
				icon
				:disabled="disabled"
				@click="holdCart"
				class="flex-grow-1"
				tile
			>
				<v-icon>pause</v-icon>
			</v-btn>
			<v-btn
				icon
				@click.stop="emptyCart(true)"
				:disabled="disabled"
				class="flex-grow-1"
				tile
			>
				<v-icon>delete</v-icon>
			</v-btn>
		</div>

		<checkoutDialog :show="checkoutDialog" />
		<cartOnHoldDialog :show="cartOnHoldDialog" />
	</div>
</template>

<script>
	import { mapActions, mapState, mapGetters } from "vuex";

	export default {
		props: {
			disabled: Boolean
		},
		computed: {
			checkoutDialog: {
				get() {
					return this.$store.state.checkoutDialog;
				},
				set(value) {
					this.$store.state.checkoutDialog = value;
				}
			},
			restoreCartDialog: {
				get() {
					return this.$store.state.restoreCartDialog;
				},
				set(value) {
					this.$store.state.restoreCartDialog = value;
				}
			},
			cartOnHoldDialog: {
				get() {
					return this.$store.state.cartOnHoldDialog;
				},
				set(value) {
					this.$store.state.cartOnHoldDialog = value;
				}
			},
			cartOnHoldSize() {
				return _.size(this.cartsOnHold);
			}
		},
		mounted() {
			this.getHolds();
		},
		methods: {
			checkout() {
				this.checkoutDialog = true;
				this.submitOrder();
			},
			emptyCart(showPrompt) {
				if (showPrompt) {
					confirm("Are you sure you want to delete the cart?") &&
						this.$store.commit("cart/emptyCart");
					this.$store.commit("cart/setCustomer", undefined);
				} else {
					this.products.splice(0);
					this.$store.commit("cart/setCustomer", undefined);
				}
			},
			holdCart() {
				let payload = {
					model: "carts",
					data: {
						customer_id: this.$store.state.cart.customer.id,
						cart: this.products
					}
				};
				this.create(payload).then(() => {
					this.emptyCart(false);
					this.getHolds();
				});
			},
			getHolds() {
				this.getAll({
					model: "carts"
				}).then(carts => {
					this.cartsOnHold = carts;
				});
			},
			showRestoreOnHoldCartDialog() {
				this.getCartsOnHold();
				this.restoreCartDialog = true;
			},
			getCartsOnHold() {
				let a;
				let payload = {
					model: "carts",
					mutation: "setCartsOnHold"
				};
				this.$store.dispatch("getAll", payload);
			},
			...mapActions({
				getAll: "getAll",
				getOne: "getOne",
				create: "create",
				delete: "delete"
			}),
			...mapActions("cart", {
				submitOrder: "submitOrder"
			})
		}
	};
</script>