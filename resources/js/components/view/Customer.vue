<template>
	<v-container>
		<v-row v-if="customerData">
			<v-col cols="12">
				<v-card>
					<v-card-title>Billing Information</v-card-title>
					<v-card-text>
						<customerForm :model="customerData" @submit="submit"></customerForm>
					</v-card-text>
				</v-card>
			</v-col>
			<v-col cols="12" v-if="customerData.addresses.length > 0">
				<v-card>
					<v-toolbar flat color="primary" dark>
						<v-toolbar-title>Addresses</v-toolbar-title>
					</v-toolbar>
					<v-tabs vertical>
						<v-tab v-for="index in customerData.addresses.length" :key="index">Address {{index}}</v-tab>
						<v-tab-item v-for="address in customerData.addresses" :key="address.id">
							<v-card flat>
								<v-card-text>
									<addressForm @submit="submit" :model="address"></addressForm>
								</v-card-text>
							</v-card>
						</v-tab-item>
					</v-tabs>
				</v-card>
			</v-col>
		</v-row>
		<v-row v-else>
			<v-col cols="12" align="center" justify="center">
				<v-progress-circular indeterminate color="secondary"></v-progress-circular>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Int32Array | null
	},
	data() {
		return {
			customer: null
		};
	},
	mounted() {
		if (this.model)
			this.getOne({
				model: "customers",
				data: {
					id: this.model.id
				}
			}).then(result => {
				this.customer = result;
			});
	},
	computed: {
		customerData() {
			return this.customer;
		}
	},
	methods: {
		submit() {
			this.$emit("submit", {
				getRows: true,
				model: "customers",
				notification: {
					msg: "Customer updated successfully",
					type: "success"
				}
			});
		},
		...mapActions({
			getOne: "getOne"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
