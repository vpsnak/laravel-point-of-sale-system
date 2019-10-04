<template>
	<div>
		<v-btn @click="fire">Dialog</v-btn>

		<confirmationDialog
			title="Alvanos"
			content="paymentActions"
			@confirmation="confirmation"
			confirmationBtnTxt="alvanos"
		/>
	</div>
</template>

<script>
export default {
	data() {
		return {
			show: false
		};
	},

	methods: {
		confirmation(event) {
			console.log(event);
			this.$store.state.confirmationDialog = false;
		},
		fire() {
			this.$store.state.confirmationDialog = true;
		}
	}
};
</script>