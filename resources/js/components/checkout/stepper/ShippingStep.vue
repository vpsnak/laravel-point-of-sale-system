<template>
	<div>
		<shipping />
		<v-row justify="center" align="center" class="my-3">
			<v-col :cols="3" align="start" v-if="shipping.cost">
				<span class="title">
					Shipping cost:
					<span class="amber--text">$ {{ shipping.cost }}</span>
				</span>
			</v-col>
			<v-col :cols="shipping.cost ? 3 : 6" justify="center" align="end">
				<v-btn color="primary" :disabled="!isValid" @click="completeStep">
					Next
					<v-icon small right>mdi-chevron-right</v-icon>
				</v-btn>
			</v-col>
		</v-row>
	</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		currentStep: Object
	},

	computed: {
		isValid() {
			return this.$store.state.cart.isValid;
		},
		shipping: {
			get() {
				return this.$store.state.cart.shipping;
			},
			set(value) {
				this.$store.state.cart.shipping = value;
			}
		}
	},

	methods: {
		completeStep() {
			this.$store.dispatch("cart/completeStep");
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		}
	}
};
</script>
