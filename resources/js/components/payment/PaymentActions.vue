<template>
	<v-row>
		<v-col cols="12">
			<h3>Order payment</h3>
		</v-col>
		<v-col cols="12">
			<div class="d-flex justify-space-evenly align-center">
				<v-btn-toggle v-model="paymentType" mandatory>
					<v-btn
						v-for="(paymentType, index) in types"
						:key="index"
						:value="paymentType.type"
						:disabled="loading"
					>
						<v-icon class="pr-2">{{ paymentType.icon }}</v-icon>
						{{ paymentType.name }}
					</v-btn>
				</v-btn-toggle>

				<v-spacer></v-spacer>

				<div v-if="paymentType === 'card'">
					<v-text-field
						label="Card number"
						type="number"
						prepend-inner-icon="mdi-credit-card"
						v-model="card.number"
						style="max-width:300px;"
					></v-text-field>
					<v-text-field
						label="Security code"
						type="number"
						prepend-inner-icon="mdi-lock"
						v-model="card.security_code"
						style="max-width:300px;"
					></v-text-field>
					<v-text-field
						label="Exp date"
						v-model="card.exp_date"
						style="max-width:300px;"
						prepend-inner-icon="mdi-calendar"
					></v-text-field>
				</div>

				<v-spacer></v-spacer>

				<v-text-field
					v-if="paymentType !== 'coupon'"
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="paymentAmount"
					style="max-width:150px;"
				></v-text-field>

				<v-spacer></v-spacer>

				<v-text-field
					v-if="paymentType === 'coupon' || paymentType === 'giftcard'"
					label="Code"
					:prepend-inner-icon="getIcon()"
					v-model="code"
					style="max-width:300px;"
				></v-text-field>

				<v-spacer></v-spacer>
			</div>
		</v-col>
		<v-col cols="12">
			<v-btn @click="sendPayment" :loading="loading" :disabled="loading" block>Send payment</v-btn>
		</v-col>
		<v-col cols="12">
			<span class="title">Remaining: $ {{ remaining }}</span>
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
			code: null,

			card: {
				number: null,
				security_code: null,
				exp_date: null
			}
		};
	},

	mounted() {},

	methods: {
		sendPayment() {
			let payload;

			switch (this.paymentType) {
				case "cash":
					payload = {
						paymentAmount: this.paymentAmount,
						paymentType: this.paymentType
					};
					break;
				case "card":
					payload = {
						card: this.card,
						paymentAmount: this.paymentAmount,
						paymentType: this.paymentType
					};
					break;
				case "coupon":
					payload = {
						code: this.code,
						paymentType: this.paymentType
					};
					break;
				case "giftcard":
					payload = {
						code: this.code,
						paymentAmount: this.paymentAmount,
						paymentType: this.paymentType
					};

					break;
				default:
					break;
			}

			this.$emit("sendPayment", payload);
		},

		getIcon() {
			return _.find(this.$props.types, ["type", this.paymentType]).icon;
		}
	},
	beforeDestroy() {
		this.$off("sendPayment", this.address);
	}
};
</script>