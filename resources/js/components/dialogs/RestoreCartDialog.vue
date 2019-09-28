<template>
	<v-card>
		<v-card-title primary-title>Carts on Hold</v-card-title>
		<v-chip-group
			multiple
			column
			active-class="primary--text"
		>
			<v-chip
				class="d-flex justify-center pa-2"
				v-for="cartOnHold in cartsOnHold"
				:key="cartOnHold.id"
				close
				@click="restoreCart(cartOnHold)"
				@click:close="nukeCart(cartOnHold)"
			>
				<span>
					{{cartOnHold.name}}
					<v-icon left>mdi-cartOnHold</v-icon>
				</span>
			</v-chip>
		</v-chip-group>
		<v-divider></v-divider>

		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn
				color="primary"
				text
				@click="close"
			>Close</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	import { mapActions } from "vuex";

	export default {
		computed: {
			cartsOnHold: {
				get() {
					return this.$store.state.cartsOnHold;
				},
				set(value) {
					this.$store.state.cartsOnHold = value;
				}
			}
		},
		methods: {
			close() {
				this.$store.state.restoreCartDialog = false;
			},
			restoreCart(cartOnHold) {
				this.getOne({
					model: "customers",
					data: {
						id: cartOnHold.customer_id
					}
				}).then(customer => {
					this.$store.commit("cart/setCustomer", customer);
					this.$store.state.cart.products = JSON.parse(cartOnHold.cart);
					this.nukeCart(cartOnHold).then(() => {
						this.close();
					});
				});
			},
			nukeCart(cartOnHold) {
				return this.delete({
					model: "carts",
					id: cartOnHold.id
				}).then(() => {
					this.cartsOnHold = this.cartsOnHold.filter(cart => {
						return cart.id !== cartOnHold.id;
					});
				});
			},
			...mapActions({
				getOne: "getOne",
				delete: "delete"
			})
		}
	};
</script>
