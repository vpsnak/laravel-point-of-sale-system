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
export default {};
</script>

<style>
a {
	text-decoration: none;
}
html {
	overflow-y: hidden;
}
</style>