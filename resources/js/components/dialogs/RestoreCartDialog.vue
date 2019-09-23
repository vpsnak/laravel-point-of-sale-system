<template>
	<v-card>
		<v-card-title primary-title>Products</v-card-title>
		<v-chip-group multiple column active-class="primary--text">
			<v-chip class="d-flex justify-center pa-2" v-for="cart in cartList" :key="cart.id">
				<span>
					{{cart.id}}
					<v-icon left>mdi-cart</v-icon>
					{{ cart.created_at }}
				</span>
			</v-chip>
		</v-chip-group>
		<v-card-text>{{showRestoredCarts() }}</v-card-text>
		<v-divider></v-divider>

		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" text @click="restoreCartDialog = false">Restore</v-btn>
			<v-btn color="primary" text @click="close">Close</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	export default {
		mounted() {
			this.getAllCarts();
		},
		computed: {
			allcarts() {
				this.getAllCarts();
			},
			cartsOnHold() {
				return this.$store.state.cartsOnHold;
			},
			cartList: {
				get() {
					return this.$store.state.cartList;
				},
				set(value) {
					this.$store.state.cartList = value;
				}
			}
		},
		methods: {
			close() {
				this.$store.state.restoreCartDialog = false;
			},
			showRestoredCarts() {},
			addAll() {},
			getAllCarts() {
				let payload = {
					model: "carts",
					mutation: "setCartList"
				};
				this.$store
					.dispatch("getAll", payload)
					.then(result => {})
					.catch(error => {
						console.log(error);
					});
			}
		}
	};
</script>