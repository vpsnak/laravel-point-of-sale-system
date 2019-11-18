<template>
	<ValidationObserver v-slot="{ invalid }" ref="categoryObs">
		<v-form @submit.prevent="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-bars</v-icon>Category Form
				</v-chip>
			</div>
			<ValidationProvider rules="required|min:3|max:191" v-slot="{ errors, valid }" name="Name">
				<v-text-field
					:error-messages="errors"
					:success="valid"
					v-model="formFields.name"
					label="Name"
					required
				></v-text-field>
			</ValidationProvider>
			<v-switch v-model="formFields.in_product_listing" label="In Product listing"></v-switch>
			<v-btn
				class="mr-4"
				type="submit"
				@click.prevent="submit"
				:disabled="invalid || disableSubmit"
			>submit</v-btn>
			<v-btn v-if="!model" class="mr-4" @click="clear">clear</v-btn>
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
			defaultValues: {},
			formFields: {
				name: "",
				in_product_listing: false
			}
		};
	},
	computed: {
		disableSubmit() {
			return this.formFields.name ? false : true;
		}
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
			let payload = {
				model: "categories",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.$emit("submit", {
					getRows: true,
					model: "categories",
					notification: {
						msg: "Category added successfully",
						type: "success"
					}
				});
				this.clear();
			});
		},
		clear() {
			this.$refs.categoryObs.reset();
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
