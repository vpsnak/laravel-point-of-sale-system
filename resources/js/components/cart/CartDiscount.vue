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
				<input type="hidden" :value="($store.state.cart.isValidCheckout = valid)" />
				<ValidationProvider
					:rules="'between:0,' + max.toFixed(2)"
					v-slot="{ invalid }"
					name="Discount amount"
				>
					<v-text-field
						@input="alert()"
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
		product_price: String,
		product_index: Number,
		editable: Boolean
	},
	methods: {
		alert() {
			console.log(this.max);
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
				console.log(this.cart.products[this.$props.product_index]);
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
						return (
							parseFloat(this.cart.cart_price) - parseFloat(this.discountAmount)
						);
					} else {
						return parseFloat(this.$props.product_price);
					}
				case "Percentage":
					return 100;
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
					this.cart.discount_type = value;
				} else {
					Vue.set(this.product, "discount_type", value);
				}
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
				if (this.$props.product_index === -1) {
					this.cart.discount_amount = value;
				} else {
					Vue.set(this.product, "discount_amount", value);
				}
			}
		}
	}
};
</script>
