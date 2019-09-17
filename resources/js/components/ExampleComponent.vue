<template>
	<v-app id="app">
		<sideMenu />

		<topMenu />

		<v-content>
			<v-container fluid>
				<v-row justify="center">
					<v-col cols="8">
						<productList />
					</v-col>
					<v-col cols="4">
						<cartList />
					</v-col>
				</v-row>
			</v-container>
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