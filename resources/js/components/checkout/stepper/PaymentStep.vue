<template>
	<payment :order_id="order.id" />
</template>

<script>
import { mapState } from "vuex";
export default {
	props: {
		currentStep: Object
	},
	computed: {
		...mapState("cart", ["order"])
	},
	methods: {}
};
</script>
