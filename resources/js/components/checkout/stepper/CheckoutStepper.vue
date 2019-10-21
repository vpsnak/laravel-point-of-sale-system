<template>
	<v-stepper v-model="currentCheckoutStep">
		<v-stepper-header>
			<v-stepper-step
				v-for="checkoutStep in checkoutSteps"
				:key="checkoutStep.id"
				:step="checkoutStep.id"
				:complete="checkoutStep.completed"
			>
				{{ checkoutStep.name }}
				<v-divider></v-divider>
			</v-stepper-step>
		</v-stepper-header>
		<v-stepper-items>
			<v-stepper-content
				v-for="checkoutStep in checkoutSteps"
				:key="checkoutStep.id"
				:step="checkoutStep.id"
			>
				<v-card class="mx-auto">
					<v-card-title class="justify-center" align="center">
						<v-row align="center" justify="center">
							<v-col align="center" justify="center">
								<v-icon>{{ checkoutStep.icon }}</v-icon>
								<h5 class="text-center">{{ checkoutStep.name }}</h5>
							</v-col>
						</v-row>
					</v-card-title>
					<v-card-text>
						<component :is="checkoutStep.component" :key="currentCheckoutStep" />
					</v-card-text>
				</v-card>
			</v-stepper-content>
		</v-stepper-items>
	</v-stepper>
</template>

<script>
export default {
	computed: {
		currentCheckoutStep() {
			return this.$store.state.cart.currentCheckoutStep;
		},
		checkoutSteps() {
			return this.$store.state.cart.checkoutSteps;
		}
	}
};
</script>