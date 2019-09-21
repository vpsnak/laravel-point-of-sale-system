<template>
	<v-card>
		<v-toolbar dark color="primary">
			<v-btn icon dark @click="close">
				<v-icon>mdi-close</v-icon>
			</v-btn>
			<v-toolbar-title>Settings</v-toolbar-title>
			<div class="flex-grow-1"></div>
			<v-toolbar-items>
				<v-btn dark text @click="dialog = false">Save</v-btn>
			</v-toolbar-items>
		</v-toolbar>
	</v-card>
</template>

<script>
export default {
	methods: {
		close() {
			this.$store.state.checkoutDialog = false;
		}
	}
};
</script>