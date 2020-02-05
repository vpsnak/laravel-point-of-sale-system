<template></template>

<script>
import { mapActions, mapMutations } from "vuex";

export default {
	mounted() {
		this.logout();
	},
	methods: {
		...mapActions(["logout"])
	}
};
</script>
