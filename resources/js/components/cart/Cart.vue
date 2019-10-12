<template>
	<v-card class="pa-3 d-flex flex-column" style="max-height: 90vh;">
		<div class="d-flex align-center justify-space-between">
			<div :class="titleClass">
				<v-icon class="pr-2">{{ icon }}</v-icon>
				<h4 class="title-2">{{ title }}</h4>
			</div>

			<div v-if="toggles" class="d-flex align-center justify-space-between">
				<v-switch label="Retail" v-model="retail" class="px-2"></v-switch>
			</div>
		</div>

		<v-divider />

		<customerSearch :editable="editable" :keywordLength="1" class="my-3"></customerSearch>

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
		actions: Boolean | null,
		toggles: Boolean | null
	},
	computed: {
		titleClass() {
			return this.$props.toggles
				? "d-flex align-center pb-2"
				: "d-flex align-center";
		},
		retail: {
			get() {
				return this.$store.state.cart.retail;
			},
			set() {
				this.$store.commit("cart/toggleRetail");
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