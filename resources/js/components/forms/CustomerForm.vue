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
			<v-text-field v-model="formFields.addresses.area_code_id" label="Area Code id" required></v-text-field>
			<v-text-field v-model="formFields.addresses.first_name" label="First name" required></v-text-field>
			<v-text-field v-model="formFields.addresses.last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.addresses.street" label="Street" required></v-text-field>
			<v-text-field v-model="formFields.addresses.city" label="City" required></v-text-field>
			<v-text-field v-model="formFields.addresses.country_id" label="Country id" required></v-text-field>
			<v-text-field v-model="formFields.addresses.region" label="Region" required></v-text-field>
			<v-text-field v-model="formFields.addresses.postcode" label="Postcode" required></v-text-field>
			<v-text-field v-model="formFields.addresses.phone" label="Phone" required></v-text-field>

			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
	</div>
</template>
<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	// props: {
	// 	model: Object || undefined
	// },
	data() {
		return {
			defaultValues: {},
			formFields: {
				first_name: null,
				last_name: null,
				company_name: null,
				email: null,
				phone: null,
				addresses: {
					area_code_id: null,
					last_name: "",
					first_name: "",
					street: "",
					city: "",
					country_id: null,
					region: null,
					postcode: null,
					phone: null
				}
			}
		};
	},
	// mounted() {
	// 	this.defaultValues = this.formFields;
	// 	if (this.$props.model) {
	// 		this.formFields = {
	// 			...this.$props.model
	// 		};
	// 	}
	// },
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
			// this.formFields = { ...this.defaultValues };
			this.formFields.first_name = null;
			this.formFields.last_name = null;
			this.formFields.email = null;
			this.formFields.phone = null;
			this.formFields.company_name = null;
			this.formFields.addresses.area_code_id = null;
			this.formFields.addresses.first_name = null;
			this.formFields.addresses.last_name = null;
			this.formFields.addresses.street = null;
			this.formFields.addresses.city = null;
			this.formFields.addresses.country_id = null;
			this.formFields.addresses.region = null;
			this.formFields.addresses.postcode = null;
			this.formFields.addresses.phone = null;
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