<template>
	<div class="d-flex flex-column">
		<div class="d-flex justify-space-between pa-2">
			<span>Sub total w/ discount</span>
			<span>$ {{ subTotalwDiscount }}</span>
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
	</div>
</template>

<script>
export default {
	props: {
		order: Array | undefined,
		cart: Object | undefined,
		products: Array | undefined
	},

	computed: {
		subTotalwDiscount() {
			return (this.subTotal - this.totalDiscount).toFixed(2);
		},

		subTotal() {
			let subTotal = 0.0;

			this.$props.products.forEach(product => {
				subTotal +=
					parseInt(product.qty) *
					parseFloat(
						product.price.amount ? product.price.amount : product.price
					);
			});

			return subTotal;
		},
		tax() {
			if (this.$props.order) {
				return parseFloat(
					this.$props.order.total - this.$props.order.subtotal
				).toFixed(2);
			} else {
				// @TODO change flat discount
				return parseFloat(
					(parseFloat(this.subTotalwDiscount) * 0.24).toFixed(2)
				);
			}
		},
		totalDiscount() {
			let totalFlatDiscount = 0.0;
			let totalPercentageDiscount = 0.0;
			this.$props.products.forEach(product => {
				// cart items discount calculation
				if (
					_.has(product, "discount_type") &&
					_.has(product, "discount_amount")
				) {
					if (product.discount_type === "Flat" && product.discount_amount > 0) {
						totalFlatDiscount =
							parseFloat(totalFlatDiscount) +
							parseFloat(product.discount_amount);
					} else if (
						product.discount_type === "Percentage" &&
						product.discount_amount > 0
					) {
						totalPercentageDiscount =
							parseFloat(totalPercentageDiscount) +
							(parseFloat(product.price.amount) *
								parseFloat(product.discount_amount)) /
								100;
					}
				}
			});

			// total cart discount calculation

			if (
				this.$props.cart.discount_type === "Flat" &&
				this.$props.cart.discount_amount > 0
			) {
				totalFlatDiscount =
					parseFloat(totalFlatDiscount) +
					parseFloat(this.$props.cart.discount_amount);
			} else if (
				this.$props.cart.discount_type === "Percentage" &&
				this.$props.cart.discount_amount > 0
			) {
				totalPercentageDiscount =
					parseFloat(totalPercentageDiscount) +
					(parseFloat(this.subTotal) *
						parseFloat(this.$props.cart.discount_amount)) /
						100;
			}

			return (
				parseFloat(totalFlatDiscount) + parseFloat(totalPercentageDiscount)
			).toFixed(2);
		},
		total() {
			return (
				parseFloat(this.subTotal) +
				parseFloat(this.tax) -
				parseFloat(this.totalDiscount)
			).toFixed(2);
		}
	}
};
</script>