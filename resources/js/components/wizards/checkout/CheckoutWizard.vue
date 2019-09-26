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
					<cart
						icon="fa_list_alt"
						title="Order summary"
					/>
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
											<v-card-title
												class="justify-center"
												align="center"
											>
												<v-row
													align="center"
													justify="center"
												>
													<v-col
														align="center"
														justify="center"
													>
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
												<v-btn
													color="primary"
													@click="completeStep(checkoutStep)"
												>Continue</v-btn>
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
			...mapGetters("checkout", ["getCheckoutSteps"]),
			cartProducts() {
				return this.$store.state.cartProducts;
			},
			cartCustomer() {
				return this.$store.state.cartCustomer;
			},
			currentCheckoutStep() {
				return this.$store.state.checkout.currentCheckoutStep;
			}
		},
		methods: {
			close() {
				this.$store.state.checkoutDialog = false;
			},
			completeStep(checkoutStep) {
				this.$store.dispatch("checkout/completeStep", checkoutStep);
			}
		}
	};
</script>