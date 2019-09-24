<template>
	<v-form v-model="valid">
		<div class="text-center">
			<v-chip color="indigo darken-4" label>
				<v-icon left>fas fa-address-card</v-icon>Address Form
			</v-chip>
		</div>
		<v-text-field
			v-model="area_code_id"
			:rules="valueRules"
			:counter="10"
			label="Area Code id"
			required
		></v-text-field>
		<v-text-field v-model="first_name" :rules="valueRules" :counter="10" label="First name" required></v-text-field>
		<v-text-field v-model="last_name" :rules="valueRules" :counter="10" label="Last name" required></v-text-field>
		<v-text-field v-model="street" :rules="valueRules" :counter="10" label="Street" required></v-text-field>
		<v-text-field v-model="city" :rules="valueRules" :counter="10" label="City" required></v-text-field>
		<v-text-field v-model="country_id" :rules="valueRules" :counter="10" label="Country id" required></v-text-field>
		<v-text-field v-model="region" :rules="valueRules" :counter="10" label="Region" required></v-text-field>
		<v-text-field v-model="postcode" :rules="valueRules" :counter="12" label="Postcode" required></v-text-field>
		<v-text-field v-model="phone" :rules="valueRules" :counter="10" label="Country id" required></v-text-field>
		<v-text-field
			v-model="customer_id"
			:rules="valueRules"
			:counter="10"
			label="Customer id"
			required
		></v-text-field>
	</v-form>
</template>

<script>
	export default {
		data: () => ({
			valid: false,
			area_code_id: "",
			last_name: "",
			first_name: "",
			street: "",
			city: "",
			country_id: "",
			region: "",
			postcode: "",
			phone: "",
			customer_id: "",
			valueRules: [
				v => !!v || "Value is required",
				v => v.length <= 10 || "Value must be less than 10"
			]
		})
	};
</script>