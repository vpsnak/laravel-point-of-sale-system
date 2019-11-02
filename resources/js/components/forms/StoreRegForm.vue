<template>
	<div>
		<v-select
			:loading="loading"
			v-model="store_id"
			:disabled="storeDisabled"
			:items="stores"
			label="Stores"
			required
			item-text="name"
			item-value="id"
			v-on:change="changeCashRegisters"
			v-on:input="enableCashRegisters"
		></v-select>
		<v-select
			:loading="loading"
			:disabled="cashRegisterDisabled"
			v-model="cash_register_id"
			:items="cash_registers"
			label="Cash Register"
			required
			item-text="name"
			item-value="id"
			v-on:input="enableOpeningAmount"
		></v-select>
		<v-text-field
			:loading="loading"
			:disabled="openingAmountDisabled"
			v-model="opening_amount"
			type="number"
			label="Opening amount"
			required
		></v-text-field>

		<v-text-field
			:loading="loading"
			v-model="closing_amount"
			type="number"
			label="Closing amount"
			required
		></v-text-field>

		<v-btn class="mr-4" @click="submit" :loading="loading">Open Cash Register</v-btn>
		<v-btn @click="close" :loading="loading">Close Cash Register</v-btn>
	</div>
</template>
<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			loading: false,
			stores: [],
			cash_registers: [],
			storeDisabled: true,
			cashRegisterDisabled: true,
			openingAmountDisabled: true,
			store_id: null,
			cash_register_id: null,
			opening_amount: null,
			closing_amount: null,
			status: true
		};
	},
	mounted() {
		this.getAll({
			model: "stores"
		}).then(stores => {
			this.stores = stores;
			this.storeDisabled = false;
		});
	},
	methods: {
		submit() {
			this.loading = true;
			let payload = {
				model: "cash-register-logs",
				data: {
					store_id: this.store_id,
					cash_register_id: this.cash_register_id,
					opening_amount: this.opening_amount,
					status: this.status
				}
			};
			this.openCashRegister(payload)
				.then(() => {
					this.$emit("submit", {
						model: "cash-register-logs",
						notification: {
							msg: "Cash register opened successfully",
							type: "success"
						}
					});
					for (const store of this.stores) {
						if (store.id == this.store_id) {
							this.$store.state.store = store;
						}
					}
					for (const cash_register of this.cash_registers) {
						if (cash_register.id == this.cash_register_id) {
							this.$store.state.cashRegister = cash_register;
						}
					}
					this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
		},

		close() {
			this.loading = true;
			let payload = {
				model: "cash-register-logs",
				data: {
					store_id: this.store_id,
					cash_register_id: this.cash_register_id,
					closing_amount: 12
				}
			};
			this.closeCashRegister(payload)
				.then(() => {
					this.$emit("submit", {
						model: "cash-register-logs",
						notification: {
							msg: "Cash register closed successfully",
							type: "success"
						}
					});
					this.$store.state.store = null;
					this.$store.state.cashRegister = null;
					this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
		},

		beforeDestroy() {
			this.$off("submit");
		},
		changeCashRegisters() {
			for (const store of this.stores) {
				if (store.id == this.store_id) {
					this.cash_registers = store.cash_registers;
				}
			}
		},
		enableCashRegisters() {
			this.cashRegisterDisabled = false;
		},

		enableOpeningAmount() {
			this.openingAmountDisabled = false;
		},
		clear() {
			this.formFields = null;
		},
		getAllStores() {
			this.getAll({
				model: "stores"
			}).then(stores => {
				this.stores = stores;
			});
		},
		getAllCashRegisters() {
			this.getAll({
				model: "cash-registers"
			}).then(cash_registers => {
				this.cash_registers = cash_registers;
			});
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			openCashRegister: "openCashRegister",
			closeCashRegister: "closeCashRegister",
			delete: "delete"
		})
	},
	computed: {
		user_id() {
			return this.$store.state.user.id;
		}
	}
};
</script>