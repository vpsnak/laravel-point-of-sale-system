<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form @submit.prevent="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-gifts</v-icon>Gift Card Form
				</v-chip>
			</div>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Names">
				<v-text-field v-model="formFields.name" label="Name" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Code">
				<v-text-field v-model="formFields.code" label="Code" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<v-switch v-model="formFields.enabled" label="Enabled"></v-switch>
			<ValidationProvider
				:rules="{
					required,
					regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
					}"
				v-slot="{ errors, valid }"
				name="Amount"
			>
				<v-text-field
					v-model="formFields.amount"
					type="number"
					label="Amount"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
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
				code: null,
				enabled: false,
				amount: null
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
				model: "gift-cards",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "gift-cards",
						notification: {
							msg: "Gift card added successfully",
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
