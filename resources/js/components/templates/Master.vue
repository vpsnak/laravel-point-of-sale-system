<template>
	<v-app id="app">
		<sideMenu v-if="authorized" />

		<topMenu v-if="authorized" />

		<notification />

		<v-content>
			<router-view></router-view>
		</v-content>
	</v-app>
</template>

<script>
import { mapActions } from "vuex";

export default {
	computed: {
		authorized() {
			window.axios.defaults.headers.common[
				"Authorization"
			] = this.$store.state.token;

			return this.$store.getters.authorized;
		}
	},
	methods: {
		...mapActions({
			getOne: "getOne"
		})
	}
};
</script>

<style>
.fas,
.fab,
.fa {
	font-size: 1.2em !important;
}

a {
	text-decoration: none;
}
html {
	overflow-y: hidden;
}

input[type="number"] {
	-moz-appearance: textfield;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
	-webkit-appearance: none;
}
</style>
