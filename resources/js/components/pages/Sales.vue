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
		<interactiveDialog
			v-if="visibility"
			:show="visibility"
			title="Select store and cash register"
			:width="600"
			component="storeRegForm"
			:titleCloseBtn="true"
		></interactiveDialog>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			visibility: false
		};
	},
	mounted() {
		this.visibility = true;
	}
};
</script>