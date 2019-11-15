<template>
	<v-form @submit="submit">
		<v-row>
			<v-col cols="4">
				<v-text-field v-model="formFields.first_name" label="First name" :disabled="loading" required></v-text-field>
				<v-text-field v-model="formFields.city" label="City" :disabled="loading" required></v-text-field>
				<v-select
					:loading="loading"
					v-model="formFields.region_id"
					:items="regions"
					label="Regions"
					required
					item-text="default_name"
					item-value="region_id"
				></v-select>
				<v-text-field v-model="formFields.company" label="Company" :disabled="loading" required></v-text-field>
			</v-col>
			<v-col cols="4">
				<v-text-field v-model="formFields.last_name" label="Last name" :disabled="loading" required></v-text-field>
				<v-text-field v-model="formFields.postcode" label="Postcode" :disabled="loading" required></v-text-field>
				<v-select
					:loading="loading"
					v-model="formFields.country_id"
					:items="countries"
					label="Countries"
					required
					item-text="iso2_code"
					item-value="iso2_code"
				></v-select>
				<v-text-field v-model="formFields.vat_id" label="Vat id" :disabled="loading" required></v-text-field>
			</v-col>
			<v-col cols="4">
				<v-text-field v-model="formFields.street" label="Street" :disabled="loading" required></v-text-field>
				<v-text-field v-model="formFields.street2" label="Second Street" :disabled="loading" required></v-text-field>
				<v-text-field
					v-model="formFields.phone"
					label="Phone"
					type="number"
					:min="0"
					:disabled="loading"
					required
				></v-text-field>
				<!-- <v-text-field
			v-model="formFields.deliverydate"
			label="Delivery date"
			:disabled="loading"
			required
				></v-text-field>-->
			</v-col>
			<v-row justify="space-around">
				<v-switch v-model="formFields.billing" label="Default billing"></v-switch>
				<v-switch v-model="formFields.shipping" label="Default shipping"></v-switch>
			</v-row>
		</v-row>
		<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
		<v-btn v-if="this.model === undefined" @click="clear">clear</v-btn>
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
			countries: [],
			regions: [],
			formFields: {
				first_name: null,
				last_name: null,
				street: null,
				street2: null,
				city: null,
				country_id: null,
				region_id: null,
				postcode: null,
				phone: null,
				company: null,
				vat_id: null,
				deliverydate: null,
				shipping: false,
				billing: false
			}
		};
	},
	mounted() {
		this.getAllRegions();
		this.getAllCountries();
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
			this.formFields.region = this.formFields.region_id;

			let payload = {
				model: "addresses",
				data: { ...this.formFields }
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
					// this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
		},
		clear() {
			this.formFields = { ...this.defaultValues };
		},

		getAllRegions() {
			this.loading = true;
			this.getAll({
				model: "regions"
			})
				.then(regions => {
					this.regions = regions;
				})
				.finally(() => {
					this.loading = false;
				});
		},
		getAllCountries() {
			this.getAll({
				model: "countries"
			}).then(countries => {
				this.countries = countries;
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