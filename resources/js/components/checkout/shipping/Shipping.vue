<template>
	<div>
		<deliveryOption v-if="shipping.method === 'delivery'"></deliveryOption>
		<inStorePickupOption v-if="shipping.method === 'pickup'"></inStorePickupOption>
		<retailOption v-if="shipping.method === 'retail'"></retailOption>
	</div>
</template>

<script>
export default {
	computed: {
		shippingMethod() {
			return this.shipping.method;
		},
		shipping: {
			get() {
				return this.$store.state.cart.shipping;
			},
			set(value) {
				this.$store.state.cart.shipping = value;
			}
		}
	}
};
</script>
