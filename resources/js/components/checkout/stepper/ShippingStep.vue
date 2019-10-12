<template>
	<div>
		shipping here
		<v-card-actions>
			<v-btn color="secondary" @click="prevStep()">Back</v-btn>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" @click="completeStep()">Continue</v-btn>
		</v-card-actions>
	</div>
</template>

<script>
export default {
	props: {
		currentStep: Object
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
