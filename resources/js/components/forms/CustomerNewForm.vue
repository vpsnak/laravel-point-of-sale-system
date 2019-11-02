<template>
	<div>
		<v-form @submit="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-user</v-icon>Customer Form
				</v-chip>
			</div>
			<v-text-field v-model="firstName" label="First name" required></v-text-field>
			<v-text-field v-model="lastName" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.email" label="Email" required></v-text-field>
			<v-row justify="space-around">
				<v-switch v-model="formFields.house_account_status" label="Has house account"></v-switch>
				<v-switch v-model="formFields.no_tax" label="No tax"></v-switch>
			</v-row>
			<v-text-field v-model="formFields.house_account_number" label="House account number" required></v-text-field>
			<v-text-field
				type="number"
				v-model="formFields.house_account_limit"
				label="House account limit"
				required
			></v-text-field>
			<v-text-field v-model="formFields.no_tax_file" label="No Tax Certification"></v-text-field>
			<v-textarea rows="3" v-model="formFields.comment" label="Comments"></v-textarea>
			<v-checkbox v-model="syncName" label="Use customer's name as default address name"></v-checkbox>
			<v-row>
				<v-col cols="4">
					<v-text-field
						v-model="formFields.address.first_name"
						label="Address First name"
						:disabled="loading || syncName"
						required
					></v-text-field>
					<v-text-field v-model="formFields.address.city" label="City" :disabled="loading" required></v-text-field>
					<v-select
						v-model="formFields.address.region"
						:items="regions"
						label="Regions"
						required
						item-text="default_name"
						item-value="region_id"
					></v-select>
					<v-text-field
						v-model="formFields.address.company"
						label="Company"
						:disabled="loading"
						required
					></v-text-field>
				</v-col>

				<v-col cols="4">
					<v-text-field
						v-model="formFields.address.last_name"
						label="Address Last name"
						:disabled="loading || syncName"
						required
					></v-text-field>
					<v-text-field
						v-model="formFields.address.postcode"
						label="Postcode"
						:disabled="loading"
						required
					></v-text-field>
					<v-select
						v-model="formFields.address.country_id"
						:items="countries"
						label="Countries"
						required
						item-text="iso2_code"
						item-value="iso2_code"
					></v-select>
					<v-text-field v-model="formFields.address.vat_id" label="Vat id" :disabled="loading" required></v-text-field>
				</v-col>
				<v-col cols="4">
					<v-text-field v-model="formFields.address.street" label="Street" :disabled="loading" required></v-text-field>
					<v-text-field
						v-model="formFields.address.street2"
						label="Second Street"
						:disabled="loading"
						required
					></v-text-field>
					<v-text-field
						v-model="formFields.address.phone"
						label="Phone"
						type="number"
						:min="0"
						:disabled="loading"
						required
					></v-text-field>
				</v-col>
			</v-row>
			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
	</div>
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
				no_tax_file: null,
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
					company: null,
					vat_id: null,
					deliverydate: null
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
				.then(() => {
					this.$emit("submit", {
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
		},
		beforeDestroy() {
			this.$off("submit");
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