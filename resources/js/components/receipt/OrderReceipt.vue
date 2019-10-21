<template>
	<v-card class="mx-auto" max-width="300">
		<v-list>
			<v-subheader>
				<v-icon left>mdi-receipt</v-icon>
				<h3>
					Receipt for order
					<span class="cyan--text">#{{ orderId }}</span>
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
			<v-list-item :disabled="! customerEmail" @click="mailReceipt">
				<v-list-item-icon>
					<v-icon>mdi-email-newsletter</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>Email</v-list-item-title>
					<v-list-item-subtitle v-if="customerEmail">
						<i>at {{ customerEmail }}</i>
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-card>
</template>

<script>
export default {
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
		customerEmail() {
			if (this.customer) {
				return this.customer.email ? this.customer.email : undefined;
			} else {
				return undefined;
			}
		}
	},
	methods: {
		printReceipt() {},
		mailReceipt() {}
	}
};
</script>