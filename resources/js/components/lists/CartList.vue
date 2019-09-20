<template>
	<v-card>
		<v-card-text>
			<v-row align="center" justify="center">
				<v-col align="center" justify="center">
					<v-icon>shopping_cart</v-icon>
					<h3 class="text-center">Shopping cart</h3>
				</v-col>
			</v-row>
			<v-row>
				<v-col>
					<v-toolbar flat>
						<v-text-field placeholder="Search customer" class="grey--text">
							<v-icon slot="prepend">mdi-magnify</v-icon>
						</v-text-field>
						<v-tooltip bottom>
							<template v-slot:activator="{ on }">
								<v-btn icon v-on="on" class="ml-1">
									<v-icon>person_add</v-icon>
								</v-btn>
							</template>
							<span>Add customer</span>
						</v-tooltip>
					</v-toolbar>
					<v-divider />
				</v-col>
			</v-row>
			<v-row style="height: 33vh; overflow-y: auto;">
				<v-col>
					<v-list dense style="height:60vh; overflow-y:auto;">
						<v-list-group v-for="cartProduct in cartProducts" :key="cartProduct.id">
							<template v-slot:activator>
								<v-list-item dense>
									<v-layout row>
										<v-list-item-content>
											<v-list-item-title>{{ cartProduct.name }}</v-list-item-title>
											<v-list-item-subtitle>$ {{ cartProduct.qty * cartProduct.price }}</v-list-item-subtitle>
										</v-list-item-content>
										<v-list-item-action>
											<v-btn icon @click.stop="decreaseQty(cartProduct)">
												<v-icon color="grey lighten-1">remove</v-icon>
											</v-btn>
										</v-list-item-action>
										<v-list-item-action>
											<v-chip filter @click.stop>
												<span>{{ cartProduct.qty }}</span>
											</v-chip>
										</v-list-item-action>
										<v-list-item-action>
											<v-btn icon @click.stop="increaseQty(cartProduct)">
												<v-icon color="grey lighten-1">add</v-icon>
											</v-btn>
										</v-list-item-action>
										<v-list-item-action>
											<v-btn icon @click.stop="removeItem(cartProduct)">
												<v-icon color="grey lighten-1">delete</v-icon>
											</v-btn>
										</v-list-item-action>
									</v-layout>
								</v-list-item>
							</template>
							<v-list-item>
								<v-layout row>
									<v-col cols="12" md="3">
										<v-text-field type="number" label="Qty" v-model="cartProduct.qty" min="1"></v-text-field>
									</v-col>
									<v-col cols="12" md="5">
										<v-select :items="discountTypes" label="Discount" item-text="label" item-value="value"></v-select>
									</v-col>
									<v-col cols="12" md="4">
										<v-text-field type="number" label="Amount"></v-text-field>
									</v-col>

									<v-col cols="12" md="12">
										<v-textarea
											:v-model="true"
											rows="3"
											label="Notes"
											:hint="'For product: ' + cartProduct.name"
											counter
											no-resize
										></v-textarea>
									</v-col>
								</v-layout>
							</v-list-item>
						</v-list-group>
					</v-list>
				</v-col>
			</v-row>

			<v-row>
				<v-col>
					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Sub total</span>
						<span class="pa-2">12 {{ }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Tax</span>
						<span class="pa-2">12 {{ }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Total discount</span>
						<span class="pa-2">12 {{ }}</span>
					</div>

					<v-divider />

					<div class="d-flex justify-space-between">
						<span class="pa-2">Total</span>
						<span class="pa-2">12 {{ }}</span>
					</div>

					<v-divider />

					<v-btn block class="my-2">Checkout</v-btn>

					<v-divider />
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>fa-recycle</v-icon>
					</v-btn>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>pause</v-icon>
					</v-btn>
				</v-col>

				<v-col cols="4" class="text-center">
					<v-btn icon>
						<v-icon>delete</v-icon>
					</v-btn>
				</v-col>
			</v-row>
		</v-card-text>
		<v-card-actions></v-card-actions>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			discountTypes: [
				{
					label: "Flat",
					value: "flat"
				},
				{
					label: "Percentage",
					value: "percentage"
				}
			]
		};
	},

	computed: {
		cartProducts: {
			get() {
				return this.$store.state.cartProducts;
			},
			set(value) {
				this.$store.state.cartProducts = value;
			}
		}
	},

	methods: {
		decreaseQty(cartProduct) {
			this.$store.commit("decreaseCartProductQty", cartProduct);
		},
		increaseQty(cartProduct) {
			this.$store.commit("increaseCartProductQty", cartProduct);
		},
		removeItem(cartProduct) {
			this.cartProducts.splice(cartProduct, 1);
		}
	}
};
</script>