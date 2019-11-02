<template>
	<div>
		<v-form @submit="submit">
			<v-text-field v-model="formFields.first_name" label="First name" required></v-text-field>
			<v-text-field v-model="formFields.last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.email" label="Email" required></v-text-field>
			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
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
				id: null,
				first_name: null,
				last_name: null,
				email: null
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