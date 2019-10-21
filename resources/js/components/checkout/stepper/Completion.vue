<template>
	<div>
		<div v-if="change" class="my-5 d-flex align-center justify-center">
			<h3>
				Change:
				<span class="amber--text" v-text="'$ ' + change.toFixed(2)" />
			</h3>
		</div>
		<v-card class="mx-auto" max-width="300">
			<v-list>
				<v-subheader>
					<v-icon left>mdi-receipt</v-icon>
					<h3>Receipt for order #{{ orderId }}</h3>
				</v-subheader>
				<v-divider></v-divider>
				<v-list-item @click>
					<v-list-item-icon>
						<v-icon>mdi-printer</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>Print</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item :disabled="! customerEmail" @click>
					<v-list-item-icon>
						<v-icon>mdi-email-newsletter</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>Email</v-list-item-title>
						<v-list-item-subtitle v-text="customerEmail"></v-list-item-subtitle>
					</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-card>

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