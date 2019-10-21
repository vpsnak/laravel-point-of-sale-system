<template>
	<div>
		<shipping @shipping="setShipping" />
		<v-card-actions>
			<span class="title" v-if="shipping.cost">Shipping cost: $ {{ shipping.cost }}</span>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" v-if="showNext" @click="completeStep">
				Next
				<v-icon small right>mdi-chevron-right</v-icon>
			</v-btn>
		</v-card-actions>
	</div>
</template>

<script>
export default {
	data() {
		return {
			shipping: {
				method: "pickup",
				address: undefined
			}
		};
	},
	props: {
		currentStep: Object
	},

	methods: {
		completeStep() {
			this.$store.dispatch("cart/completeStep");
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		},
		showNext() {
			switch (this.shipping.method) {
				case "retail":
					return true;
					break;
				case "pickup":
					if (this.shipping.date && this.shipping.time) {
						return true;
					}
					break;
				case "delivery":
					if (
						this.shipping.date &&
						this.shipping.time &&
						this.shipping.address
					) {
						return true;
					}
					break;
				default:
					break;
			}
		},
		setShipping(value) {
			this.shipping = value;
		}
	}
};
</script>
