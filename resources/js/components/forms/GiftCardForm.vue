<template>
	<ValidationObserver v-slot="{ invalid }">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Names">
				<v-text-field
					:readonly="$props.readonly"
					v-model="formFields.name"
					label="Name"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Code">
				<v-text-field
					:readonly="$props.readonly"
					v-model="formFields.code"
					label="Code"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider
				:rules="{
                    required: true,
                    regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g,
                    max_value: 99999
                }"
				v-slot="{ errors, valid }"
				name="Amount"
			>
				<v-text-field
					:readonly="$props.readonly"
					v-model="formFields.amount"
					type="number"
					label="Amount"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<v-row justify="space-around">
				<v-col cols="6">
					<ValidationProvider vid="bulk_action">
						<v-switch :readonly="$props.readonly" v-model="bulk_action" label="Bulk Action"></v-switch>
					</ValidationProvider>
				</v-col>
				<v-col cols="6">
					<v-switch :readonly="$props.readonly" v-model="formFields.enabled" label="Enabled"></v-switch>
				</v-col>
			</v-row>
			<ValidationProvider
				rules="required_if:bulk_action|max:5|numeric"
				v-slot="{ errors, valid }"
				name="Qty"
			>
				<v-text-field
					:readonly="$props.readonly"
					v-if="bulk_action"
					type="number"
					v-model="qty"
					label="Qty"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<v-row v-if="!$props.readonly">
				<v-col cols="12" align="center" justify="center">
					<v-btn
						class="mr-4"
						type="submit"
						:loading="loading"
						:disabled="invalid || loading"
						color="secondary"
					>submit</v-btn>
					<v-btn v-if="!model" @click="clear" color="orange">clear</v-btn>
				</v-col>
			</v-row>
		</v-form>
	</ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object,
		readonly: Boolean
	},
	data() {
		return {
			bulk_action: false,
			qty: null,
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
		...mapActions({
			create: "create"
		}),

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
		}
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
