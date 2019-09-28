<template>
	<v-sheet class="pa-3 d-flex flex-column" style="height:100%">
		<div class="d-flex align-center justify-space-between">
			<div class="d-flex align-center">
				<v-icon>{{ icon }}</v-icon>
				<h5 class="title-2 ml-2">{{ title }}</h5>
			</div>

			<div v-if="toggles" class="d-flex align-center justify-space-between">
				<v-switch label="Retail" v-model="retail" class="px-2"></v-switch>
				<v-switch label="Taxes" v-model="taxes" class="px-2"></v-switch>
			</div>
		</div>

		<v-divider />

		<customerSearch :editable="editable" :keywordLength="1"></customerSearch>

		<cartProducts :products="products" :editable="editable"></cartProducts>

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

			<cartTotals :products="products" :taxes="taxes" :cart="cartDiscount" />

			<div v-if="actions">
				<cartActions :disabled="totalProducts" />
			</div>
		</div>
	</v-sheet>
</template>

<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	props: {
		title: String,
		icon: String | undefined,
		editable: Boolean | undefined,
		actions: Boolean | undefined,
		toggles: Boolean | undefined
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
		taxes: {
			get() {
				return this.$store.state.cart.taxes;
			},
			set() {
				this.$store.commit("cart/toggleTaxes");
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