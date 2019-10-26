<template>
	<div>
		<v-form @submit="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-user</v-icon>Customer Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.first_name" label="First name" required></v-text-field>
			<v-text-field v-model="formFields.last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.email" label="Email" required></v-text-field>

			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
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
			loading: false,
			defaultValues: {},
			formFields: {
				first_name: null,
				last_name: null,
				email: null
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