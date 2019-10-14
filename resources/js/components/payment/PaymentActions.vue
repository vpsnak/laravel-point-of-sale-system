<template>
	<v-row>
		<v-col cols="12">
			<h3>{{ title }}</h3>
		</v-col>
		<v-col cols="12">
			<div class="d-flex justify-space-evenly align-center">
				<v-btn-toggle v-model="paymentType" mandatory @change="clearState">
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
						label="CVC/CVV"
						type="number"
						prepend-inner-icon="mdi-lock"
						v-model="card.cvc"
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
					:min="0.01"
					:max="99999"
					v-if="paymentType !== 'coupon'"
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="amount"
					@keyup="limits"
					@blur="limits"
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
			<v-btn
				color="blue-grey"
				@click="sendPayment"
				:loading="loading"
				:disabled="loading"
				block
				v-show="remaining > 0"
			>{{ paymentBtnTxt }}</v-btn>
		</v-col>
		<v-col cols="12" v-if="remaining !== undefined">
			<span class="title">Remaining: $ {{ remaining }}</span>
		</v-col>
	</v-row>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		title: String,
		paymentBtnTxt: String,
		types: Array,
		remaining: String,
		loading: Boolean
	},
	data() {
		return {
			paymentAmount: Number,
			paymentType: null,
			code: null,

			card: {
				number: null,
				cvc: null,
				exp_date: null
			}
		};
	},

	computed: {
		amount: {
			get() {
				return this.paymentAmount;
			},
			set(value) {
				this.paymentAmount = value;
			}
		}
	},

	methods: {
		limits() {
			if (this.paymentType !== "cash") {
				if (parseFloat(this.amount) > parseFloat(this.$props.remaining)) {
					this.amount = this.$props.remaining;
					return parseFloat(this.amount);
				}
			} else {
				if (parseFloat(this.amount) > 99999) {
					this.amount = 99999;
				}
			}
		},
		clearState() {
			this.amount = null;
			this.code = null;

			this.card.number = null;
			this.card.cvc = null;
			this.card.exp_date = null;
		},
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
			this.clearState();
		},

		getIcon() {
			return _.find(this.$props.types, ["type", this.paymentType]).icon;
		}
	},
	beforeDestroy() {
		this.$off("sendPayment", this.sendPayment);
	}
};
</script>