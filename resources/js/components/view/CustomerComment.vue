<template>
	<v-textarea :value="customerData.comment" v-if="customerData" readonly solo auto-grow outlined></v-textarea>
	<div v-else>Loading...</div>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object
	},
	data() {
		return {
			customer: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "customers",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.customer = result;
			});
	},
	computed: {
		customerData() {
			return this.customer;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>
