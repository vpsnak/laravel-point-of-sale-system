<template>
	<div>
		<v-select
			v-model="formFields.store_id"
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
			:disabled="cashRegisterDisabled"
			v-model="formFields.cash_register_id"
			:items="cash_registers"
			label="Cash Register"
			required
			item-text="name"
			item-value="id"
			v-on:input="enableOpeningAmount"
		></v-select>
		<v-text-field
			:disabled="openingAmountDisabled"
			v-model="formFields.opening_amount"
			type="number"
			label="Opening amount"
			required
		></v-text-field>

		<v-btn class="mr-4" @click="submit" :loading="loading">Open Cash Register</v-btn>
		<v-btn @click="clear">Close Cash Register</v-btn>
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
			formFields: {
				store_id: null,
				cash_register_id: null,
				user_id: null,
				opening_amount: null,
				opened_by: null,
				opening_time: new Date()
					.toJSON()
					.slice(0, 10)
					.replace(/-/g, "/"),
				status: true
			}
		};
	},
	mounted() {
		this.getAll({
			model: "stores"
		}).then(stores => {
			this.stores = stores;
			this.storeDisabled = false;
		});
		this.formFields.user_id = this.$store.state.user.id;
	},
	methods: {
		submit() {
			this.loading = true;
			for (const store of this.stores) {
				if (store.id == this.formFields.store_id) {
					this.$store.state.store = store;
				}
			}
			for (const cash_register of this.cash_registers) {
				if (cash_register.id == this.formFields.cash_register_id) {
					this.$store.state.cashRegister = cash_register;
				}
			}
			let payload = {
				model: "cash-register-logs",
				data: { ...this.formFields }
			};
			this.openCashRegister(payload)
				.then(() => {
					this.clear();
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
		beforeDestroy() {
			this.$off("submit");
		},
		changeCashRegisters() {
			for (const store of this.stores) {
				if (store.id == this.formFields.store_id) {
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