<template>
	<div class="d-flex flex-column">
		<div class="d-flex justify-space-between pa-2">
			<span>Sub total w/ discount</span>
			<span>$ {{ subTotalwDiscount.toFixed(2) }}</span>
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
			let subtotal = 0;
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
			return subtotal;
		},
		tax() {
			if (this.$props.order) {
				return parseFloat(
					this.$props.order.total - this.$props.order.subtotal
				).toFixed(2);
			} else {
				return parseFloat(
					(
						(this.subTotalwDiscount *
							parseFloat(this.$store.state.store.tax.percentage)) /
						100
					).toFixed(2)
				);
			}
		},
		totalDiscount() {
			let subtotalNoDiscount = 0;
			this.$props.products.forEach(product => {
				subtotalNoDiscount += product.price.amount * product.qty;
			});

			return subtotalNoDiscount - this.subTotalwDiscount;
		},
		total() {
			return (this.subTotalwDiscount + parseFloat(this.tax)).toFixed(2);
		}
	},
	methods: {
		calcDiscount(price, type, amount) {
			if (type && amount) {
				switch (_.lowerCase(type)) {
					case "flat":
						return parseFloat(price) - parseFloat(amount);
						break;
					case "percentage":
						return parseFloat(price) - (parseFloat(price) * amount) / 100;
						break;
				}
			}
			return price;
		}
	}
};
</script>