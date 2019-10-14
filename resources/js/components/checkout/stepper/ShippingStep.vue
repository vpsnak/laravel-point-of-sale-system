<template>
	<div>
		<shipping @shipping="setShipping" />
		<v-card-actions>
			<span class="title" v-if="shipping.cost">Shipping cost: $ {{ shipping.cost }}</span>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" v-if="showNext()" @click="completeStep()">
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
			shipping: {}
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
			if (this.shipping.method === "pickup") {
				return true;
			} else {
				if (this.shipping.date && this.shipping.time) {
					return true;
				}
			}
		},
		setShipping(value) {
			this.shipping = value;
		}
	}
};
</script>
