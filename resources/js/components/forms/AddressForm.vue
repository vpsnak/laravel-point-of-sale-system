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
	</v-form>
</template>

<script>
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
		}
	};
</script>