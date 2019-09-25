<template>
	<v-sheet
		class="pa-3 d-flex flex-column"
		style="height:100%"
	>
		<div class="d-flex align-center justify-space-between">
			<div class="d-flex align-center">
				<v-icon>shopping_cart</v-icon>
				<h5 class="title-2 ml-2">Shopping cart</h5>
			</div>
			<v-switch
				label="Retail"
				v-model="retail"
			></v-switch>
		</div>

		<customerSearch :keywordLength="1"></customerSearch>

		<cartProducts :cartProducts="cartProducts"></cartProducts>

		<v-divider />
		<div class="d-flex flex-column">
			<v-row class="d-flex justify-space-between align-center">
				<v-col
					cols="3"
					class="px-2 py-0"
				>
					<v-label>Cart discount</v-label>
				</v-col>
				<v-col
					cols="9"
					class="px-2 py-0"
				>
					<cartDiscount :model="cartProducts"></cartDiscount>
				</v-col>
			</v-row>

			<v-divider />

			<cartTotals :cartProducts="cartProducts" />

			<v-divider />

			<v-btn
				block
				class="my-2"
				@click="checkout"
				:disabled="totalCartProducts"
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
							<span>{{cartsOnHoldSize}}</span>
						</template>
					</v-badge>
				</v-btn>
				<v-btn
					icon
					:disabled="totalCartProducts"
					@click="holdCart"
					class="flex-grow-1"
					tile
				>
					<v-icon>pause</v-icon>
				</v-btn>
				<v-btn
					icon
					@click.stop="emptyCart(true)"
					:disabled="totalCartProducts"
					class="flex-grow-1"
					tile
				>
					<v-icon>delete</v-icon>
				</v-btn>
			</div>
		</div>

		<v-dialog
			v-model="checkoutDialog"
			fullscreen
			hide-overlay
			transition="dialog-bottom-transition"
		>
			<checkoutWizard />
		</v-dialog>
		<v-dialog
			v-model="restoreCartDialog"
			width="500"
		>
			<restoreCartDialog />
		</v-dialog>
	</v-sheet>
</template>

<script>
	import { mapActions, mapState, mapGetters } from "vuex";

	export default {
		data() {
			return {
				model: "customers"
			};
		},
		mounted() {
			this.getHolds();
		},
		computed: {
			retail: {
				get() {
					return this.$store.state.cart.retail;
				},
				set() {
					this.$store.commit("cart/toggleRetail");
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
			restoreCartDialog: {
				get() {
					return this.$store.state.restoreCartDialog;
				},
				set(value) {
					this.$store.state.restoreCartDialog = value;
				}
			},
			totalCartProducts() {
				return _.size(this.cartProducts) ? false : true;
			},
			cartsOnHold: {
				get() {
					return this.$store.state.cartsOnHold;
				},
				set(value) {
					this.$store.state.cartsOnHold = value;
				}
			},
			cartsOnHoldSize() {
				return _.size(this.cartsOnHold);
			},
			cartProducts: {
				get() {
					return this.$store.state.cart.cartProducts;
				},
				set(value) {
					this.$store.state.cart.cartProducts = value;
				}
			},
			customerList: {
				get() {
					return this.$store.state.customerList;
				},
				set(value) {
					this.$store.state.customerList = value;
				}
			}
		},

		methods: {
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

			emptyCart(showPrompt) {
				if (showPrompt) {
					confirm("Are you sure you want to delete the cart?") &&
						this.cartProducts.splice(0);
					this.$store.commit("cart/setCustomer", undefined);
				} else {
					this.cartProducts.splice(0);
					this.$store.commit("cart/setCustomer", undefined);
				}
			},
			addAll(cart) {
				this.cartProducts.splice(0);
				this.cartProducts = cart;
			},
			checkout() {
				this.checkoutDialog = true;
				this.submitOrder()
					.then(() => {
						console.log("spinn");
					})
					.catch(error => {
						console.log("eased");
					});
			},
			holdCart() {
				let payload = {
					model: "carts",
					data: {
						customer_id: this.$store.state.cart.customer.id,
						cart: this.cartProducts
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
