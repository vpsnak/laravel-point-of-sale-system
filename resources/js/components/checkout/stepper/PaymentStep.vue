<template>
	<div>
		<payment @payment="payment" />
		<v-row justify="center" align="center" class="my-3">
			<v-col :cols="5" justify="center" :align="orderId ? 'center' : 'start'">
				<v-btn
					color="teal"
					@click="prevStep()"
					v-if="!orderId"
					dark
					:disabled="$store.state.cart.paymentLoading"
				>
					<v-icon small left>mdi-chevron-left</v-icon>Back
				</v-btn>

				<h3 v-if="change > 0" class="my-3">
					Change:
					<span class="amber--text" v-text="'$ ' + parseFloat(change).toFixed(2)" />
				</h3>

				<v-btn
					v-if="
                        orderId && completed && !$store.state.cart.refundLoading
                    "
					color="primary"
					@click="completeStep"
					:loading="loading"
					:disabled="loading"
				>Complete order</v-btn>
			</v-col>
		</v-row>
	</div>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
	data() {
		return {
			completed: false,
			change: null
		};
	},
	mounted() {},
	computed: {
		...mapState("cart", ["order"]),
		loading: {
			get() {
				return this.$store.state.cart.completeOrderLoading;
			},
			set(value) {
				this.$store.state.cart.completeOrderLoading = value;
			}
		},
		orderId() {
			return this.order.id;
		}
	},
	methods: {
		payment(payload) {
			if (payload.order_status === "paid") {
				this.completed = true;
			} else {
				this.completed = false;
			}

			this.change = payload.change;
		},
		completeStep() {
			this.loading = true;
			let payload = {
				model: "orders",
				data: {
					id: this.order.id,
					status: "complete"
				},
				mutation: "cart/setOrder"
			};
			let receipt_payload = {
				data: {
					order_id: this.order.id
				}
			};
			this.create(payload).then(response => {
				this.$store.dispatch("cart/completeStep").then(() => {
					this.printReceipt(receipt_payload).then(() => {
						this.loading = false;
					});
				});
			});
		},
		prevStep() {
			this.$store.state.cart.currentCheckoutStep--;
		},
		...mapActions(["create", "printReceipt"])
	}
};
</script>
