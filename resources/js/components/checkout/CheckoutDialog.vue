<template>
	<v-dialog
		v-model="state"
		fullscreen
		transition="dialog-bottom-transition"
		persistent
		no-click-animation
		@close:outer.stop
	>
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
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn @click.stop="close" icon v-on="on" color="red">
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</template>
					<span>{{ closeBtnTxt }}</span>
				</v-tooltip>

				<v-tooltip bottom v-if="showHoldBtn">
					<template v-slot:activator="{ on }">
						<v-btn @click="hold" icon v-on="on" color="yellow">
							<v-icon>mdi-pause</v-icon>
						</v-btn>
					</template>
					<span>Hold</span>
				</v-tooltip>

				<v-toolbar-title>Checkout</v-toolbar-title>
			</v-toolbar>
			<v-container fluid>
				<v-row>
					<v-col :lg="9" class="py-lg-0">
						<v-card class="mx-auto">
							<checkoutStepper />
						</v-card>
					</v-col>
					<v-col :lg="3" class="py-lg-0">
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
		closeBtnTxt() {
			return this.order && this.$store.state.cart.currentCheckoutStep !== 3
				? "Cancel order"
				: "Close";
		},
		showHoldBtn() {
			if (this.order && this.$store.state.cart.currentCheckoutStep !== 3) {
				return true;
			} else {
				return false;
			}
		},
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
		hold() {
			this.resetState();
			this.state = false;
		},
		close() {
			if (this.order && this.order.status === "complete") {
				this.resetState();
				this.state = false;
			} else if (this.order && this.order.status !== "complete") {
				this.closePrompt = true;
			} else {
				this.state = false;
			}

			this.$emit("close", true);
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
	},
	beforeDestroy() {
		this.$off("close");
	}
};
</script>
