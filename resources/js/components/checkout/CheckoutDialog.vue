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
				<v-btn @click.stop="close" icon>
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
						<cart
							:key="order ? order.id : 0"
							icon="mdi-clipboard-list"
							title="Order summary"
							:items="items"
							:order="order"
							:editable="isEditable"
						/>
					</v-col>
				</v-row>
			</v-container>
		</v-card>
	</v-dialog>
</template>

<script>
import { mapMutations, mapActions } from "vuex";

export default {
	data() {
		return {
			closePrompt: false
		};
	},
	computed: {
		isEditable() {
			return this.order ? false : true;
		},
		state: {
			get() {
				return this.$store.state.checkoutDialog;
			},
			set(value) {
				this.$store.state.checkoutDialog = value;
			}
		},
		order() {
			return this.$store.state.cart.order;
		},
		items() {
			if (this.order) {
				return this.$store.state.cart.order.items;
			}
			return undefined;
		}
	},
	methods: {
		close() {
			if (this.order && this.order.status === "complete") {
				this.resetState();
				this.state = false;
			} else if (this.order && this.order.status === "pending") {
				this.closePrompt = true;
			} else {
				this.state = false;
			}
		},
		confirmation(event) {
			if (event) {
				let payload = {
					model: "orders",
					id: this.order.id
				};

				this.delete(payload).then(response => {
					this.resetState();
					this.state = false;
				});
			}
			this.closePrompt = false;
		},

		...mapActions(["delete"]),
		...mapMutations("cart", ["resetState"])
	}
};
</script>