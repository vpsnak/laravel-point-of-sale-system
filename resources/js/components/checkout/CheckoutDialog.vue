<template>
	<v-dialog v-model="show" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
			<v-toolbar>
				<v-btn @click="close" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
				<v-toolbar-title>Checkout</v-toolbar-title>
			</v-toolbar>
			<v-container fluid>
				<v-row>
					<v-col cols="3">
						<cart icon="fa_list_alt" title="Order summary" />
					</v-col>
					<v-col cols="9">
						<v-card class="pa-2">
							<v-card-text>
								<checkoutStepper />
							</v-card-text>
						</v-card>
					</v-col>
				</v-row>
			</v-container>
		</v-card>
	</v-dialog>
</template>

<script>
export default {
	props: {
		show: Boolean
	},
	methods: {
		close() {
			this.$store.state.checkoutDialog = false;
		}
	}
};
</script>