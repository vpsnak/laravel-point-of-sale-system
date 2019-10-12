<template>
	<div>
		completion
		<v-card-actions class="justify-center">
			<v-btn color="success" @click="close">Close</v-btn>
		</v-card-actions>
	</div>
</template>

<script>
import { mapActions } from "vuex";
export default {
	props: {
		currentStep: Object
	},

	computed: {
		order: {
			get() {
				return this.$store.state.cart.order;
			},
			set(value) {
				this.$store.state.cart.order = value;
			}
		}
	},

	methods: {
		close() {
			this.$store.commit("cart/resetState");
			this.$store.state.checkoutDialog = false;
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		},

		...mapActions(["create"])
	}
};
</script>