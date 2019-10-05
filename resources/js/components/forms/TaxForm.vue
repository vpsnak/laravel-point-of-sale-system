<template>
	<v-form>
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-money-bill-wave</v-icon>Tax Form
			</v-chip>
		</div>
		<v-text-field v-model="formFields.name" counter label="Name" required></v-text-field>
		<v-text-field v-model="formFields.percentage" counter label="Percentage" required></v-text-field>
		<v-switch
			v-model="formFields.is_default"
			:label="`Default : ${formFields.is_default.toString()}`"
		></v-switch>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
	</v-form>
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
					name: "",
					percentage: "",
					is_default: false
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
					model: "taxes",
					data: {
						name: this.name,
						percentage: this.percentage,
						is_default: this.is_default
					}
				};
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
					window.location.reload();
				});
			},

			submit() {
				let payload = {
					model: "taxes",
					data: { ...this.formFields }
				};
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
					window.location.reload();
				});
			},
			clear() {
				this.formFields = { ...this.defaultValues };
			},
			...mapActions({
				create: "create"
			})
		}
	};
</script>
