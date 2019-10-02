<template>
	<v-dialog
		:value="show"
		fullscreen
		hide-overlay
		transition="dialog-bottom-transition"
		@click:outside="close"
	>
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
						<cart icon="mdi-clipboard-list" title="Order summary" :items="items" />
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
import { mapMutations } from "vuex";

export default {
	props: {
		show: Boolean
	},
	computed: {
		items() {
			if (this.$store.state.cart.order) {
				return this.$store.state.cart.order.items;
			}
			return [];
		}
	},
	methods: {
		close() {
			this.resetState;
			this.$store.state.checkoutDialog = false;
		},

		...mapMutations("cart", ["resetState"])
	}
};
</script>