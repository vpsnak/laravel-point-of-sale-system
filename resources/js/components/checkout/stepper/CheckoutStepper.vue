<template>
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
</template>

<script>
	import { mapGetters } from "vuex";

	export default {
		computed: {
			...mapGetters("cart", ["getCheckoutSteps"]),
			customer() {
				return this.$store.state.cart.customer;
			},
			currentCheckoutStep() {
				return this.$store.state.cart.currentCheckoutStep;
			}
		},
		methods: {
			completeStep(checkoutStep) {
				this.$store.dispatch("cart/completeStep", checkoutStep);
			}
		}
	};
</script>