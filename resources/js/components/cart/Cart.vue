<template>
	<v-card class="pa-3 d-flex flex-column" style="max-height: 90vh;">
		<div class="d-flex">
			<div class="d-flex align-center justify-center">
				<v-icon class="pr-2">{{ icon }}</v-icon>
				<h4 class="title-2">{{ title }}</h4>
			</div>

			<v-spacer></v-spacer>

			<div class="pt-5 pr-2">
				<v-label>Delivery</v-label>
			</div>
			<v-switch class="my-0 pt-5" v-model="toggleRetail" label="Retail" :disabled="!editable"></v-switch>
		</div>
		<v-divider />
		<v-container grid-list-md text-xs-center>
			<customerSearch :editable="editable" :keywordLength="1" class="my-3"></customerSearch>
		</v-container>

		<cartProducts :products="items ? items : products" :editable="editable"></cartProducts>

		<v-divider />

		<div class="d-flex flex-column">
			<v-row class="d-flex justify-space-between align-center">
				<v-col cols="4" class="px-5 py-0">
					<v-label>Cart discount</v-label>
				</v-col>
				<v-col cols="8" class="px-2 py-0">
					<cartDiscount :model="products" :editable="editable"></cartDiscount>
				</v-col>
			</v-row>

			<v-divider />

			<cartTotals :products="items ? items : products" :cart="cartDiscount" :order="order" />

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
		order: Array | null,
		items: Array | null,
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
		cartDiscount() {
			let discount = {
				discount_type: this.$store.state.cart.discount_type,
				discount_amount: this.$store.state.cart.discount_amount
			};

			return discount;
		},
		products: {
			get() {
				return this.$store.state.cart.products;
			},
			set(value) {
				this.$store.state.cart.products = value;
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