<template>
	<v-app id="app">
		<sideMenu v-if="auth" />

		<topMenu v-if="auth" />

		<notification />

		<v-content>
			<router-view></router-view>
		</v-content>
	</v-app>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";

export default {
	computed: {
		auth() {
			if (this.authorized && this.role) {
				window.axios.defaults.headers.common[
					"Authorization"
				] = this.$store.state.token;
				return true;
			} else {
				this.logout();
				return false;
			}
		},
		...mapGetters(["authorized", "role"])
	},
	methods: {
		...mapMutations(["logout"])
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
