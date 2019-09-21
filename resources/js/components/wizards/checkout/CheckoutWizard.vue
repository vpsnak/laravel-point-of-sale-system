<template>
	<v-card>
		<v-toolbar>
			<v-btn icon dark @click="close">
				<v-icon>mdi-close</v-icon>
			</v-btn>
			<v-toolbar-title>Checkout</v-toolbar-title>
		</v-toolbar>
		<v-container fluid>
			<v-row>
				<v-col cols="3">
					<v-card class="pa-2">
						<v-card-title class="justify-center" align="center">
							<v-row align="center" justify="center">
								<v-col align="center" justify="center">
									<v-icon>fa-list-alt</v-icon>
									<h5 class="text-center">Order summary</h5>
								</v-col>
							</v-row>
						</v-card-title>
						<v-card-text>
							<h3>Items:</h3>
							<v-list dense style="height:60vh; overflow-y:auto;">
								<v-list-group v-for="cartProduct in cartProducts" :key="cartProduct.id">
									<template v-slot:activator>
										<v-list-item dense>
											<v-row>
												<v-list-item-content>
													<v-list-item-title>{{ cartProduct.name }}</v-list-item-title>
													<v-list-item-subtitle>$ {{ cartProduct.qty * cartProduct.price.amount }}</v-list-item-subtitle>
												</v-list-item-content>
											</v-row>
										</v-list-item>
									</template>
									<v-list-item>
										<v-row>
											<v-col cols="12" md="3">
												<v-text-field type="number" label="Qty" v-model="cartProduct.qty" disabled></v-text-field>
											</v-col>
											<v-col cols="12" md="5">
												<v-text-field
													label="Discount"
													:value="cartProduct.discount_type ? cartProduct.discount_type : 'None'"
													disabled
												></v-text-field>
											</v-col>
											<v-col cols="12" md="4" v-if="cartProduct.discount_amount">
												<v-text-field
													type="number"
													label="Amount"
													:value="cartProduct.discount_amount"
													disabled
												></v-text-field>
											</v-col>

											<v-col cols="12" md="12" v-if="cartProduct.notes">
												<v-textarea
													:value="cartProduct.notes"
													rows="3"
													label="Notes"
													:hint="'For product: ' + cartProduct.name"
													counter
													no-resize
													disabled
												></v-textarea>
											</v-col>
										</v-row>
									</v-list-item>
								</v-list-group>
							</v-list>
						</v-card-text>
					</v-card>
				</v-col>
				<v-col cols="9">
					<v-card class="mb-4">
						<v-card-text>
							<!-- <v-stepper>
								<v-stepper-header>
									<template v-for="n in steps">
										<v-stepper-step :key="`${n}-step`" :complete="e1 > n" :step="n" editable>Step {{ n }}</v-stepper-step>

										<v-divider v-if="n !== steps" :key="n"></v-divider>
									</template>
								</v-stepper-header>

								<v-stepper-items>
									<v-stepper-content>
										<v-card class="mb-12" height="200px"></v-card>

										<v-btn color="primary" @click>Continue</v-btn>

										<v-btn text>Cancel</v-btn>
									</v-stepper-content>
								</v-stepper-items>
							</v-stepper>-->
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</v-card>
</template>

<script>
export default {
	computed: {
		cartProducts() {
			return this.$store.state.cartProducts;
		}
	},
	methods: {
		close() {
			this.$store.state.checkoutDialog = false;
		}
	}
};
</script>