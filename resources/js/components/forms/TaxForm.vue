<template>
	<v-form @submit="submit">
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-money-bill-wave</v-icon>Tax Form
			</v-chip>
		</div>
		<v-text-field label="Name" v-model="formFields.name" :disabled="loading" required></v-text-field>
		<v-text-field
			label="Percentage"
			v-model="formFields.percentage"
			type="number"
			:min="0"
			:disabled="loading"
			required
		></v-text-field>
		<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
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
			loading: false,
			defaultValues: {},
			formFields: {
				name: "",
				percentage: ""
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
				model: "taxes",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "taxes",
						notification: {
							msg: "Tax added successfully",
							type: "success"
						}
					});
					this.clear();
				})
				.finally(() => {
					this.loading = false;
				});
		},
		beforeDestroy() {
			this.$off("submit");
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
