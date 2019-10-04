<template>
	<div>
		<v-btn @click.stop="visibility = true">Dialog</v-btn>

		<interactiveDialog
			v-if="visibility"
			action="confirmation"
			:show="visibility"
			title="Test confirmation"
			component="paymentActions"
			@confirmation="confirmation"
			persistent
		/>
	</div>
</template>

<script>
export default {
	data() {
		return {
			showInteractiveDialog: false
		};
	},

	computed: {
		visibility: {
			get() {
				return this.showInteractiveDialog;
			},
			set(value) {
				this.showInteractiveDialog = value;
			}
		}
	},

	methods: {
		confirmation(event) {
			this.visibility = false;
		}
	}
};
</script>