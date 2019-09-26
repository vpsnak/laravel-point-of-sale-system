<template>
	<v-container fluid>
		<v-row>
			<v-col cols="8">
				<productList />
			</v-col>
			<v-col cols="4">
				<cart
					icon="shopping_cart"
					title="Shopping cart"
					:editable="true"
					:actions="true"
					:toggles="true"
				/>
			</v-col>
		</v-row>

	</v-container>
</template>

<script>
	export default {};
</script>