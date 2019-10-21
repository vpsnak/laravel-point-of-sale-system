<template>
	<v-btn @click="printReceipt">Print receipt</v-btn>
</template>

<script>
export default {
	props: {
		order_id: Number
	},
	methods: {
		printReceipt() {}
	}
};
</script>