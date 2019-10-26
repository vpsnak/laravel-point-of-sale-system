<template>
	<v-form @submit="submit">
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-address-card</v-icon>Address Form
			</v-chip>
		</div>
		<v-text-field v-model="formFields.first_name" label="First name" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.last_name" label="Last name" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.street" label="Street" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.street2" label="Second Street" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.city" label="City" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.country_id" label="Country id" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.region" label="Region" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.postcode" label="Postcode" :disabled="loading" required></v-text-field>
		<v-text-field
			v-model="formFields.phone"
			label="Phone"
			type="number"
			:min="0"
			:disabled="loading"
			required
		></v-text-field>
		<v-text-field v-model="formFields.company" label="Company" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.vat_id" label="Vat id" :disabled="loading" required></v-text-field>
		<!-- <v-text-field
			v-model="formFields.deliverydate"
			label="Delivery date"
			:disabled="loading"
			required
		></v-text-field>-->
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
				first_name: null,
				last_name: null,
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
		this.defaultValues = { ...this.formFields };
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