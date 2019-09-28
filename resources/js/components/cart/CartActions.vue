<template>
	<div>
		<v-divider />

		<v-btn block class="my-2" @click="checkout" :disabled="disabled">Checkout</v-btn>

		<v-divider />

		<div class="d-flex align-center justify-center pa-2">
			<v-btn icon @click="showRestoreOnHoldCartDialog" class="flex-grow-1" tile>
				<v-icon>fa-recycle</v-icon>
				<v-badge overlap color="purple" style="position: absolute; top: 0;right:38%;">
					<template v-slot:badge>
						<span>{{ cartOnHoldSize }}</span>
					</template>
				</v-badge>
			</v-btn>
			<v-btn icon :disabled="disabled" @click="holdCart" class="flex-grow-1" tile>
				<v-icon>pause</v-icon>
			</v-btn>
			<v-btn icon @click.stop="emptyCart(true)" :disabled="disabled" class="flex-grow-1" tile>
				<v-icon>delete</v-icon>
			</v-btn>
		</div>

		<checkoutDialog :show="checkoutDialog" />
		<cartRestoreDialog :show="cartRestoreDialog" />
	</div>
</template>

<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	props: {
		disabled: Boolean
	},
	computed: {
		products: {
			get() {
				return this.$store.state.cart;
			},
			set() {
				this.$store.commit("cart/emptyCart");
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
		cartRestoreDialog: {
			get() {
				return this.$store.state.cartRestoreDialog;
			},
			set(value) {
				this.$store.state.cartRestoreDialog = value;
			}
		},
		cartOnHoldSize() {
			return _.size(this.cartsOnHold);
		}
	},
	mounted() {},
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
				this.products = null;
				this.$store.commit("cart/setCustomer", undefined);
			}
		},
		holdCart() {
			let payload = {
				model: "carts",
				data: {
					user_id: this.$store.state.user.id,
					cart: this.products
				}
			};
			this.create(payload).then(() => {
				this.emptyCart(false);
				this.$store.commit("setNotification", {
					msg: "Cart added on hold list",
					type: "info"
				});
			});
		},
		showRestoreOnHoldCartDialog() {
			this.getCartsOnHold();
			this.cartRestoreDialog = true;
		},
		getCartsOnHold() {
			let a;
			let payload = {
				model: "carts"
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