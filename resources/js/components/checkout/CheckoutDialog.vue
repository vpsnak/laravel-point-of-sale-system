<template>
	<v-dialog v-model="state" fullscreen transition="dialog-bottom-transition" persistent>
		<interactiveDialog
			v-if="closePrompt"
			:show="closePrompt"
			action="confirmation"
			title="Cancel order?"
			content="Are you sure you want to <strong>cancel</strong> the current order?"
			@action="confirmation"
			actions
			persistent
		/>

		<v-card>
			<v-toolbar>
				<v-btn @click.stop="closePrompt = true" icon>
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
	data() {
		return {
			closePrompt: false
		};
	},
	computed: {
		state: {
			get() {
				return this.$store.state.checkoutDialog;
			},
			set(value) {
				this.$store.state.checkoutDialog = value;
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
		confirmation(event) {
			if (event) {
				this.resetState();
				this.state = false;
			}

			this.closePrompt = false;
		},

		...mapMutations("cart", ["resetState"])
	}
};
</script>