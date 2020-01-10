<template>
	<div class="d-flex" :key="$props.product_index">
		<v-col cols="6" class="pa-0 pr-2">
			<v-select
				v-if="editable"
				v-model="discountType"
				:items="discountTypes"
				label="Discount"
				item-text="label"
				item-value="value"
			></v-select>
			<v-text-field v-else-if="!editable" :value="discountType" label="Discount" disabled></v-text-field>
		</v-col>

		<v-col cols="6" class="pa-0 pl-2" v-if="discountType && discountType !== 'None'">
			<ValidationObserver v-slot="{ valid, invalid }" ref="checkoutObs">
				<ValidationProvider
					:rules="'between:0,' + max.toFixed(2)"
					v-slot="{ invalid }"
					name="Discount amount"
				>
					<v-text-field
						v-model="discountAmount"
						type="number"
						label="Amount"
						:min="0"
						:error-messages="invalid ? 'Invalid amount' : undefined"
						:success="valid"
						:disabled="!editable"
					></v-text-field>
				</ValidationProvider>
			</ValidationObserver>
		</v-col>
	</div>
</template>

<script>
import { mapState } from "vuex";
import { mapGetters } from "vuex";

export default {
	props: {
		product_price: Number,
		product_index: Number,
		editable: Boolean
	},
	watch: {
		product_price() {
			this.setDiscount();
		}
	},
	methods: {
		setDiscount(value = 0) {
			if (this.$props.product_index === -1) {
				Vue.set(
					this.cart,
					"discount_amount",
					value || this.cart.discount_amount
				);
			} else {
				Vue.set(
					this.product,
					"discount_amount",
					value || this.product.discount_amount
				);
			}
		}
	},
	computed: {
		...mapState("cart", ["discountTypes"]),
		cart: {
			get() {
				return this.$store.state.cart;
			},
			set(value) {
				this.$store.state.cart = value;
			}
		},
		product: {
			get() {
				return this.cart.products[this.$props.product_index];
			},
			set(value) {
				this.cart.products[this.$props.product_index] = value;
			}
		},
		max() {
			switch (this.discountType) {
				case "Flat":
					if (this.$props.product_index === -1) {
						let max =
							parseFloat(this.cart.cart_price) +
							parseFloat(this.cart.discount_amount);

						return max - 0.01;
					} else {
						return parseFloat(this.$props.product_price);
					}
				case "Percentage":
					return 99;
				default:
					return 0;
			}
		},
		discountType: {
			get() {
				if (this.$props.product_index === -1) {
					return this.cart.discount_type;
				} else {
					return this.product.discount_type;
				}
			},
			set(value) {
				if (this.$props.product_index === -1) {
					Vue.set(this.cart, "discount_type", value);
				} else {
					Vue.set(this.product, "discount_type", value);
				}

				this.setDiscount();
			}
		},
		discountAmount: {
			get() {
				if (this.$props.product_index === -1) {
					return this.cart.discount_amount;
				} else {
					return this.product.discount_amount;
				}
			},
			set(value) {
				this.setDiscount(value);
			}
		}
	}
};
</script>
