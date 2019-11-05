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
			<v-col cols="6" v-if="remainingAmount !== undefined">
				<span class="title">
					Remaining:
					<span class="amber--text" v-text="'$ ' + remainingAmount.toFixed(2)" />
				</span>
			</v-col>
			<v-col cols="6">
				<v-btn @click="close" :loading="loading">Close Cash Register</v-btn>
			</v-col>
		</v-row>
		<!-- <div class="d-flex align-center mt-5">
			<div class="flex-grow-1"></div>
			<v-btn @click="close" :loading="loading">Close Cash Register</v-btn>
		</div>-->
	</div>
</template>
<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			loading: false,
			closing_amount: null,
			remainingAmount: 121231.123131
		};
	},
	computed: {
		store() {
			console.log(this.$store.state.store);
			return this.$store.state.store || false;
		},
		cashRegister() {
			console.log(this.$store.state.cashRegister);
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
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			closeCashRegister: "closeCashRegister",
			delete: "delete"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>