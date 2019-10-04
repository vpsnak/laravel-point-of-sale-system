<template>
	<div>
		<v-btn @click.stop="visibility = true">Dialog</v-btn>

		<interactiveDialog
			v-if="visibility"
			:show="visibility"
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