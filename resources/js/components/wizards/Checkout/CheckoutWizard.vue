<template>
	<v-stepper>
		<v-stepper-header>
			<v-stepper-step
				step="1"
				editable
			>
				Shipping
			</v-stepper-step>

			<v-divider></v-divider>

			<v-stepper-step
				step="2"
				editable
			>Payment
			</v-stepper-step>

		</v-stepper-header>
		<v-stepper-content step="1">
			<shippingStep />
		</v-stepper-content>
		<v-stepper-content step="2">
			<paymentStep />
		</v-stepper-content>
	</v-stepper>
</template>

<script>
	export default {};
</script>