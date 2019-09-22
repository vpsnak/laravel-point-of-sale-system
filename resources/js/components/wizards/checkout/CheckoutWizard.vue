<template>
	<v-card>
		<v-toolbar>
			<v-btn
				@click="close"
				icon
			>
				<v-icon>mdi-close</v-icon>
			</v-btn>
			<v-toolbar-title>Checkout</v-toolbar-title>
		</v-toolbar>
		<v-container fluid>
			<v-row>
				<v-col cols="3">
					<v-card class="pa-2">
						<v-card-title
							align="center"
							class="justify-center"
						>
							<v-row
								align="center"
								justify="center"
							>
								<v-col
									align="center"
									justify="center"
								>
									<v-icon>fa-list-alt</v-icon>
									<h5 class="text-center">Order summary</h5>
								</v-col>
							</v-row>
						</v-card-title>
						<v-card-text>
							<v-divider></v-divider>
							<v-list
								class="pt-2"
								dense
								subheader
							>
								<v-subheader class="justify-center">
									<v-icon>person</v-icon>
								</v-subheader>
								<v-list-item>
									<v-list-item-content class="text-center">
										<v-list-item-title>Φλοριάνα Πετροπούλου</v-list-item-title>
										<v-list-item-subtitle>asdasd@asd.asd</v-list-item-subtitle>
										<v-list-item-subtitle>6989898989</v-list-item-subtitle>
									</v-list-item-content>
								</v-list-item>
							</v-list>
							<v-divider></v-divider>
							<v-list
								class="pt-2"
								dense
								style="height:60vh; overflow-y:auto;"
								subheader
							>
								<v-subheader class="justify-center">
									<v-icon>shopping_cart</v-icon>
								</v-subheader>
								<v-list-group
									:key="cartProduct.id"
									v-for="cartProduct in cartProducts"
								>
									<template v-slot:activator>
										<v-list-item dense>
											<v-row>
												<v-list-item-content>
													<v-list-item-title>{{ cartProduct.name }}</v-list-item-title>
													<v-list-item-subtitle>$ {{ cartProduct.qty *
														cartProduct.price.amount }}
													</v-list-item-subtitle>
												</v-list-item-content>
											</v-row>
										</v-list-item>
									</template>
									<v-list-item>
										<v-row>
											<v-col
												cols="12"
												md="3"
											>
												<v-text-field
													disabled
													label="Qty"
													type="number"
													v-model="cartProduct.qty"
												></v-text-field>
											</v-col>
											<v-col
												cols="12"
												md="5"
											>
												<v-text-field
													:value="cartProduct.discount_type ? cartProduct.discount_type : 'None'"
													disabled
													label="Discount"
												></v-text-field>
											</v-col>
											<v-col
												cols="12"
												md="4"
												v-if="cartProduct.discount_amount"
											>
												<v-text-field
													:value="cartProduct.discount_amount"
													disabled
													label="Amount"
													type="number"
												></v-text-field>
											</v-col>
											<v-col
												cols="12"
												md="12"
												v-if="cartProduct.notes"
											>
												<v-textarea
													:hint="'For product: ' + cartProduct.name"
													:value="cartProduct.notes"
													counter
													disabled
													label="Notes"
													no-resize
													rows="3"
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
					<v-card class="pa-2">
						<v-card-text>
							<v-stepper>
								<v-stepper-header>
									<v-stepper-step
										:complete="false"
										complete-icon="local_shipping"
										edit-icon="local_shipping"
										step="1"
									>Shipping options
									</v-stepper-step>
									<v-divider></v-divider>
									<v-stepper-step
										:complete="false"
										complete-icon="payment"
										edit-icon="payment"
										step="2"
									>Payment
									</v-stepper-step>
									<v-divider></v-divider>
									<v-stepper-step
										:complete="false"
										editable
										step="3"
									>Completion
									</v-stepper-step>
								</v-stepper-header>
								<v-stepper-items>
									<v-stepper-content step="1">
										<shippingStep />
									</v-stepper-content>
									<v-stepper-content step="2">
										<paymentStep />
									</v-stepper-content>
								</v-stepper-items>
							</v-stepper>
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