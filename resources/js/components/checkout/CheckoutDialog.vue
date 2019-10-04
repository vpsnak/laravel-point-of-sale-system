<template>
	<v-dialog v-model="show" fullscreen hide-overlay transition="dialog-bottom-transition" @click.stop>
		<interactiveDialog
			v-if="closePrompt"
			:show="closePrompt"
			title="Cancel order?"
			content="Are you sure you want to <strong>cancel</strong> the current order?"
			@confirmation="confirmation"
			actions
			persistent
		/>

		<v-card>
			<v-toolbar>
				<v-btn @click="close" icon>
					<v-icon>mdi-close</v-icon>
				</v-btn>
				<v-toolbar-title>Checkout</v-toolbar-title>
			</v-toolbar>
			<v-container fluid>
				<v-row>
					<v-col cols="9">
						<v-card class="pa-2">
							<v-card-text>
								<checkoutStepper />
							</v-card-text>
						</v-card>
					</v-col>
					<v-col cols="3">
						<cart icon="mdi-clipboard-list" title="Order summary" :items="items" />
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
	data() {
		return {
			interactiveDialog: false
		};
	},
	computed: {
		closePrompt: {
			get() {
				return this.interactiveDialog;
			},
			set(value) {
				this.interactiveDialog = value;
			}
		},
		items() {
			if (this.$store.state.cart.order) {
				return this.$store.state.cart.order.items;
			}
			return [];
		}
	},
	methods: {
		close() {
			this.closePrompt = true;
		},
		confirmation(event) {
			this.closePrompt = false;

			if (event) {
				this.resetState;
				this.$store.state.checkoutDialog = false;
			}
		},

		...mapMutations("cart", ["resetState"])
	}
};
</script>