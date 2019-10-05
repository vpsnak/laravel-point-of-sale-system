<template>
	<div>
		<v-btn @click.stop="visibility = true">Confirmation dialog</v-btn>

		<interactiveDialog
			v-if="visibility"
			action="confirmation"
			:show="visibility"
			title="Test confirmation"
			component="paymentActions"
			@action="result"
			persistent
		/>

		<v-btn @click.stop="visibility = true">Dialog</v-btn>

		<interactiveDialog
			v-if="visibility"
			action="confirmation"
			:show="visibility"
			title="Test confirmation"
			component="paymentActions"
			@action="result"
			persistent
		/>

		<v-btn @click.stop="visibility = true">Dialog</v-btn>

		<interactiveDialog
			v-if="visibility"
			action="confirmation"
			:show="visibility"
			title="Test confirmation"
			component="paymentActions"
			@action="result"
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
		result(event) {
			console.log(event);
			this.visibility = false;
		}
	}
};
</script>