<template>
	<div class="d-flex flex-column">

		<div class="d-flex justify-space-between pa-2">
			<span>Sub total</span>
			<span>$ {{ subTotal }}</span>
		</div>

		<v-divider />

		<div
			v-if="taxes"
			class="d-flex justify-space-between pa-2 bb-1"
		>
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
	import { parse } from "path";
	export default {
		props: {
			cartProducts: Array,
			taxes: Boolean | undefined
		},

		computed: {
			subTotal() {
				let subTotal = 0;

				this.$props.cartProducts.forEach(element => {
					subTotal += element.qty * parseInt(element.price.amount);
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
				this.$props.cartProducts.forEach(cartProduct => {
					// cart items discount calculation
					if (
						_.has(cartProduct, "discount_type") &&
						_.has(cartProduct, "discount_amount")
					) {
						if (
							cartProduct.discount_type === "flat" &&
							cartProduct.discount_amount > 0
						) {
							totalFlatDiscount += parseInt(
								cartProduct.discount_amount
							);
						} else if (
							cartProduct.discount_type === "percentage" &&
							cartProduct.discount_amount > 0
						) {
							totalPercentageDiscount +=
								(cartProduct.price.amount *
									cartProduct.discount_amount) /
								100;
						}
					}
				});

				// total cart discount calculation

				if (
					this.$props.cartProducts.discount_type === "flat" &&
					this.$props.cartProducts.discount_amount > 0
				) {
					totalFlatDiscount += parseInt(
						this.$props.cartProducts.discount_amount
					);
				} else if (
					this.$props.cartProducts.discount_type === "percentage" &&
					this.$props.cartProducts.discount_amount > 0
				) {
					totalPercentageDiscount +=
						(this.subTotal * this.$props.cartProducts.discount_amount) /
						100;
				}

				return totalFlatDiscount + totalPercentageDiscount;
			},
			total() {
				if (this.$props.taxes) {
					return (this.subTotal + this.tax - this.totalDiscount).toFixed(
						2
					);
				} else {
					return (this.subTotal - this.totalDiscount).toFixed(2);
				}
			}
		}
	};
</script>