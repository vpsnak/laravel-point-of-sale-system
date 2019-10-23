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
				get() {
					return this.$store.state.cart.shipping;
				},
				set(value) {
					this.$store.state.cart.shipping = value;
				}
			}
		};
	},
	props: {
		currentStep: Object
	},

	computed: {
		showNext() {
			switch (this.shipping.method) {
				case undefined:
				case "retail":
					return true;
				case "pickup":
					if (this.shipping.date && this.shipping.time) {
						return true;
					} else {
						return false;
					}
				case "delivery":
					if (
						this.shipping.date &&
						this.shipping.time &&
						this.shipping.address
					) {
						return true;
					} else {
						return false;
					}
				default:
					return false;
			}
		}
	},

	methods: {
		completeStep() {
			this.$store.dispatch("cart/completeStep");
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		},
		setShipping(value) {
			this.shipping = value;
		}
	}
};
</script>
