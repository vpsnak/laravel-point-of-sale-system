<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-gifts</v-icon>Gift Card Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.name" label="Name" required></v-text-field>
			<v-text-field v-model="formFields.code" label="Code" required></v-text-field>
			<v-switch v-model="formFields.enabled" :label="`Enabled : ${formFields.enabled.toString()}`"></v-switch>
			<v-text-field v-model="formFields.amount" type="number" label="Amount" required></v-text-field>

			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
	</div>
</template>
<script>
	import { mapActions } from "vuex";

	export default {
		props: {
			model: Object || undefined
		},
		data() {
			return {
				savingSuccessful: false,
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
					model: "gift-cards",
					data: { ...this.formFields }
				};
				this.create(payload).then(() => {
					this.clear();
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
		}
	};
</script>
