<template>
	<ValidationObserver v-slot="{ invalid }" ref="closeCashRegisterObs">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Closing Amount">
				<v-text-field
					:loading="loading"
					v-model="closing_amount"
					type="number"
					label="Closing amount"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<v-row>
				<v-col cols="6" v-if="cashRegister.earnings">
					<span class="title">
						Remaining:
						<span
							class="amber--text"
							v-text="'$ ' + cashRegister.earnings.cash_total.toFixed(2)"
						/>
					</span>
				</v-col>
				<v-col cols="6">
					<v-btn type="submit" :loading="loading" :disabled="invalid">Close Cash Register</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</ValidationObserver>
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
		submit() {
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