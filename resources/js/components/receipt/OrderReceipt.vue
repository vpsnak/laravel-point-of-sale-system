<template>
	<v-card class="mx-auto" max-width="400">
		<v-list>
			<v-subheader>
				<v-icon left>mdi-receipt</v-icon>
				<h3>
					Receipt for order
					<i class="cyan--text">#{{ orderId }}</i>
				</h3>
			</v-subheader>
			<v-divider></v-divider>
			<v-list-item @click="printReceipt">
				<v-list-item-icon>
					<v-icon>mdi-printer</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>Print</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-list-item>
				<v-list-item-content>
					<v-text-field
						v-model="customerEmail"
						label="Send via e-mail"
						prepend-inner-icon="mdi-email-newsletter"
						append-outer-icon="mdi-send"
						@click:append-outer="mailReceipt"
						:loading="loading"
						:disabled="loading"
					></v-text-field>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			loading: false,
			customer_email: ""
		};
	},
	computed: {
		order() {
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
		customerEmail: {
			get() {
				if (this.customer_email) {
					return this.customer_email;
				} else if (this.customer) {
					return this.customer.email ? this.customer.email : undefined;
				} else {
					return undefined;
				}
			},
			set(value) {
				this.customer_email = value;
			}
		}
	},
	methods: {
		printReceipt() {},
		mailReceipt() {
			this.loading = true;

			if (this.customer && this.customer.email !== this.customerEmail) {
				this.$store.dispatch("cart/saveGuestEmail", {
					email: this.customerEmail
				});
			}

			this.$store
				.dispatch("cart/mailReceipt", {
					email: this.customerEmail
				})
				.finally(() => {
					this.loading = false;
				});
		}
	}
};
</script>