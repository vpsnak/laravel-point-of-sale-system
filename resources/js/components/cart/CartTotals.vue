<template>
	<div class="d-flex flex-column">
		<div class="d-flex justify-space-between pa-2">
			<span>Sub total w/ discount</span>
			<span>$ {{ subTotalwDiscount.toFixed(2) }}</span>
		</div>

		<v-divider v-if="totalDiscount" />

		<div class="d-flex justify-space-between pa-2" v-if="totalDiscount">
			<span>Total discount</span>
			<span>$ {{ totalDiscount.toFixed(2) }}</span>
		</div>

		<v-divider />

		<div class="d-flex justify-space-between pa-2" v-if="shippingCost">
			<span>Shipping cost</span>
			<span>$ {{ shippingCost.toFixed(2) }}</span>
		</div>

		<v-divider />

		<div class="d-flex justify-space-between pa-2 bb-1">
			<span>Tax</span>
			<span>$ {{ tax.toFixed(2) }}</span>
		</div>

		<v-divider />

		<div class="d-flex justify-space-between pa-2">
			<span>Total</span>
			<span>$ {{ total.toFixed(2) }}</span>
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
		customer() {
			return this.$store.state.cart.customer;
		},
		shippingCost() {
			if (this.$props.order) {
				return parseFloat(this.$props.order.shipping_cost);
			} else if (this.$store.state.cart.shipping.cost) {
				return parseFloat(this.$store.state.cart.shipping.cost);
			} else {
				return 0;
			}
		},
		subTotalwDiscount() {
			let subtotal = 0;
			if (this.$props.order) {
				this.$props.order.items.forEach(item => {
					subtotal += this.calcDiscount(
						item.price * item.qty,
						item.discount_type,
						item.discount_amount
					);
				});

				subtotal -=
					subtotal -
					this.calcDiscount(
						subtotal,
						this.$props.order.discount_type,
						this.$props.order.discount_amount
					);
			} else {
				this.$props.products.forEach(product => {
					subtotal += this.calcDiscount(
						product.price.amount * product.qty,
						product.discount_type,
						product.discount_amount
					);
				});

				subtotal -=
					subtotal -
					this.calcDiscount(
						subtotal,
						this.$props.cart.discount_type,
						this.$props.cart.discount_amount
					);
			}
			return subtotal;
		},
		tax() {
			if (this.customer) {
				if (this.customer.no_tax) {
					return 0;
				}
			}

			if (this.$props.order) {
				return parseFloat(
					this.$props.order.total - this.$props.order.total_without_tax
				);
			} else {
				return parseFloat(
					((this.subTotalwDiscount + this.shippingCost) *
						parseFloat(this.$store.state.store.tax.percentage)) /
						100
				);
			}
		},
		totalDiscount() {
			let subtotalNoDiscount = 0;

			if (this.$props.order) {
				this.$props.order.items.forEach(item => {
					subtotalNoDiscount += item.price * item.qty;
				});
			} else {
				this.$props.products.forEach(product => {
					subtotalNoDiscount += product.price.amount * product.qty;
				});
			}

			return subtotalNoDiscount - this.subTotalwDiscount;
		},
		total() {
			if (this.$props.order) {
				return this.$props.order.total;
			} else {
				Vue.set(
					this.$store.state.cart,
					"cart_price",
					this.subTotalwDiscount + this.tax + this.shippingCost
				);

				if (parseFloat(this.$store.state.cart.cart_price) > 0) {
					this.$store.state.cart.isValidCheckout = true;
				} else {
					this.$store.state.cart.isValidCheckout = false;
				}

				return this.subTotalwDiscount + this.tax + this.shippingCost;
			}
		}
	},
	methods: {
		calcDiscount(price, type, amount) {
			if (type && amount) {
				switch (_.lowerCase(type)) {
					case "flat":
						return parseFloat(price).toFixed(2) - parseFloat(amount).toFixed(2);
					case "percentage":
						return (
							parseFloat(price) -
							(parseFloat(price) * amount) / 100
						).toFixed(2);
					default:
						return price;
				}
			}
			return price;
		}
	}
};
</script>
