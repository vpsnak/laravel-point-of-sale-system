<template>
	<div>
		<v-text-field
			:loading="loading"
			v-model="closing_amount"
			type="number"
			label="Closing amount"
			required
		></v-text-field>
		<v-row>
			<v-col cols="6" v-if="cashRegister.earnings.cash_total">
				<span class="title">
					Remaining:
					<span
						class="amber--text"
						v-text="'$ ' + cashRegister.earnings.cash_total.toFixed(2)"
					/>
				</span>
			</v-col>
			<v-col cols="6">
				<v-btn @click="close" :loading="loading">Close Cash Register</v-btn>
			</v-col>
		</v-row>
	</div>
</template>
<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			loading: false,
			closing_amount: null
		};
	},
	computed: {
		store() {
			return this.$store.state.store || false;
		},
		cashRegister() {
			return this.$store.state.cashRegister || false;
		}
	},
	methods: {
		close() {
			this.loading = true;
			let payload = {
				data: {
					store_id: this.store.id,
					cash_register_id: this.cashRegister.id,
					closing_amount: this.closing_amount
				}
			};
			this.closeCashRegister(payload)
				.then(() => {
					this.$emit("submit");
				})
				.finally(() => {
					this.loading = false;
				});
		},
		...mapActions(["closeCashRegister"])
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>