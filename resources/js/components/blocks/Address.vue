<template>
	<v-text-field v-if="address"></v-text-field>

	<v-select v-else></v-select>
</template>

<script>
export default {
	props: {
		addresses: Array | null,
		address: Object | null
	},

	computed: {
		displayAddress() {},
		displayAddresses() {}
	}
};
</script>