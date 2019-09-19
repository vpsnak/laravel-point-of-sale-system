<template>
	<div>
		<v-text-field v-model="keyword"></v-text-field>
		<v-btn @click="fire"></v-btn>
	</div>
</template>

<script>
export default {
	data() {
		return {
			model: "products",
			keyword: ""
		};
	},
	methods: {
		fire() {
			let payload = {
				model: "products",
				keyword: this.keyword
			};

			this.$store.dispatch("search", payload);
		}
	}
};
</script>