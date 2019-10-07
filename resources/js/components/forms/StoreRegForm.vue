<template>
	<div>
		<v-select
			v-model="store_id"
			:items="stores"
			:rules="[v => !!v || 'Store is required']"
			label="Stores"
			required
			item-text="name"
			item-value="id"
			v-on:change="changeCashRegisters"
			v-on:input="enableCashRegisters"
		></v-select>
		<v-select
			:disabled="disabled"
			v-model="cash_register_id"
			:items="cash_registers"
			:rules="[v => !!v || 'Cash Register is required']"
			label="Cash Register"
			required
			item-text="name"
			item-value="id"
		></v-select>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</div>
</template>
<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			cash_register_id: null,
			store_id: null,
			stores: [],
			cash_registers: [],
			disabled: true
		};
	},
	mounted() {
		this.getAll({
			model: "stores"
		}).then(stores => {
			this.stores = stores;
		});
		this.getAll({
			model: "cash-registers"
		}).then(cash_registers => {
			this.cash_registers = cash_registers;
		});
	},
	methods: {
		submit() {},
		changeCashRegisters() {
			for (const stra of this.stores) {
				if (stra.id == this.store_id) {
					this.cash_registers = stra.cash_registers;
				}
			}
		},
		enableCashRegisters() {
			this.disabled = false;
		},
		clear() {
			this.cash_register_id = null;
			this.store_id = null;
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
	}
};
</script>
