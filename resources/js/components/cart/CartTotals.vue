<template>
	<div class="d-flex flex-column">
		<div class="d-flex justify-space-between pa-2">
			<span>Sub total</span>
			<span>$ {{ subTotal }}</span>
		</div>

		<v-divider />

		<div v-if="taxes" class="d-flex justify-space-between pa-2 bb-1">
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
	</div>
</template>

<script>
export default {
	props: {
		cart: Object | undefined,
		products: Array | undefined,
		taxes: Boolean | undefined
	},

	computed: {
		subTotal() {
			let subTotal = 0;

			this.$props.products.forEach(product => {
				subTotal +=
					product.qty *
					parseInt(product.price.amount ? product.price.amount : product.price);
			});

			return subTotal;
		},
		tax() {
			// @TODO: pending configurable taxing
			return parseFloat((this.subTotal * 0.24).toFixed(2));
		},
		totalDiscount() {
			let totalFlatDiscount = 0;
			let totalPercentageDiscount = 0;
			this.$props.products.forEach(product => {
				// cart items discount calculation
				if (
					_.has(product, "discount_type") &&
					_.has(product, "discount_amount")
				) {
					if (product.discount_type === "Flat" && product.discount_amount > 0) {
						totalFlatDiscount += parseInt(product.discount_amount);
					} else if (
						product.discount_type === "Percentage" &&
						product.discount_amount > 0
					) {
						totalPercentageDiscount +=
							(product.price.amount * product.discount_amount) / 100;
					}
				}
			});

			// total cart discount calculation

			if (
				this.$props.cart.discount_type === "Flat" &&
				this.$props.cart.discount_amount > 0
			) {
				totalFlatDiscount += parseInt(this.$props.cart.discount_amount);
			} else if (
				this.$props.cart.discount_type === "Percentage" &&
				this.$props.cart.discount_amount > 0
			) {
				totalPercentageDiscount +=
					(this.subTotal * this.$props.cart.discount_amount) / 100;
			}

			return totalFlatDiscount + totalPercentageDiscount;
		},
		total() {
			if (this.$props.taxes) {
				return (this.subTotal + this.tax - this.totalDiscount).toFixed(2);
			} else {
				return (this.subTotal - this.totalDiscount).toFixed(2);
			}
		}
	}
};
</script>