<template>
	<v-text-field
		v-model="customer_email"
		label="Send plant care via e-mail"
		prepend-inner-icon="mdi-email-send"
		append-outer-icon="mdi-send"
		@click:append-outer="plantCare"
		:loading="loading"
		:disabled="loading"
	></v-text-field>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
	props: {
		model: Object
	},
	data() {
		return {
			loading: false,
			customer_email: ""
		};
	},
	methods: {
		...mapActions(["mailPlantCare"]),

		plantCare() {
			let payload = {
				product: this.$props.model.id,
				email: this.customer_email
			};
			this.loading = true;

			this.mailPlantCare(payload).finally(() => {
				this.loading = false;
			});
		}
	}
};
</script>