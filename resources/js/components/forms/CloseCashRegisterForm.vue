<template>
	<ValidationObserver v-slot="{ invalid }">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Password">
				<v-text-field
					:append-icon="
                        showPassword ? 'visibility' : 'visibility_off'
                    "
					:type="showPassword ? 'text' : 'password'"
					:loading="loading"
					v-model="password"
					label="Password"
					:error-messages="errors"
					:success="valid"
					prepend-icon="mdi-key"
					@click:append="showPassword = !showPassword"
				></v-text-field>
			</ValidationProvider>
			<v-row>
				<v-col :cols="12" v-if="cashRegister.earnings">
					<v-text-field
						class="amber--text"
						label="Cash amount"
						:value="amount"
						:loading="loading"
						readonly
						prepend-icon="mdi-currency-usd"
					></v-text-field>
				</v-col>
			</v-row>
			<v-row>
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
			showPassword: false,
			amount: "",
			loading: true,
			password: ""
		};
	},
	mounted() {
		this.$store.dispatch("cashRegisterAmount").then(response => {
			this.amount = response.toFixed(2);
			this.loading = false;
		});
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
				current_password: this.password
			};

			this.verifySelf(payload)
				.then(() => {
					let payload = {
						data: {
							store_id: this.store.id,
							cash_register_id: this.cashRegister.id,
							password: this.password
						}
					};
					this.closeCashRegister(payload)
						.then(response => {
							this.$emit("submit", { data: { response } });
						})
						.finally(() => {
							this.loading = false;
						});
				})
				.catch(() => {
					this.password = "";
					this.loading = false;
				});
		},
		...mapActions(["verifySelf", "closeCashRegister"])
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
