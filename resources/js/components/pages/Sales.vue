<template>
	<v-container fluid>
		<v-row>
			<v-col :lg="8">
				<productList />
			</v-col>
			<v-col :lg="4">
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
export default {
	data() {
		return {
			visibility: false
		};
	}
};
</script>
