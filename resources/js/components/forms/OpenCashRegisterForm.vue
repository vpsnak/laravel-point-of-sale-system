<template>
	<ValidationObserver v-slot="{ invalid }">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Name">
				<v-select
					:loading="loading"
					v-model="store_id"
					:disabled="storeDisabled"
					:items="stores"
					label="Stores"
					item-text="name"
					item-value="id"
					@change="changeCashRegisters"
					@input="enableCashRegisters"
					:error-messages="errors"
					:success="valid"
				></v-select>
			</ValidationProvider>
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Name">
				<v-select
					:loading="loading"
					:disabled="cashRegisterDisabled"
					v-model="cash_register_id"
					:items="cash_registers"
					label="Cash Register"
					item-text="name"
					item-value="id"
					@input="enableOpeningAmount"
					:error-messages="errors"
					:success="valid"
				></v-select>
			</ValidationProvider>
			<ValidationProvider
				v-if="!cashRegisterIsopen"
				rules="required|max_value:99999"
				v-slot="{ errors, valid }"
				name="Name"
			>
				<v-text-field
					v-if="!cashRegisterIsopen"
					:loading="loading"
					:disabled="openingAmountDisabled"
					v-model="opening_amount"
					type="number"
					label="Opening amount"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<v-row>
				<v-col
					cols="6"
					v-if="
                        remainingAmount &&
                            cash_register_id &&
                            cashRegisterIsopen
                    "
				>
					<span class="title">
						Amount:
						<span class="amber--text" v-text="'$ ' + remainingAmount.toFixed(2)" />
					</span>
				</v-col>
				<v-col cols="6">
					<v-btn
						color="secondary"
						type="submit"
						:loading="loading"
						:disabled="disableOpenCashRegister || invalid"
					>Open Cash Register</v-btn>
				</v-col>
			</v-row>
			<v-alert v-if="cashRegisterIsopen" dense outlined type="warning">
				Warning: The selected cash register is already
				<strong>open</strong>
			</v-alert>
		</v-form>
	</ValidationObserver>
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
			status: true,
			remainingAmount: null
		};
	},
	mounted() {
		this.loading = true;

		this.getAll({
			model: "stores"
		}).then(stores => {
			this.stores = stores;
			if (this.role == "admin") {
				this.storeDisabled = false;
			}
			this.loading = false;
		});

		this.getAll({
			model: "cash-registers"
		})
			.then(cash_registers => {
				this.cash_registers = cash_registers;
			})
			.finally(() => {
				this.loading = false;
			});

		this.$root.$on("barcodeScan", barcode => {
			this.barcodeHandling(barcode);
		});
	},
	computed: {
		store() {
			return this.$store.state.store;
		},
		cashRegister() {
			return this.$store.state.cashRegister;
		},
		cashRegisterIsopen() {
			for (const cash_register of this.cash_registers) {
				if (
					cash_register.id == this.cash_register_id &&
					cash_register.is_open
				) {
					this.remainingAmount = cash_register.earnings.cash_total;
					return true;
				}
			}
			return false;
		},
		disableOpenCashRegister() {
			if (
				(this.store_id && this.opening_amount) ||
				this.cashRegisterIsopen
			) {
				return false;
			} else {
				return true;
			}
		},
		role() {
			return this.$store.getters.role;
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
				.catch()
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
		cashierDisabled() {
			if (this.role == "admin" || this.role == "store_manager") {
				return false;
			} else {
				return true;
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
		barcodeHandling(barcode) {
			for (const cash_register of this.cash_registers) {
				if (cash_register.barcode === barcode) {
					this.cash_register_id = cash_register.id;
					this.store_id = cash_register.store.id;
					this.storeDisabled = true;
					if (cash_register.is_open === false) {
						this.enableOpeningAmount();
					}
				}
			}
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
