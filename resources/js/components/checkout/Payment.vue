<template>
	<div>

	</div>
</template>

<script>
	import { mapActions, mapMutations, mapState } from "vuex";

	export default {
		...mapState("payment", {
			payment_types: "payment_types",
			amount: "amount"
		})
	};
</script>