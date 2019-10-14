<template>
	<div>
		<v-divider />

		<v-btn
			block
			class="my-2"
			@click.stop="checkout"
			:disabled="disabled || checkoutLoading"
			:loading="checkoutLoading"
		>Checkout</v-btn>

		<v-divider />

		<div class="d-flex align-center justify-center pa-2">
			<v-btn
				icon
				@click="showRestoreOnHoldCartDialog"
				:disabled="checkoutLoading"
				class="flex-grow-1"
				tile
			>
				<v-icon>fa-recycle</v-icon>
				<v-badge overlap color="purple" style="position: absolute; top: 0;right:38%;">
					<template v-slot:badge>
						<span>{{ cartsOnHoldSize }}</span>
					</template>
				</v-badge>
			</v-btn>
			<v-btn icon :disabled="disabled || checkoutLoading" @click="holdCart" class="flex-grow-1" tile>
				<v-icon>pause</v-icon>
			</v-btn>
			<v-btn
				icon
				@click.stop="emptyCart(true)"
				:disabled="disabled || checkoutLoading"
				class="flex-grow-1"
				tile
			>
				<v-icon>delete</v-icon>
			</v-btn>
		</div>

		<checkoutDialog :show="checkoutDialog" />
		<cartRestoreDialog :show="cartRestoreDialog" :key="cartsOnHoldSize" />
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			checkoutLoading: false,
			carts_on_hold_size: 0
		};
	},
	props: {
		disabled: Boolean
	},

	mounted() {
		this.getCartsOnHoldSize();
	},

	computed: {
		cartsOnHoldSize: {
			get() {
				return this.carts_on_hold_size;
			},
			set(value) {
				this.carts_on_hold_size = value;
			}
		},
		products: {
			get() {
				return this.$store.state.cart;
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
		}
	},

	methods: {
		checkout() {
			this.checkoutLoading = true;

			this.submitOrder()
				.then(response => {
					this.checkoutDialog = true;
				})
				.finally(() => {
					this.checkoutLoading = false;
				});
		},
		emptyCart(showPrompt) {
			if (showPrompt) {
				confirm("Are you sure you want to delete the cart?") &&
					this.$store.commit("cart/resetState");
			} else {
				this.$store.commit("cart/resetState");
			}
		},
		holdCart() {
			let payload = {
				model: "carts",
				data: {
					cash_register_id: this.$store.state.cashRegister.id,
					cart: {
						products: this.products,
						customer_id: this.$store.state.cart.customer
							? this.$store.state.cart.customer.id
							: null
					}
				}
			};
			this.create(payload).then(() => {
				this.getCartsOnHoldSize();
				this.emptyCart(false);

				this.$store.commit("setNotification", {
					msg: "Cart added on hold list",
					type: "info"
				});
			});
		},
		showRestoreOnHoldCartDialog() {
			this.cartRestoreDialog = true;
		},
		getCartsOnHoldSize() {
			let payload = {
				model: "carts"
			};
			this.$store.dispatch("getAll", payload).then(response => {
				this.cartsOnHoldSize = _.size(response);
			});
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