<template>
	<ValidationObserver v-slot="{ invalid }" ref="customerNewObs">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="First name">
				<v-text-field v-model="firstName" label="First name" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Last Name">
				<v-text-field v-model="lastName" label="Last name" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|email|max:191" v-slot="{ errors, valid }" name="Email">
				<v-text-field
					v-model="formFields.email"
					label="Email"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>

			<v-row justify="space-around">
				<ValidationProvider vid="house_account_status">
					<v-switch v-model="formFields.house_account_status" label="Has house account"></v-switch>
				</ValidationProvider>
			</v-row>
			<v-row justify="space-around">
				<v-col v-if="formFields.house_account_status">
					<ValidationProvider
						rules="required_if:house_account_status|max:100"
						v-slot="{ errors, valid }"
						name="House account number"
					>
						<v-text-field
							v-model="formFields.house_account_number"
							label="House account number"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider
						rules="required_if:house_account_status|max:16|numeric"
						v-slot="{ errors, valid }"
						name="House account limit"
					>
						<v-text-field
							type="number"
							v-model="formFields.house_account_limit"
							label="House account limit"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
				</v-col>
			</v-row>
			<ValidationProvider rules="max:65535" v-slot="{ errors, valid }" name="Comment">
				<v-textarea
					rows="3"
					v-model="formFields.comment"
					label="Comments"
					:error-messages="errors"
					:success="valid"
				></v-textarea>
			</ValidationProvider>

			<v-checkbox v-model="syncName" label="Use customer's name as default address name"></v-checkbox>
			<v-row>
				<v-col cols="4">
					<ValidationProvider
						rules="required|max:100"
						v-slot="{ errors, valid }"
						name="Address First Name"
					>
						<v-text-field
							v-model="formFields.address.first_name"
							label="Address First name"
							:disabled="loading || syncName"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="City">
						<v-text-field
							v-model="formFields.address.city"
							label="City"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Region">
						<v-select
							v-model="formFields.address.region"
							:items="regions"
							label="Regions"
							required
							item-text="default_name"
							item-value="region_id"
							:error-messages="errors"
							:success="valid"
						></v-select>
					</ValidationProvider>
				</v-col>

				<v-col cols="4">
					<ValidationProvider
						rules="required|max:100"
						v-slot="{ errors, valid }"
						name="Address Last Name"
					>
						<v-text-field
							v-model="formFields.address.last_name"
							label="Address Last name"
							:disabled="loading || syncName"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Postcode">
						<v-text-field
							v-model="formFields.address.postcode"
							label="Postcode"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Countries">
						<v-select
							v-model="formFields.address.country_id"
							:items="countries"
							label="Countries"
							required
							item-text="iso2_code"
							item-value="iso2_code"
							:error-messages="errors"
							:success="valid"
						></v-select>
					</ValidationProvider>
				</v-col>
				<v-col cols="4">
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Street">
						<v-text-field
							v-model="formFields.address.street"
							label="Street"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>

					<ValidationProvider rules="max:100" v-slot="{ errors, valid }" name="Second Street">
						<v-text-field
							v-model="formFields.address.street2"
							label="Second Street"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
					<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Phone">
						<v-text-field
							v-model="formFields.address.phone"
							label="Phone"
							type="number"
							:min="0"
							:disabled="loading"
							:error-messages="errors"
							:success="valid"
						></v-text-field>
					</ValidationProvider>
				</v-col>
				<v-row justify="space-around">
					<v-switch v-model="formFields.address.billing" label="Default billing"></v-switch>
					<v-switch v-model="formFields.address.shipping" label="Default shipping"></v-switch>
				</v-row>
			</v-row>
			<v-row>
				<v-col cols="12" align="center" justify="center">
					<v-btn
						color="secondary"
						class="mr-4"
						type="submit"
						:loading="loading"
						:disabled="invalid || loading"
					>submit</v-btn>
					<v-btn color="orange" @click="clear">clear</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</ValidationObserver>
</template>
<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	data() {
		return {
			loading: false,
			syncName: true,
			countries: [],
			regions: [],
			defaultValues: {},
			formFields: {
				first_name: null,
				last_name: null,
				email: null,
				house_account_number: null,
				house_account_limit: null,
				house_account_status: false,
				no_tax: false,
				comment: null,
				address: {
					first_name: null,
					last_name: null,
					street: null,
					street2: null,
					city: null,
					country_id: null,
					region: null,
					postcode: null,
					phone: null,
					deliverydate: null,
					shipping: false,
					billing: false
				}
			}
		};
	},
	mounted() {
		this.getAll({
			model: "regions"
		}).then(regions => {
			this.regions = regions;
		});
		this.getAll({
			model: "countries"
		}).then(countries => {
			this.countries = countries;
		});
		this.defaultValues = { ...this.formFields };
	},
	computed: {
		firstName: {
			get() {
				return this.formFields.first_name;
			},
			set(value) {
				if (this.syncName) {
					this.formFields.address.first_name = this.formFields.first_name = value;
				} else {
					this.formFields.first_name = value;
				}
			}
		},
		lastName: {
			get() {
				return this.formFields.last_name;
			},
			set(value) {
				if (this.syncName) {
					this.formFields.address.last_name = this.formFields.last_name = value;
				} else {
					this.formFields.last_name = value;
				}
			}
		}
	},
	methods: {
		submit() {
			this.loading = true;
			let payload = {
				model: "customers",
				data: { ...this.formFields }
			};

			this.create(payload)
				.then(response => {
					this.$emit("submit", {
						data: { customer: response },
						getRows: true,
						model: "customers",
						notification: {
							msg: "Customer added successfully",
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
			this.formFields.address.first_name = null;
			this.formFields.address.last_name = null;
			this.formFields.address.street = null;
			this.formFields.address.street2 = null;
			this.formFields.address.city = null;
			this.formFields.address.country_id = null;
			this.formFields.address.region = null;
			this.formFields.address.postcode = null;
			this.formFields.address.phone = null;
			this.formFields.address.deliverydate = null;
			this.formFields.address.shipping = false;
			this.formFields.address.billing = false;
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
