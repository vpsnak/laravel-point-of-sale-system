<template>
	<v-card class="pa-3">
		<div class="d-flex">
			<div class="d-flex align-center justify-center">
				<v-icon class="pr-2">{{ icon }}</v-icon>
				<h4 class="title-2">{{ title }}</h4>
			</div>

			<v-spacer></v-spacer>

			<v-btn-toggle v-model="$store.state.cart.shipping.method" mandatory>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn color="primary" icon value="retail" v-on="on">
							<v-icon>mdi-cart-arrow-right</v-icon>
						</v-btn>
					</template>
					<span>Cash & Carry</span>
				</v-tooltip>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn color="red" icon value="pickup" :disabled="!$store.state.cart.customer" v-on="on">
							<v-icon>mdi-store-24-hour</v-icon>
						</v-btn>
					</template>
					<span>In store pickup</span>
				</v-tooltip>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn
							color="warning"
							icon
							value="delivery"
							:disabled="!$store.state.cart.customer"
							v-on="on"
						>
							<v-icon>fas fa-shipping-fast</v-icon>
						</v-btn>
					</template>
					<span>Delivery</span>
				</v-tooltip>
			</v-btn-toggle>
		</div>

		<v-divider class="py-1" />

		<customerSearch :editable="editable" :keywordLength="3" class="pa-3"></customerSearch>

		<v-divider class="py-1" />

		<cartProducts :editable="editable"></cartProducts>

		<v-divider />

		<div class="d-flex flex-column">
			<v-row class="d-flex justify-space-between align-center">
				<v-col cols="4" class="px-5 py-0">
					<v-label>Cart discount</v-label>
				</v-col>
				<v-col cols="8" class="px-2 py-0">
					<cartDiscount :product_index="-1" :editable="editable"></cartDiscount>
				</v-col>
			</v-row>

			<v-divider />

			<cartTotals />

			<div v-if="actions">
				<cartActions :disabled="totalProducts" />
			</div>
		</div>
	</v-card>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		title: String,
		icon: String | null,
		editable: Boolean | null,
		actions: Boolean | null
	},
	computed: {
		toggleRetail: {
			get() {
				return this.$store.state.cart.retail;
			},
			set(value) {
				this.$store.commit("cart/toggleRetail", value);
			}
		},

		cart: {
			get() {
				return this.$store.state.cart;
			},
			set(value) {
				this.$store.state.cart = value;
			}
		},

		order: {
			get() {
				return this.cart.order;
			},
			set(value) {
				this.cart.order = value;
			}
		},
		cartDiscount() {
			let discount = {
				discount_type: this.$store.state.cart.discount_type,
				discount_amount: this.$store.state.cart.discount_amount
			};

			return discount;
		},
		products: {
			get() {
				if (this.order) {
					return this.order.items;
				} else {
					return this.cart.products;
				}
			},
			set(value) {
				if (this.order) {
					this.order.items = value;
				} else {
					this.cart.products = value;
				}
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
		totalProducts() {
			return _.size(this.products) ? false : true;
		}
	},

	methods: {
		addAll(cart) {
			this.products.splice(0);
			this.products = cart;
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	}
};
</script>
