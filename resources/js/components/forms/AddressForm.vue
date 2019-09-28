<template>
	<v-form>
		<div class="text-center">
			<v-chip color="indigo darken-4" label>
				<v-icon left>fas fa-address-card</v-icon>Address Form
			</v-chip>
		</div>
		<v-text-field v-model="address.area_code_id" counter label="Area Code id" required></v-text-field>
		<v-text-field v-model="address.first_name" counter label="First name" required></v-text-field>
		<v-text-field v-model="address.last_name" counter label="Last name" required></v-text-field>
		<v-text-field v-model="address.street" counter label="Street" required></v-text-field>
		<v-text-field v-model="address.city" counter label="City" required></v-text-field>
		<v-text-field v-model="address.country_id" counter label="Country id" required></v-text-field>
		<v-text-field v-model="address.region" counter label="Region" required></v-text-field>
		<v-text-field v-model="address.postcode" counter label="Postcode" required></v-text-field>
		<v-text-field v-model="address.phone" counter label="Country id" required></v-text-field>
		<v-text-field v-model="address.customer_id" counter label="Customer id" required></v-text-field>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
	import { mapActions } from "vuex";

	export default {
		data() {
			return {
				address: {
					area_code_id: "",
					last_name: "",
					first_name: "",
					street: "",
					city: "",
					country_id: "",
					region: "",
					postcode: "",
					phone: "",
					customer_id: ""
				}
			};
		},
		watch: {
			address: {
				handler() {
					this.$emit("address", this.address);
				},
				deep: true
			}
		},
		beforeDestroy() {
			this.$off("address", this.address);
		},
		methods: {
			submit() {
				let payload = {
					model: "address",
					data: {
						area_code_id: this.area_code_id,
						last_name: this.last_name,
						first_name: this.first_name,
						street: this.street,
						city: this.city,
						country_id: this.country_id,
						region: this.region,
						postcode: this.postcode,
						phone: this.phone,
						customer_id: this.customer_id
					}
				};
				this.create(payload).then(() => {});
			},
			clear() {
				address.area_code_id = "";
				address.last_name = "";
				address.first_name = "";
				address.street = "";
				address.city = "";
				address.country_id = "";
				address.region = "";
				address.postcode = "";
				address.phone = "";
				address.customer_id = "";
			},
			...mapActions({
				create: "create"
			})
		}
	};
</script>