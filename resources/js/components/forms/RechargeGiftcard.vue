<template>
	<paymentActions :types="paymentTypes" paymentBtnTxt="Recharge" />
</template>
        
<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object
	},
	data() {
		return {
			giftcard_id: null,
			paymentTypes: []
		};
	},
	methods: {
		getPaymentTypes() {
			this.getAll({ model: "payment-types" }).then(response => {
				this.paymentTypes = response.filter(function(value, index, arr) {
					return value.type === "card" || value.type === "cash";
				});
				console.log(this.paymentTypes);
			});
		},

		...mapActions(["getAll"])
	},
	mounted() {
		this.getPaymentTypes();
		console.log(this.$props.model);
	}
};
</script>