<template>
	<v-form @submit="submit">
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-address-card</v-icon>Address Form
			</v-chip>
		</div>
		<v-text-field
			v-model="formFields.first_name"
			counter
			label="First name"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field
			v-model="formFields.last_name"
			counter
			label="Last name"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field v-model="formFields.street" counter label="Street" :disabled="loading" required></v-text-field>
		<v-text-field
			v-model="formFields.street2"
			counter
			label="Second Street"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field v-model="formFields.city" counter label="City" :disabled="loading" required></v-text-field>
		<v-text-field
			v-model="formFields.country_id"
			counter
			label="Country id"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field v-model="formFields.region" counter label="Region" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.postcode" counter label="Postcode" :disabled="loading" required></v-text-field>
		<v-text-field
			v-model="formFields.phone"
			counter
			label="Phone"
			type="number"
			:min="0"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field v-model="formFields.company" counter label="Company" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.vat_id" counter label="Vat id" :disabled="loading" required></v-text-field>
		<v-text-field
			v-model="formFields.deliverydate"
			counter
			label="Delivery date"
			:disabled="loading"
			required
		></v-text-field>
		<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object || undefined
	},
	data() {
		return {
			loading: false,
			defaultValues: {},
			formFields: {
				last_name: null,
				first_name: null,
				street: null,
				street2: null,
				city: null,
				country_id: null,
				region: null,
				postcode: null,
				phone: null,
				company: null,
				vat_id: null,
				deliverydate: null
			}
		};
	},
	mounted() {
		this.defaultValues = this.formFields;
		if (this.$props.model) {
			this.formFields = {
				...this.$props.model
			};
		}
	},
	beforeDestroy() {
		this.$off("submit");
	},
	methods: {
		submit() {
			this.loading = true;
			let payload = {
				model: "addresses",
				data: { ...this.address }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "addresses",
						notification: {
							msg: "Address added successfully!",
							type: "success"
						}
					});
					this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
		},
		clear() {
			this.formFields = { ...this.defaultValues };
		},

		...mapActions({
			create: "create"
		})
	}
};
</script>