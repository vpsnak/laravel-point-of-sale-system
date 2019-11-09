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
						:disabled="(paymentType.type === 'house-account' && houseAccount) || loading || orderLoading"
						:key="index"
						:value="paymentType.type"
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
					:disabled="loading || orderLoading"
					:min="0.01"
					:max="99999"
					v-if="paymentType !== 'coupon'"
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="amount"
					@keyup="limits"
					@keyup.enter="sendPayment"
					@blur="limits"
					style="max-width:150px;"
				></v-text-field>

				<v-spacer></v-spacer>

				<v-text-field
					v-if="paymentType === 'coupon' || paymentType === 'giftcard'"
					label="Code"
					:prepend-inner-icon="getIcon()"
					:disabled="loading || orderLoading"
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
				:loading="loading || orderLoading"
				:disabled="loading || orderLoading"
				block
			>{{ paymentBtnTxt }}</v-btn>
		</v-col>
		<v-col cols="12" v-if="remainingAmount !== undefined">
			<span class="title">
				Remaining:
				<span class="amber--text" v-text="'$ ' + remainingAmount.toFixed(2)" />
			</span>
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
		remaining: Number,
		loading: Boolean
	},
	data() {
		return {
			orderLoading: false,
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
		houseAccountNumber() {
			if (this.houseAccount) {
				return this.$store.state.cart.customer.house_account_number;
			} else {
				return false;
			}
		},
		houseAccount() {
			if (this.$store.state.cart.customer) {
				if (
					this.$store.state.cart.customer.house_account_status &&
					this.$store.state.cart.customer.house_account_number &&
					this.$store.state.cart.customer.house_account_limit > 0
				) {
					return true;
				}
			} else {
				return false;
			}
		},
		houseAccountLimit() {
			return parseFloat(this.$store.state.cart.customer.house_account_limit);
		},
		remainingAmount() {
			if (parseFloat(this.$props.remaining) >= 0) {
				this.amount = parseFloat(this.$props.remaining);
				return parseFloat(this.$props.remaining);
			} else {
				this.amount = parseFloat(this.$store.state.cart.cart_price);
				return parseFloat(this.$store.state.cart.cart_price);
			}
		},
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
		pay() {
			let payload;

			switch (this.paymentType) {
				case "pos-terminal":
					payload = {
						paymentAmount: this.paymentAmount,
						paymentType: this.paymentType
					};
					break;
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
				case "house-account":
					payload = {
						house_account_number: this.houseAccountNumber,
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
		limits() {
			if (this.paymentType !== "cash") {
				if (this.paymentType === "house-account") {
					if (parseFloat(this.amount) > this.houseAccountLimit) {
						this.amount = this.houseAccountLimit;
					}
				}
				if (parseFloat(this.amount) > parseFloat(this.remainingAmount)) {
					this.amount = this.remainingAmount.toFixed(2);
				}
			} else {
				if (parseFloat(this.amount) > 99999) {
					this.amount = 99999;
				}
			}
		},
		clearState() {
			this.amount = this.remainingAmount;
			this.code = null;

			this.card.number = null;
			this.card.cvc = null;
			this.card.exp_date = null;
		},
		sendPayment() {
			this.orderLoading = true;
			if (this.$store.state.cart.order === undefined) {
				this.submitOrder().then(response => {
					this.pay();
					this.orderLoading = false;
				});
			} else {
				this.pay();
				this.orderLoading = false;
			}
		},

		...mapActions("cart", ["submitOrder"]),

		getIcon() {
			return _.find(this.$props.types, ["type", this.paymentType]).icon;
		}
	},
	beforeDestroy() {
		this.$off("sendPayment", this.sendPayment);
	}
};
</script>