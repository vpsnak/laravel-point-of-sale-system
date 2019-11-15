<template>
	<div>
		<v-form @submit.prevent="submit">
			<v-text-field v-model="formFields.first_name" label="First name" required></v-text-field>
			<v-text-field v-model="formFields.last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="formFields.email" label="Email" required></v-text-field>
			<v-row justify="space-around">
				<v-switch v-model="formFields.house_account_status" label="Has house account"></v-switch>
				<v-switch v-model="formFields.no_tax" label="Zero tax"></v-switch>
			</v-row>
			<v-row justify="space-around" align-center>
				<v-col v-if="formFields.house_account_status">
					<v-text-field v-model="formFields.house_account_number" label="House account number" required></v-text-field>
					<v-text-field
						type="number"
						v-model="formFields.house_account_limit"
						label="House account limit"
						required
					></v-text-field>
				</v-col>
			</v-row>
			<v-row v-if="formFields.no_tax" align="center">
				<v-col :cols="6">
					<v-file-input
						v-model="formFields.file"
						accept="image/png, image/jpeg, document/pdf"
						show-size
						label="Upload new certification file"
						clearable
					></v-file-input>
				</v-col>

				<v-col v-if="formFields.no_tax_file" :cols="6">
					<v-btn
						text
						v-if="formFields.no_tax_file"
						:href="formFields.no_tax_file"
						target="_blank"
					>View uploaded file</v-btn>
				</v-col>
			</v-row>
			<v-textarea rows="3" v-model="formFields.comment" label="Comments"></v-textarea>
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
				first_name: null,
				last_name: null,
				email: null,
				house_account_number: null,
				house_account_limit: null,
				house_account_status: false,
				no_tax: false,
				file: null,
				no_tax_file: null,
				comment: null
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
		makeFormData() {
			let data = new FormData();

			data.append("id", this.model.id);
			data.append("first_name", this.formFields.first_name);
			data.append("last_name", this.formFields.last_name);
			data.append("email", this.formFields.email);
			data.append("house_account_number", this.formFields.house_account_number);
			data.append(
				"house_account_limit",
				this.formFields.house_account_limit || 0
			);
			data.append(
				"house_account_status",
				this.formFields.house_account_status ? 1 : 0
			);
			data.append("no_tax", this.formFields.no_tax ? 1 : 0);
			data.append("file", this.formFields.file);
			data.append("comment", this.formFields.comment);

			return data;
		},
		submit() {
			this.loading = true;
			let payload = {
				model: "customers",
				data: { ...this.formFields }
			};

			if (payload.data.file && payload.data.no_tax) {
				payload.data = this.makeFormData();
			}

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