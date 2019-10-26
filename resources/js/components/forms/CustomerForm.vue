formFields<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-user</v-icon>Customer Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.first_name" label="First name" required></v-text-field>
			<v-text-field v-model="formFields.last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.email" label="Email" required></v-text-field>
			<v-text-field v-model="formFields.phone" label="Phone" required></v-text-field>
			<v-text-field v-model="formFields.company_name" label="Company name" required></v-text-field>
			<v-text-field
				v-model="formFields.address.first_name"
				label="First name"
				:disabled="loading"
				required
			></v-text-field>
			<v-text-field
				v-model="formFields.address.last_name"
				label="Last name"
				:disabled="loading"
				required
			></v-text-field>
			<v-text-field v-model="formFields.address.street" label="Street" :disabled="loading" required></v-text-field>
			<v-text-field
				v-model="formFields.address.street2"
				label="Second Street"
				:disabled="loading"
				required
			></v-text-field>
			<v-text-field v-model="formFields.address.city" label="City" :disabled="loading" required></v-text-field>
			<v-text-field
				v-model="formFields.address.country_id"
				label="Country id"
				:disabled="loading"
				required
			></v-text-field>
			<v-text-field v-model="formFields.addresses.region" label="Region" :disabled="loading" required></v-text-field>
			<v-text-field
				v-model="formFields.address.postcode"
				label="Postcode"
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
			<v-text-field v-model="formFields.address.company" label="Company" :disabled="loading" required></v-text-field>
			<v-text-field v-model="formFields.address.vat_id" label="Vat id" :disabled="loading" required></v-text-field>
			<!-- <v-text-field
				v-model="formFields.address.deliverydate"
				label="Delivery date"
				:disabled="loading"
				required
			></v-text-field>-->
			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
	</div>
</template> 
<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	props: {
		model: Object || undefined
	},
	data() {
		return {
			defaultValues: { address: {} },
			formFields: {
				first_name: null,
				last_name: null,
				email: null,
				phone: null,
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
		if (this.$props.model) {
			this.formFields = this.$props.model;
		}
	},
	methods: {
		submit() {
			let payload = {
				model: "customers",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.clear();
				this.$emit("submit", "customers");
			});
		},
		clear() {
			this.formFields = { ...this.defaultValues };
			// this.formFields.first_name = null;
			// this.formFields.last_name = null;
			// this.formFields.email = null;
			// this.formFields.phone = null;
			// this.formFields.company_name = null;
			// this.formFields.addresses.area_code_id = null;
			// this.formFields.addresses.first_name = null;
			// this.formFields.addresses.last_name = null;
			// this.formFields.addresses.street = null;
			// this.formFields.addresses.city = null;
			// this.formFields.addresses.country_id = null;
			// this.formFields.addresses.region = null;
			// this.formFields.addresses.postcode = null;
			// this.formFields.addresses.phone = null;
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