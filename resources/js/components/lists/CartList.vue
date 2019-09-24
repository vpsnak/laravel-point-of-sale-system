<template>
	<v-sheet class="pa-3 d-flex flex-column" style="height:100%">
		<div class="d-flex align-center justify-space-between">
			<div class="d-flex">
				<v-icon>shopping_cart</v-icon>
				<h5 class="text-center title-2 ml-2">Shopping cart</h5>
			</div>
			<v-switch label="Retail" v-model="retail"></v-switch>
		</div>
		<div class="d-flex">
			<v-autocomplete
				v-model="cartCustomer"
				clearable
				dense
				:items="customers"
				:loading="isLoading"
				:search-input.sync="search"
				color="white"
				hide-no-data
				hide-selected
				:item-text="getCustomerFullname"
				label="Select customer"
				placeholder="Start typing to Search"
				prepend-icon="mdi-database-search"
				return-object
			></v-autocomplete>
		</div>
		<div class="d-flex flex-grow-1" style="max-height: 44vh; overflow-y: auto">
			<v-expansion-panels class="d-block" accordion>
				<v-expansion-panel v-for="cartProduct in cartProducts" :key="cartProduct.id">
					<v-expansion-panel-header class="pa-3" ripple>
						<v-row no-gutters>
							<v-col cols="6" class="d-flex flex-column">
								<span class="subtitle-2">{{ cartProduct.name }}</span>
								<span class="body-2">$ {{ cartProduct.qty * cartProduct.price.amount }}</span>
							</v-col>
							<v-col cols="6" class="d-flex align-center justify-end">
								<v-btn icon @click.stop="decreaseQty(cartProduct)">
									<v-icon color="grey lighten-1">remove</v-icon>
								</v-btn>
								<v-chip filter @click.stop>
									<span>{{ cartProduct.qty }}</span>
								</v-chip>
								<v-btn icon @click.stop="increaseQty(cartProduct)">
									<v-icon color="grey lighten-1">add</v-icon>
								</v-btn>
								<v-btn icon @click.stop="removeItem(cartProduct)" class="mx-1">
									<v-icon color="grey lighten-1">delete</v-icon>
								</v-btn>
							</v-col>
						</v-row>
					</v-expansion-panel-header>
					<v-expansion-panel-content class="pa-0">
						<v-divider />
						<v-row>
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
								v-if="cartProduct.discount_type && cartProduct.discount_type !== 'none'"
							>
								<v-text-field type="number" label="Amount" min="1" v-model="cartProduct.discount_amount"></v-text-field>
							</v-col>
						</v-row>
						<v-row>
							<v-col cols="12">
								<v-textarea
									v-model="cartProduct.notes"
									rows="3"
									label="Notes"
									:hint="'For product: ' + cartProduct.name"
									counter
									no-resize
								></v-textarea>
							</v-col>
						</v-row>
					</v-expansion-panel-content>
				</v-expansion-panel>
			</v-expansion-panels>
		</div>

		<div class="d-flex flex-column">
			<v-divider />
			<div class="d-flex justify-space-between align-center">
				<v-col cols="3" class="px-2 py-0">
					<v-label>Cart discount</v-label>
				</v-col>
				<v-col cols="6" class="px-2 py-0">
					<v-select
						v-model="cartDiscount.type"
						:items="discountTypes"
						label="Discount"
						item-text="label"
						item-value="value"
					></v-select>
				</v-col>
				<v-col cols="3" class="px-2 py-0">
					<v-text-field
						v-if="cartDiscount.type && cartDiscount.type !== 'none'"
						v-model="cartDiscount.amount"
						type="number"
						label="Amount"
						min="1"
					></v-text-field>
				</v-col>
			</div>
			<v-divider />

			<div class="d-flex justify-space-between pa-2">
				<span>Sub total</span>
				<span>$ {{ subTotal }}</span>
			</div>

			<v-divider />

			<div class="d-flex justify-space-between pa-2 bb-1">
				<span>Tax</span>
				<span>$ {{ taxes }}</span>
			</div>

			<v-divider />

			<div class="d-flex justify-space-between pa-2">
				<span>Total discount</span>
				<span>$ {{ totalDiscount }}</span>
			</div>

			<v-divider />

			<div class="d-flex justify-space-between pa-2">
				<span>Total</span>
				<span>$ {{ total }}</span>
			</div>

			<v-divider />

			<v-btn block class="my-2" @click="checkout" :disabled="totalCartProducts">Checkout</v-btn>

			<v-divider />
		</div>
		<div class="d-flex align-center justify-center pa-2">
			<v-btn icon @click="showRestoreOnHoldCartDialog" class="flex-grow-1" tile>
				<v-icon>fa-recycle</v-icon>
				<v-badge overlap color="purple" style="position: absolute; top: 0;right:38%;">
					<template v-slot:badge>
						<span>{{cartsOnHoldSize}}</span>
					</template>
				</v-badge>
			</v-btn>
			<v-btn icon :disabled="totalCartProducts" @click="holdCart" class="flex-grow-1" tile>
				<v-icon>pause</v-icon>
			</v-btn>
			<v-btn icon @click.stop="emptyCart(true)" :disabled="totalCartProducts" class="flex-grow-1" tile>
				<v-icon>delete</v-icon>
			</v-btn>
		</div>
		<v-dialog v-model="checkoutDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
			<checkoutWizard />
		</v-dialog>
		<v-dialog v-model="restoreCartDialog" width="500">
			<restoreCartDialog />
		</v-dialog>
	</v-sheet>
</template>

<script>
export default {
	data() {
		return {
			model: "customers",
			search: null,
			isLoading: false,
			customers: undefined,
			cartDiscount: {
				type: undefined,
				amount: undefined
			},
			discountTypes: [
				{
					label: "None",
					value: "none"
				},
				{
					label: "Flat",
					value: "flat"
				},
				{
					label: "Percentage",
					value: "percentage"
				}
			]
		};
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
		subTotal() {
			let subTotal = 0;

			this.cartProducts.forEach(element => {
				subTotal += element.qty * parseInt(element.price.amount);
			});

			return subTotal;
		},
		tax() {
			return this.subTotal * 0.24;
		},
		taxes() {
			return this.tax.toFixed(2);
		},
		totalDiscount() {
			let totalFlatDiscount = 0;
			let totalPercentageDiscount = 0;
			this.cartProducts.forEach(cartProduct => {
				// cart items discount calculation
				if (
					_.has(cartProduct, "discount_type") &&
					_.has(cartProduct, "discount_amount")
				) {
					if (
						cartProduct.discount_type === "flat" &&
						cartProduct.discount_amount > 0
					) {
						totalFlatDiscount += parseInt(cartProduct.discount_amount);
					} else if (
						cartProduct.discount_type === "percentage" &&
						cartProduct.discount_amount > 0
					) {
						totalPercentageDiscount +=
							(cartProduct.price.amount * cartProduct.discount_amount) / 100;
					}
				}
			});

			// total cart discount calculation

			if (this.cartDiscount.type === "flat" && this.cartDiscount.amount > 0) {
				totalFlatDiscount += parseInt(this.cartDiscount.amount);
			} else if (
				this.cartDiscount.type === "percentage" &&
				this.cartDiscount.amount > 0
			) {
				totalPercentageDiscount +=
					(this.subTotal * this.cartDiscount.amount) / 100;
			}

			return totalFlatDiscount + totalPercentageDiscount;
		},
		total() {
			return (this.subTotal + this.tax - this.totalDiscount).toFixed(2);
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
		},
		customerList: {
			get() {
				return this.$store.state.customerList;
			},
			set(value) {
				this.$store.state.customerList = value;
			}
		},
		cartCustomer: {
			get() {
				return this.$store.state.cartCustomer;
			},
			set(value) {
				this.$store.state.cartCustomer = value;
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
		getCustomerFullname(item) {
			return item.first_name + " " + item.last_name;
		},

		decreaseQty(cartProduct) {
			this.$store.commit("decreaseCartProductQty", cartProduct);
		},
		increaseQty(cartProduct) {
			this.$store.commit("increaseCartProductQty", cartProduct);
		},
		removeItem(cartProduct) {
			this.cartProducts.splice(cartProduct, 1);
		},
		emptyCart(showPrompt) {
			if (showPrompt) {
				confirm("Are you sure you want to delete the cart?") &&
					this.cartProducts.splice(0);
			} else {
				this.cartProducts.splice(0);
			}
		},
		addAll(cart) {
			this.cartProducts.splice(0);
			this.cartProducts = cart;
		},
		checkout() {
			this.checkoutDialog = true;
			console.log("---- CHECKOUT! ----");
		},
		holdCart() {
			let payload = {
				model: "carts",
				data: {
					customer_id: 1,
					cart: this.cartProducts
				}
			};
			this.$store.dispatch("create", payload).then(this.emptyCart(false));
		},
		submitCart() {
			let payload = {
				model: "orders",
				data: {
					customer_id: 1,
					user_id: 1,
					discount: this.totalDiscount,
					// discount_type: 'none',
					shipping_type: "shipping",
					shipping_cost: 0,
					tax: this.tax,
					subtotal: this.subTotal,
					note: "",
					items: this.cartProducts
				}
			};
			this.$store.dispatch("create", payload);
		},
		searchCustomer(keyword) {
			this.isLoading = true;
			const payload = {
				model: "customers",
				mutation: "setCustomerList",
				keyword: keyword
			};

			this.$store
				.dispatch("search", payload)
				.then(result => {
					this.customers = result;
				})
				.catch(error => {
					console.log(error);
				})
				.finally(() => (this.isLoading = false));
		}
	},
	watch: {
		search(keyword) {
			if (keyword) {
				if (keyword.length > 4) {
					if (this.isLoading) {
						return;
					} else {
						this.searchCustomer(keyword);
						return;
					}
				}
			}
			this.cartCustomer = undefined;
			return;
		}
	}
};
</script>