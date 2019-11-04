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
		<div class="d-flex align-center mt-5">
			<div class="flex-grow-1"></div>
			<v-btn @click="submit" :loading="loading">Open Cash Register</v-btn>
		</div>
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
			status: true
		};
	},
	mounted() {
		this.loading = true;

		this.getAll({
			model: "stores"
		}).then(stores => {
			this.stores = stores;
			this.storeDisabled = false;
			this.loading = false;
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
				store_id: this.store_id,
				cash_register_id: this.cash_register_id,
				opening_amount: this.opening_amount,
				status: this.status
			};
			this.openCashRegister(payload)
				.then(response => {
					this.$emit("submit", true);
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
		getAllStores() {
			this.getAll({
				model: "stores"
			}).then(stores => {
				this.stores = stores;
			});
		},
		getAllCashRegisters() {
			this.loading = true;
			this.getAll({
				model: "cash-registers"
			})
				.then(cash_registers => {
					this.cash_registers = cash_registers;
				})
				.finally(() => {
					this.loading = false;
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
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>