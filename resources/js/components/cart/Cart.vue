<template>
	<v-card class="pa-3 d-flex flex-column" style="max-height: 90vh;">
		<div class="d-flex align-center justify-center">
			<v-icon class="pr-2">{{ icon }}</v-icon>
			<h4 class="title-2">{{ title }}</h4>
			<v-spacer></v-spacer>
			<v-switch @change="saveOneClick" label="Retail" :disabled="!editable"></v-switch>
		</div>
		<v-divider />
		<v-container grid-list-md text-xs-center>
			<v-layout row wrap>
				<v-flex xs11>
					<customerSearch :editable="editable" :keywordLength="1" class="my-3"></customerSearch>
				</v-flex>
				<v-flex xs1>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn @click="showCreateDialog = true" class="my-3" v-on="on" icon :disabled="!editable">
								<v-icon>mdi-plus</v-icon>
							</v-btn>
						</template>
						<span>Add a customer</span>
					</v-tooltip>
				</v-flex>
			</v-layout>
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
		<interactiveDialog
			v-if="showCreateDialog"
			:show="showCreateDialog"
			:model="{}"
			component="customerForm"
			title="Add a Customer"
			@action="result"
			cancelBtnTxt="Close"
			persistent
			titleCloseBtn
		></interactiveDialog>
	</v-card>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			showCreateDialog: false
		};
	},
	props: {
		order: Array | null,
		items: Array | null,
		title: String,
		icon: String | null,
		editable: Boolean | null,
		actions: Boolean | null
	},
	computed: {
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
		saveOneClick(e) {
			e
				? ((this.$store.state.cart.checkoutSteps[0].completed = true),
				  (this.$store.state.cart.currentCheckoutStep = 2))
				: ((this.$store.state.cart.checkoutSteps[0].completed = false),
				  (this.$store.state.cart.currentCheckoutStep = 1));
		},
		addAll(cart) {
			this.products.splice(0);
			this.products = cart;
		},
		result(event) {
			this.showCreateDialog = false;
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