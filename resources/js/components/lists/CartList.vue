<template>
	<v-card>
		<v-card-text>
			<v-row align="center" justify="center">
				<v-col align="center" justify="center">
					<v-icon>shopping_cart</v-icon>
					<h3 class="text-center">Shopping cart</h3>
				</v-col>
			</v-row>
			<v-row>
				<v-col>
					<v-toolbar flat>
						<v-text-field placeholder="Search customer" class="grey--text">
							<v-icon slot="prepend">mdi-magnify</v-icon>
						</v-text-field>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on" class="ml-1">
									<v-icon>person_add</v-icon>
								</v-btn>
							</template>
							<span>Add customer</span>
						</v-tooltip>
					</v-toolbar>
					<v-divider />
				</v-col>
			</v-row>
			<v-row style="height: 33vh; overflow-y: auto;">
				<v-col>
					<v-list dense style="height:60vh; overflow-y:auto;">
						<v-list-group v-for="cartProduct in cartProducts" :key="cartProduct.id">
							<template v-slot:activator>
								<v-list-item dense>
									<v-layout row>
										<v-list-item-content>
											<v-list-item-title>{{ cartProduct.name }}</v-list-item-title>
											<v-list-item-subtitle>$ {{ cartProduct.qty * cartProduct.price.amount }}</v-list-item-subtitle>
										</v-list-item-content>
										<v-list-item-action>
											<v-btn icon @click.stop="decreaseQty(cartProduct)">
												<v-icon color="grey lighten-1">remove</v-icon>
											</v-btn>
										</v-list-item-action>
										<v-list-item-action>
											<v-chip filter @click.stop>
												<span>{{ cartProduct.qty }}</span>
											</v-chip>
										</v-list-item-action>
										<v-list-item-action>
											<v-btn icon @click.stop="increaseQty(cartProduct)">
												<v-icon color="grey lighten-1">add</v-icon>
											</v-btn>
										</v-list-item-action>
										<v-list-item-action>
											<v-btn icon @click.stop="removeItem(cartProduct)">
												<v-icon color="grey lighten-1">delete</v-icon>
											</v-btn>
										</v-list-item-action>
									</v-layout>
								</v-list-item>
							</template>
							<v-list-item>
								<v-layout row>
									<v-col cols="12" md="3">
										<v-text-field type="number" label="Qty" v-model="cartProduct.qty" min="1"></v-text-field>
									</v-col>
									<v-col cols="12" md="5">
										<v-select
											:items="discountTypes"
											v-model="cartProduct.discount_type"
											label="Discount"
											item-text="label"
											item-value="value"
										></v-select>
									</v-col>
									<v-col
										cols="12"
										md="4"
										v-if="cartProduct.discount_type && cartProduct.discount_type != 'None'"
									>
										<v-text-field type="number" label="Amount" min="1" v-model="cartProduct.discount_amount"></v-text-field>
									</v-col>

									<v-col cols="12" md="12">
										<v-textarea
											v-model="cartProduct.notes"
											rows="3"
											label="Notes"
											:hint="'For product: ' + cartProduct.name"
											counter
											no-resize
										></v-textarea>
									</v-col>
								</v-layout>
							</v-list-item>
						</v-list-group>
					</v-list>
				</v-col>
			</v-row>

			<v-row>
				<v-col>
					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Sub total</span>
						<span class="pa-2">$ {{ subTotal }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Tax</span>
						<span class="pa-2">$ {{ tax }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Total discount</span>
						<span class="pa-2">$ {{ totalDiscount }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Total</span>
						<span class="pa-2">$ {{ total }}</span>
					</div>

					<v-divider />

					<v-dialog
						v-model="checkoutDialog"
						fullscreen
						hide-overlay
						transition="dialog-bottom-transition"
					>
						<template v-slot:activator="{ on }">
							<v-btn block class="my-2" @click="checkout" v-on="on" :disabled="totalCartProducts">Checkout</v-btn>
						</template>

						<checkoutWizard />>
					</v-dialog>

					<v-divider />
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="4" class="text-center">
					<div class="text-center">
						<v-badge overlap color="purple">
							<template v-slot:badge>
								<span>{{cartsOnHoldSize}}</span>
							</template>
							<v-btn icon @click="restoreCartDialog = true" :disabled="cartsOnHoldSize ? false : true">
								<v-icon>fa-recycle</v-icon>
							</v-btn>
						</v-badge>
					</div>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon :disabled="totalCartProducts" @click="holdCart">
						<v-icon>pause</v-icon>
					</v-btn>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon @click.stop="removeAll(cartProducts)" :disabled="totalCartProducts">
						<v-icon>delete</v-icon>
					</v-btn>
				</v-col>
			</v-row>
		</v-card-text>
		<v-card-actions></v-card-actions>

		<v-dialog v-model="restoreCartDialog" width="500">
			<restoreCartDialog />
		</v-dialog>
		<v-btn @click="submitCart">
			Submit
		</v-btn>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			discountTypes: [
				{
					label: "None",
					value: "None"
				},
				{
					label: "Flat",
					value: "Flat"
				},
				{
					label: "Percentage",
					value: "Percentage"
				}
			]
		};
	},

	computed: {
		restoreCartDialog: {
			get() {
				return this.$store.state.restoreCartDialog;
			},
			set(value) {
				this.$store.state.restoreCartDialog = value;
			}
		},
		subTotal() {
			let subTotal = 0;
			this.cartProducts.forEach(element => {
				subTotal += element.qty * parseInt(element.price.amount);
        })
            return subTotal;
		},
		tax() {
			return this.subTotal * 0.24;
		},
		totalDiscount() {
			return 0;
		},
		total() {
			return this.subTotal + this.tax - this.totalDiscount;
		},
		totalCartProducts() {
			return _.size(this.cartProducts) ? false : true;
		},
		cartsOnHold() {
			return this.$store.state.cartsOnHold;
		},
		cartsOnHoldSize() {
			return _.size(this.cartsOnHold);
		},
		cartProducts: {
			get() {
				return this.$store.state.cartProducts;
			},
			set(value) {
				this.$store.state.cartProducts = value;
			}
		}
	},

	methods: {
		decreaseQty(cartProduct) {
			this.$store.commit("decreaseCartProductQty", cartProduct);
		},
		increaseQty(cartProduct) {
			this.$store.commit("increaseCartProductQty", cartProduct);
		},
		removeItem(cartProduct) {
			this.cartProducts.splice(cartProduct, 1);
		},
		removeAll(cartProducts) {
			confirm("Are you sure you want to delete the cart?") &&
				this.cartProducts.splice(0);
		},
		addAll(cart) {
			this.cartProducts.splice(0);
			this.cartProducts = cart;
		},

		showRestoredCarts() {
            this.cartlist.forEach(cartProducts = > console.log(cartProducts);
        )
        },
		checkout() {
			this.checkoutDialog = true;
			console.log("---- CHECKOUT! ----");
			console.log(this.cartProducts);
		},
		holdCart() {
			let payload = {
				model: "carts",
                data: {
                    customer_id: 1,
                    cart: this.cartProducts
                }
			};
			this.$store.dispatch("create", payload);
        },
        submitCart() {
            let payload = {
                model: "orders",
                data: {
                    customer_id: 1,
                    items: this.cartProducts
                }
            };
            this.$store.dispatch("create", payload);
        }
	}
};
</script>