<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Name">
				<v-text-field
					v-model="formFields.name"
					label="Name"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Regions">
				<v-select
					v-model="formFields.region_id"
					:items="regions"
					label="Regions"
					:disabled="loading"
					:loading="loading"
					:error-messages="errors"
					:success="valid"
					item-text="default_name"
					item-value="region_id"
				></v-select>
			</ValidationProvider>
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Countries">
				<v-select
					v-model="formFields.country_id"
					:items="countries"
					label="Countries"
					:disabled="loading"
					:loading="loading"
					:error-messages="errors"
					:success="valid"
					item-text="iso2_code"
					item-value="iso2_code"
				></v-select>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Street">
				<v-text-field
					v-model="formFields.street"
					label="Street"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="max:191" v-slot="{ errors, valid }" name="Second Street">
				<v-text-field
					v-model="formFields.street1"
					label="Second Street"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>

			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="invalid || loading">submit</v-btn>
			<v-btn v-if="!model" @click="clear">clear</v-btn>
		</v-form>
	</ValidationObserver>
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
				name: null,
				street: null,
				street1: null,
				region_id: null,
				country_id: null
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
			let payload = {
				model: "store-pickups",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "store-pickups",
						notification: {
							msg: "Store pickup added successfully!",
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