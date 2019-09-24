<template>
	<payment :totalAmount="total" />
</template>

<script>
import { mapState } from "vuex";
export default {
	props: {
		currentStep: Object
	},
	computed: {
		...mapState("cart", {
			total: "total"
		})
	},
	methods: {}
};
</script>
