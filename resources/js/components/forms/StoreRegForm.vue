<template>
	<div>
		<v-select
			v-if="!cashRegister && !store"
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
			v-if="!cashRegister && !store"
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
			v-if="!cashRegister && !store"
			:loading="loading"
			:disabled="openingAmountDisabled"
			v-model="opening_amount"
			type="number"
			label="Opening amount"
			required
		></v-text-field>

		<v-text-field
			v-if="cashRegister && store"
			:loading="loading"
			v-model="closing_amount"
			type="number"
			label="Closing amount"
			required
		></v-text-field>

		<v-btn v-if="!cashRegister" class="mr-4" @click="submit" :loading="loading">Open Cash Register</v-btn>
		<v-btn v-if="cashRegister && store" @click="close" :loading="loading">Close Cash Register</v-btn>
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
	computed: {
		store() {
			return this.$store.state.store;
		},
		cashRegister() {
			return this.$store.state.cashRegister;
		}
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
				.then(response => {
					this.$emit("submit", {
						model: "cash-register-logs",
						notification: {
							msg: "Cash register opened successfully",
							type: "success"
						}
					});

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
					closing_amount: this.closing_amount
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

					this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
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
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>