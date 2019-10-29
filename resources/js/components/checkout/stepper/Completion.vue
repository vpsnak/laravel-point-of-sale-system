<template>
	<div>
		<div v-if="change" class="my-5 d-flex align-center justify-center">
			<h3>
				Change:
				<span class="amber--text" v-text="'$ ' + change.toFixed(2)" />
			</h3>
		</div>

		<orderReceipt />

		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" @click="close">Close</v-btn>
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
		change() {
			if (this.order && this.order.remaining < 0) {
				return Math.abs(this.order.remaining);
			} else {
				return false;
			}
		},
		order() {
			console.log(this.$store.state.cart.order);
			return this.$store.state.cart.order;
		},
		orderId() {
			return this.order ? this.order.id : 0;
		},
		customer() {
			if (this.order) {
				if (this.order.customer) {
					return this.order.customer;
				}
			}
			return undefined;
		},
		customerEmail() {
			if (this.customer) {
				return this.customer.email ? this.customer.email : undefined;
			} else {
				return undefined;
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