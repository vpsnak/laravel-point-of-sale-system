<template>
	<v-app id="app">
		<sideMenu />

		<topMenu />

		<notification />

		<v-content>
			<router-view></router-view>
		</v-content>
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
	overflow-y: auto;
}

input[type="number"] {
	-moz-appearance: textfield;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
	-webkit-appearance: none;
}
</style>