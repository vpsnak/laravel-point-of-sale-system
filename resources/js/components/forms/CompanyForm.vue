<template>
	<ValidationObserver v-slot="{ invalid }" ref="giftcardObs">
		<v-form @submit.prevent="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>mdi-domain</v-icon>CompanyForm
				</v-chip>
			</div>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Names">
				<v-text-field v-model="formFields.name" label="Name" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider
				:rules="{ required : true, min:8 , max:100, regex:/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/g}"
				v-slot="{ errors, valid }"
				name="Phone"
			>
				<v-text-field
					v-model="formFields.phone"
					label="Phone"
					:min="0"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Street">
				<v-text-field
					v-model="formFields.street"
					label="Street"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Postal code">
				<v-text-field
					v-model="formFields.postal_code"
					label="Postal code"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="City">
				<v-text-field v-model="formFields.city" label="City" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>

			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="invalid || loading">submit</v-btn>
			<v-btn v-if="!model" @click="clear">clear</v-btn>
		</v-form>
	</ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object || undefined
	},
	data() {
		return {
			loading: false,
			defaultValues: {},
			formFields: {
				name: null,
				phone: null,
				street: null,
				postal_code: null,
				city: null,
				logo: null
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
				model: "companies",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "companies",
						notification: {
							msg: "Company added successfully",
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
