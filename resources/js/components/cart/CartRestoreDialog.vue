<template>
	<v-dialog :value="show" width="500" @click:outside="close">
		<v-card>
			<v-card-title primary-title>Carts on Hold</v-card-title>
			<v-chip-group multiple column active-class="primary--text">
				<v-chip
					class="d-flex justify-center pa-2"
					v-for="cartOnHold in cartsOnHold"
					:key="cartOnHold.id"
					close
					@click="restoreCart(cartOnHold)"
					@click:close="removeCart(cartOnHold)"
				>
					<span>
						{{ cartOnHold.name }}
						<v-icon left>mdi-cartOnHold</v-icon>
					</span>
				</v-chip>
			</v-chip-group>
			<v-divider></v-divider>

			<v-card-actions>
				<div class="flex-grow-1"></div>
				<v-btn color="primary" text @click="close">Close</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import { mapActions } from "vuex";
export default {
	props: {
		show: Boolean
	},

	data() {
		return {
			onHold: []
		};
	},

	computed: {
		cartsOnHold: {
			get() {
				return this.onHold;
			},
			set(value) {
				this.onHold = value;
			}
		}
	},

	mounted() {
		this.getCartsOnHold();
	},

	methods: {
		getCartsOnHold() {
			let payload = {
				model: "carts"
			};
			this.$store.dispatch("getAll", payload).then(response => {
				this.cartsOnHold = response;
			});
		},
		close() {
			this.$store.state.cartRestoreDialog = false;
		},
		restoreCart(cartOnHold) {
			let cart = JSON.parse(cartOnHold.cart).products;

			this.$store.state.cart.products = cart.products;
			this.removeCart(cartOnHold).then(() => {
				this.close();
			});

			this.getOne({
				model: "customers",
				data: {
					id: cartOnHold.cart.customer_id
				},
				mutation: "cart/setCustomer"
			});
		},
		removeCart(cartOnHold) {
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