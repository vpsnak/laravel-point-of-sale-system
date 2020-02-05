<template>
	<ValidationObserver v-slot="{ invalid }">
		<v-form @submit.prevent="submit">
			<v-row>
				<v-col cols="4">
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="First Name">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.first_name"
							label="First name"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="City">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.city"
							label="City"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Region">
						<v-select
							:readonly="$props.readonly"
							:loading="loading"
							v-model="formFields.region_id"
							:items="regions"
							label="Regions"
							:error-messages="errors"
							:success="valid"
							item-text="default_name"
							item-value="region_id"
						></v-select>
					</ValidationProvider>
					<ValidationProvider rules="max:100" v-slot="{ errors, valid }" name="Company">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.company"
							label="Company"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
				</v-col>
				<v-col cols="4">
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Last Name">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.last_name"
							label="Last name"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Postcode">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.postcode"
							label="Postcode"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Countries">
						<v-select
							:readonly="$props.readonly"
							:loading="loading"
							v-model="formFields.country_id"
							:items="countries"
							label="Countries"
							:error-messages="errors"
							:success="valid"
							item-text="iso2_code"
							item-value="iso2_code"
						></v-select>
					</ValidationProvider>
					<ValidationProvider rules="max:100" v-slot="{ errors, valid }" name="Vat id">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.vat_id"
							label="Vat id"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
				</v-col>
				<v-col cols="4">
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Street">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.street"
							label="Street"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="max:100" v-slot="{ errors, valid }" name="Second Street">
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.street2"
							label="Second Street"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>

					<ValidationProvider
						:rules="{ required : true, min:8 , max:100, regex:/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g}"
						v-slot="{ errors, valid }"
						name="Phone"
					>
						<v-text-field
							:readonly="$props.readonly"
							v-model="formFields.phone"
							label="Phone"
							:min="0"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>

					<!-- <v-text-field
			v-model="formFields.deliverydate"
			label="Delivery date"
			:disabled="loading"
			required
					></v-text-field>-->
				</v-col>
				<v-row justify="space-around">
					<v-switch :readonly="$props.readonly" v-model="formFields.billing" label="Default billing"></v-switch>
					<v-switch :readonly="$props.readonly" v-model="formFields.shipping" label="Default shipping"></v-switch>
				</v-row>
			</v-row>
			<v-row v-if="!$props.readonly">
				<v-col cols="12" align="center" justify="center">
					<v-btn
						class="mr-4"
						type="submit"
						:loading="loading"
						:disabled="invalid || loading"
						color="secondary"
					>submit</v-btn>
					<v-btn v-if="!model" @click="clear" color="orange">clear</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object,
		readonly: Boolean
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
		...mapActions({
			getAll: "getAll",
			create: "create"
		}),
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
		}
	}
};
</script>
