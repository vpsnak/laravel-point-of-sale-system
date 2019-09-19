<template>
	<v-card>
		<v-card-text>
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
					<v-list subheader dense>
						<v-subheader>Shopping cart</v-subheader>
						<v-list-group v-for="item in items" :key="item.id">
							<template v-slot:activator>
								<v-list-item dense>
									<v-list-item-content>
										<v-list-item-title>{{ item.name }}</v-list-item-title>
										<v-list-item-subtitle>$ {{ item.qty * item.price }}</v-list-item-subtitle>
									</v-list-item-content>
									<v-list-item-action>
										<v-btn icon @click.stop="decreaseQty(item)">
											<v-icon color="grey lighten-1">remove</v-icon>
										</v-btn>
									</v-list-item-action>
									<v-list-item-action>
										<v-chip filter @click.stop>
											<span>{{ item.qty }}</span>
										</v-chip>
									</v-list-item-action>
									<v-list-item-action>
										<v-btn icon @click.stop="increaseQty(item)">
											<v-icon color="grey lighten-1">add</v-icon>
										</v-btn>
									</v-list-item-action>
									<v-list-item-action>
										<v-btn icon @click.stop="removeItem(item)">
											<v-icon color="grey lighten-1">delete</v-icon>
										</v-btn>
									</v-list-item-action>
								</v-list-item>
							</template>
							<v-list-item>
								<v-col cols="2">
									<v-text-field type="number" label="Qty" v-model="item.qty" min="1"></v-text-field>
								</v-col>
								<v-col cols="4">
									<v-select :items="discountTypes" label="Discount" item-text="label" item-value="value"></v-select>
								</v-col>
								<v-col cols="4">
									<v-text-field type="number" label="Amount"></v-text-field>
								</v-col>
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
			products: [
				{
					id: "1",
					name: "Cotton Long Sleeve T-shirt",
					price: "44",
					image: "https://cdn.vuetifyjs.com/images/lists/3.jpg",
					sku: "123",
					qty: "3"
				},
				{
					id: "2",
					name: "Recipes",
					price: "4",
					image: "https://cdn.vuetifyjs.com/images/lists/4.jpg",
					sku: "456",
					qty: "5"
				},
				{
					id: "3",
					name: "Work",
					price: "7",
					image: "https://cdn.vuetifyjs.com/images/lists/5.jpg",
					sku: "789",
					qty: "1"
				}
			],
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
		items: {
			get() {
				return this.products;
			},
			set(value) {
				this.products = value;
			}
		}
	},

	methods: {
		decreaseQty(item) {
			if (item.qty > 1) {
				item.qty--;
			}
		},
		increaseQty(item) {
			item.qty++;
		},
		removeItem(item) {
			this.items.splice(item, 1);
		}
	}
};
</script>