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

		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</div>
</template>
<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
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
				opened_by: 1,
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
		// this.getAll({
		// 	model: "cash-registers"
		// }).then(cash_registers => {
		// 	this.cash_registers = cash_registers;
		// });
	},
	methods: {
		submit() {
			for (const store of this.stores) {
				if (store.id == this.formFields.store_id) {
					this.$store.state.store = store;
				}
			}
			for (const cash_register of this.cash_registers) {
				if (cash_register.id == this.formFields.cash_register_id) {
					console.log(cash_register);
					this.$store.state.cashRegister = cash_register;
				}
			}
			let payload = {
				model: "cash-register-logs",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.clear();
				this.$emit("submit", "cash-register-logs");
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
			delete: "delete"
		})
	},
	computed: {
		user_id: {
			get() {
				return this.$store.state.user.id;
			}
		}
	}
};
</script>