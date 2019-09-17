<template>
	<v-app id="app">
		<sideMenu />

		<topMenu />

		<v-content>
			<router-view></router-view>
		</v-content>

		<footerMenu />
	</v-app>
</template>

<script>
export default {
	data() {
		return {};
	},
	mounted() {}
};
</script>