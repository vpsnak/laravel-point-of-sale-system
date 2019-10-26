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
			<v-text-field v-model="formFields.addresses.area_code_id" counter label="Area Code id" required></v-text-field>
			<v-text-field v-model="formFields.addresses.first_name" counter label="First name" required></v-text-field>
			<v-text-field v-model="formFields.addresses.last_name" counter label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.addresses.street" counter label="Street" required></v-text-field>
			<v-text-field v-model="formFields.addresses.city" counter label="City" required></v-text-field>
			<v-text-field v-model="formFields.addresses.country_id" counter label="Country id" required></v-text-field>
			<v-text-field v-model="formFields.addresses.region" counter label="Region" required></v-text-field>
			<v-text-field v-model="formFields.addresses.postcode" counter label="Postcode" required></v-text-field>
			<v-text-field v-model="formFields.addresses.phone" counter label="Phone" required></v-text-field>

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
			addressClear: false,
			savingSuccessful: false,
			defaultValues: {},
			formFields: {
				first_name: null,
				last_name: null,
				company_name: null,
				email: null,
				phone: null,
				addresses: {
					area_code_id: null,
					last_name: null,
					first_name: null,
					street: null,
					city: null,
					country_id: null,
					region: null,
					postcode: null,
					phone: null
				}
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
			console.log(this.formFields);
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