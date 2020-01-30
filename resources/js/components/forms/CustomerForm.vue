<template>
	<ValidationObserver v-slot="{ invalid }" ref="customerObs">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="First name">
				<v-text-field
					v-model="formFields.first_name"
					label="First name"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:100" v-slot="{ errors, valid }" name="Last Name">
				<v-text-field
					v-model="formFields.last_name"
					label="Last name"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
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
				<ValidationProvider vid="no_tax">
					<v-switch v-model="formFields.no_tax" label="Zero tax"></v-switch>
				</ValidationProvider>
			</v-row>

			<v-row justify="space-around" align-center>
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
						v-if="formFields.house_account_status"
						:rules="{
                            required: true,
                            regex: /^[\d]{1,7}(\.[\d]{1,4})?$/g
                        }"
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
			<v-row v-if="formFields.no_tax" align="center">
				<v-col :cols="6">
					<ValidationProvider
						rules="ext:jpg,png,jpeg,pdf"
						v-slot="{ errors, valid }"
						name="Certification file"
					>
						<v-file-input
							v-model="formFields.file"
							accept="image/png, image/jpeg, application/pdf"
							show-size
							label="Upload new certification file"
							clearable
							:error-messages="errors"
							:success="valid"
						></v-file-input>
					</ValidationProvider>
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
			<ValidationProvider rules="max:65535" v-slot="{ errors, valid }" name="Comment">
				<v-textarea
					rows="3"
					v-model="formFields.comment"
					label="Comments"
					:error-messages="errors"
					:success="valid"
				></v-textarea>
			</ValidationProvider>
			<v-row>
				<v-col cols="12" align="center" justify="center">
					<v-btn
						color="secondary"
						class="mr-4"
						type="submit"
						:loading="loading"
						:disabled="invalid || loading"
					>submit</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</ValidationObserver>
</template>
<script>
import { mapActions, mapState, mapGetters } from "vuex";

export default {
	props: {
		model: Object
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
			if (this.formFields.house_account_status) {
				data.append(
					"house_account_number",
					this.formFields.house_account_number
				);
				data.append(
					"house_account_limit",
					this.formFields.house_account_limit || 0
				);
				data.append(
					"house_account_status",
					this.formFields.house_account_status ? 1 : 0
				);
			}
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
				})
				.finally(() => {
					this.loading = false;
				});
		},
		clear() {
			this.formFields = { ...this.defaultValues };
		},
		...mapActions({
			create: "create"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
