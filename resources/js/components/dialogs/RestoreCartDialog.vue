<template>
	<v-card>
		<v-card-title primary-title>Carts on Hold</v-card-title>
		<v-chip-group multiple column active-class="primary--text">
			<v-chip
				class="d-flex justify-center pa-2"
				v-for="cartOnHold in cartsOnHold"
				:key="cartOnHold.id"
				close
				@click="restoreCart(cartOnHold)"
				@click:close="nukeCart(cartOnHold)"
			>
				<span>
					{{cartOnHold.id}}
					<v-icon left>mdi-cartOnHold</v-icon>
					{{ cartOnHold.created_at }}
				</span>
			</v-chip>
		</v-chip-group>
		<v-divider></v-divider>

		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" text @click="close">Close</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	export default {
		computed: {
			cartsOnHold: {
				get() {
					return this.$store.state.cartsOnHold;
				},
				set(value) {
					this.$store.state.cartsOnHold = value;
				}
			},
			cartCustomer: {
				get() {
					return this.$store.state.cartCustomer;
				},
				set(value) {
					this.$store.state.cartCustomer = value;
				}
			}
		},
		methods: {
			close() {
				this.$store.state.restoreCartDialog = false;
			},
			restoreCart(cartOnHold) {
				this.$store.state.cartCustomer = JSON.parse(cartOnHold.customer_id);
				this.$store.state.cartProducts = JSON.parse(cartOnHold.cart);
				this.nukeCart(cartOnHold);
				this.close();
			},
			nukeCart(cartOnHold) {
				console.log(cartOnHold);
				const payload = {
					model: "carts",
					mutation: "removeCartOnHold",
					id: cartOnHold.id
				};
				this.$store.dispatch("delete", payload).then(response => {
					this.cartsOnHold.splice(cartOnHold, 0);
				});
			}
		}
	};
</script>