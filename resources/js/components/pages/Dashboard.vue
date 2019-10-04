<template>
	<div>
		<v-btn @click="fire">Dialog</v-btn>

		<interactiveDialog
			title="Alvanos"
			component="paymentActions"
			@confirmation="confirmation"
			actions
			persistent
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

		mounted() {
			this.fire();
		},

		methods: {
			confirmation(event) {
				this.$store.state.interactiveDialog = false;
			},
			fire() {
				this.$store.state.interactiveDialog = true;
			}
		}
	};
</script>