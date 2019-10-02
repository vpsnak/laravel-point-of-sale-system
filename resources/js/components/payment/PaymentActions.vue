<template>
	<v-row>
		<v-col cols="12">
			<h3>Order payment</h3>
		</v-col>
		<v-col cols="12">
			<v-toolbar prominent class="d-flex justify-space-between flex-column pt-5">
				<v-toolbar-title class="title">Remaining: ${{ remaining }}</v-toolbar-title>
				<v-btn-toggle v-model="paymentType" mandatory group dense>
					<v-btn
						v-for="(paymentType, index) in types"
						:key="index"
						:value="paymentType.type"
						:disabled="loading"
					>{{ paymentType.name }}</v-btn>
				</v-btn-toggle>

				<v-spacer></v-spacer>

				<v-text-field
					label="Amount"
					type="number"
					prepend-inner-icon="mdi-currency-usd"
					v-model="paymentAmount"
					style="max-width:300px"
				></v-text-field>

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
			paymentType: null
		};
	},

	methods: {
		sendPayment() {
			let payload = {
				paymentAmount: this.paymentAmount,
				paymentType: this.paymentType
			};

			this.$emit("sendPayment", payload);
		}
	}
};
</script>