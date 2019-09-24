<template>
	<v-card>
		<v-toolbar>
			<v-btn @click="close" icon>
				<v-icon>mdi-close</v-icon>
			</v-btn>
			<v-toolbar-title>Checkout</v-toolbar-title>
		</v-toolbar>
		<v-container fluid>
			<v-row>
				<v-col cols="3">
					<v-card class="pa-2">
						<v-card-title align="center" class="justify-center">
							<v-row align="center" justify="center">
								<v-col align="center" justify="center">
									<v-icon>fa-list-alt</v-icon>
									<h5 class="text-center">Order summary</h5>
								</v-col>
							</v-row>
						</v-card-title>
						<v-card-text>
							<v-divider></v-divider>
							<v-list class="pt-2" dense subheader>
								<v-subheader class="justify-center">
									<v-icon>person</v-icon>
								</v-subheader>
								<v-list-item>
									<v-list-item-content class="text-center" v-if="cartCustomer">
										<v-list-item-title>{{ cartCustomer.first_name + " " + cartCustomer.last_name }}</v-list-item-title>
										<v-list-item-subtitle>{{ cartCustomer.email }}</v-list-item-subtitle>
										<v-list-item-subtitle>{{ cartCustomer.phone }}</v-list-item-subtitle>
									</v-list-item-content>
									<v-list-item-content class="text-center" v-else>
										<v-list-item-title>Guest</v-list-item-title>
									</v-list-item-content>
								</v-list-item>
							</v-list>
							<v-divider></v-divider>
							<v-list class="pt-2" dense style="height:60vh; overflow-y:auto;" subheader>
								<v-subheader class="justify-center">
									<v-icon>shopping_cart</v-icon>
								</v-subheader>
								<v-list-group :key="cartProduct.id" v-for="cartProduct in cartProducts">
									<template v-slot:activator>
										<v-list-item dense>
											<v-row>
												<v-list-item-content>
													<v-list-item-title>{{ cartProduct.name }}</v-list-item-title>
													<v-list-item-subtitle>
														$ {{ cartProduct.qty *
														cartProduct.price.amount }}
													</v-list-item-subtitle>
												</v-list-item-content>
											</v-row>
										</v-list-item>
									</template>
									<v-list-item>
										<v-row>
											<v-col cols="12" md="3">
												<v-text-field disabled label="Qty" type="number" v-model="cartProduct.qty"></v-text-field>
											</v-col>
											<v-col cols="12" md="5">
												<v-text-field
													:value="cartProduct.discount_type ? cartProduct.discount_type : 'None'"
													disabled
													label="Discount"
												></v-text-field>
											</v-col>
											<v-col cols="12" md="4" v-if="cartProduct.discount_amount">
												<v-text-field
													:value="cartProduct.discount_amount"
													disabled
													label="Amount"
													type="number"
												></v-text-field>
											</v-col>
											<v-col cols="12" md="12" v-if="cartProduct.notes">
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
							<v-stepper v-model="currentCheckoutStep">
								<v-stepper-header>
									<v-stepper-step
										v-for="(checkoutStep, index) in getCheckoutSteps"
										:key="index"
										:step="++index"
										:complete="checkoutStep.completed"
									>
										{{ checkoutStep.name }}
										<v-divider></v-divider>
									</v-stepper-step>
								</v-stepper-header>
								<v-stepper-items>
									<v-stepper-content
										v-for="(checkoutStep, index) in getCheckoutSteps"
										:key="index"
										:step="++index"
									>
										<v-card class="pa-2">
											<v-card-title class="justify-center" align="center">
												<v-row align="center" justify="center">
													<v-col align="center" justify="center">
														<v-icon>{{ checkoutStep.icon }}</v-icon>
														<h5 class="text-center">{{ checkoutStep.name }}</h5>
													</v-col>
												</v-row>
											</v-card-title>
											<v-card-text>
												<component :is="checkoutStep.component" />
											</v-card-text>
											<v-card-actions>
												<div class="flex-grow-1"></div>
												<v-btn color="primary" @click="completeStep(checkoutStep)">Continue</v-btn>
											</v-card-actions>
										</v-card>
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
import { mapGetters } from "vuex";

export default {
	computed: {
		...mapGetters("cart", ["getCheckoutSteps"]),
		cartProducts() {
			return this.$store.state.cartProducts;
		},
		cartCustomer() {
			return this.$store.state.cartCustomer;
		},
		currentCheckoutStep() {
			return this.$store.state.cart.currentCheckoutStep;
		}
	},
	methods: {
		close() {
			this.$store.state.checkoutDialog = false;
		},
		completeStep(checkoutStep) {
			this.$store.dispatch("cart/completeStep", checkoutStep);
		}
	},
	mounted() {
		console.log(this.getCheckoutSteps);
	}
};
</script>