<template>
	<v-sheet class="pa-3 d-flex flex-column" style="height:100%">
		<div class="d-flex align-center justify-center">
			<v-icon>shopping_cart</v-icon>
			<h5 class="text-center title-2 ml-2">Shopping cart</h5>
		</div>
		<div class="d-flex">
			<v-autocomplete
				prepend-icon="account_circle"
				v-model="orderCustomer"
				clearable
				dense
				:items="customerList"
				:item-text="getCustomerFullname"
				item-value="id"
				return-object
				label="Select customer"
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
								v-if="cartProduct.discount_type && cartProduct.discount_type != 'None'"
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

			<div class="d-flex justify-space-between pa-2">
				<span>Sub total</span>
				<span>$ {{ subTotal }}</span>
			</div>

			<v-divider />

			<div class="d-flex justify-space-between pa-2 bb-1">
				<span>Tax</span>
				<span>$ {{ tax }}</span>
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
			<v-btn
				icon
				@click="restoreCartDialog = true"
				:disabled="cartsOnHoldSize ? false : true"
				class="flex-grow-1"
				tile
			>
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
			<v-btn
				icon
				@click.stop="removeAll(cartProducts)"
				:disabled="totalCartProducts"
				class="flex-grow-1"
				tile
			>
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
			isEditing: false,
			discountTypes: [
				{
					label: "None",
					value: "None"
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
	mounted() {
		this.getAllCustomers();
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
		},
		customerList: {
			get() {
				return this.$store.state.customerList;
			},
			set(value) {
				this.$store.state.customerList = value;
			}
		},
		orderCustomer: {
			get() {
				return this.$store.state.orderCustomer;
			},
			set(value) {
				this.$store.state.orderCustomer = value;
			}
		}
	},

	methods: {
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
		removeAll(cartProducts) {
			confirm("Are you sure you want to delete the cart?") &&
				this.cartProducts.splice(0);
		},
		addAll(cart) {
			this.cartProducts.splice(0);
			this.cartProducts = cart;
		},

		showRestoredCarts() {
			this.cartlist.forEach(cartProducts => console.log(cartProducts));
		},
		checkout() {
			console.log(this.keyword);
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
		getAllCustomers() {
			let payload = {
				model: "customers",
				mutation: "setCustomerList"
			};
			this.$store
				.dispatch("getAll", payload)
				.then(result => {})
				.catch(error => {
					console.log(error);
				});
		},
		searchCustomer() {
			if (this.keyword.length > 0) {
				let payload = {
					model: "customers",
					mutation: "setCustomerList",
					keyword: this.keyword
				};

				this.$store
					.dispatch("search", payload)
					.then(result => {})
					.catch(error => {});
			}
		}
	}
};
</script>