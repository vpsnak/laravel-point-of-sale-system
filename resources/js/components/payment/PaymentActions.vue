<template>
	<v-row>
		<v-col cols="12">
			<h3>Order payment</h3>
		</v-col>
		<v-col cols="12">
			<v-toolbar prominent class="d-flex justify-space-between flex-column pt-5">
				<v-toolbar-title class="title">Remaining: $ {{ remaining }}</v-toolbar-title>
				<v-btn-toggle v-model="paymentType" mandatory>
					<v-btn
						v-for="(paymentType, index) in types"
						:key="index"
						:value="paymentType.type"
						:disabled="loading"
						text
					>{{ paymentType.name }}</v-btn>
				</v-btn-toggle>

				<v-spacer></v-spacer>

				<v-text-field
					v-if="paymentType === 'cash'"
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="paymentAmount"
					style="max-width:300px;"
				></v-text-field>

				<div v-else-if="paymentType === 'card'">
					<v-text-field
						label="Card number"
						type="number"
						prepend-inner-icon="mdi-credit-card"
						v-model="paymentAmount"
						style="max-width:300px;"
					></v-text-field>
					<v-text-field
						label="Security code"
						type="number"
						v-model="paymentAmount"
						style="max-width:300px;"
					></v-text-field>
					<v-text-field label="Exp date" v-model="paymentAmount" style="max-width:300px;"></v-text-field>
				</div>

				<v-autocomplete
					v-else-if="paymentType === 'coupon' || paymentType === 'giftcard'"
					label="Code"
					:prepend-inner-icon="getCouponGiftCardIcon()"
					v-model="code"
					style="max-width:300px;"
				></v-autocomplete>

				<v-spacer></v-spacer>

				<v-btn @click="sendPayment" :loading="loading" :disabled="loading">Send payment</v-btn>
				<v-spacer></v-spacer>
			</v-toolbar>
		</v-col>
	</v-row>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		types: Array,
		remaining: Number,
		loading: Boolean
	},
	data() {
		return {
			paymentAmount: null,
			paymentType: null,
			code: null
		};
	},

	mounted() {},

	methods: {
		sendPayment() {
			let payload = {
				paymentAmount: this.paymentAmount,
				paymentType: this.paymentType
			};

			this.$emit("sendPayment", payload);
		},

		getCouponGiftCardIcon() {
			if (this.paymentType === "coupon") {
				return "mdi-ticket";
			} else {
				return "mdi-ticket-account";
			}
		}
	},
	beforeDestroy() {
		this.$off("sendPayment", this.address);
	}
};
</script>