<template>
	<v-card>
		<v-card-title primary-title>Products</v-card-title>
		<div v-for="(cart,index) in cartsOnHold" :key="cart.index">
			<v-chip class="ma-2" close color="cyan" label text-color="white" @click="addAll(cart)">
				<v-icon left>mdi-cart</v-icon>
				Cart number {{index}}
			</v-chip>
		</div>
		<v-card-text>{{showRestoredCarts() }}</v-card-text>
		<v-divider></v-divider>

		<v-card-actions>
			<div class="flex-grow-1"></div>
			<v-btn color="primary" text @click="restoreCartDialog = false">Add to cart</v-btn>
			<v-btn color="primary" text @click="close">Close</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	export default {
		computed: {
			cartsOnHold() {
				return this.$store.state.cartsOnHold;
			}
		},
		methods: {
			close() {
				this.$store.state.restoreCartDialog = false;
			},
			showRestoredCarts() {},
			addAll() {}
		}
	};
</script>