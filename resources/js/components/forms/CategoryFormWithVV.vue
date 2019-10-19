<template>
	<v-form>
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-bars</v-icon>Category Form
			</v-chip>
		</div>
		<v-text-field
			v-model="formFields.name"
			v-validate="'required|max:10'"
			label="Name"
			data-vv-name="name"
			required
		></v-text-field>
		<v-switch
			v-model="formFields.in_product_listing"
			v-validate="'required'"
			label="In Product listing"
			data-vv-name="email"
		></v-switch>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn class="mr-4" @click="clear">clear</v-btn>
		<v-btn @click="edit">edit</v-btn>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";

import VeeValidate from "vee-validate";

export default {
	$_veeValidate: {
		validator: "new"
	},
	props: {
		model: Object || undefined
	},
	data() {
		return {
			defaultValues: {},
			formFields: {
				name: "",
				in_product_listing: false
			},
			dictionary: {
				attributes: {
					email: "E-mail Address"
					// custom attributes
				},
				custom: {
					name: {
						required: () => "Name can not be empty",
						max:
							"The name field may not be greater than 10 characters"
						// custom messages
					},
					select: {
						required: "Select field is required"
					}
				}
			}
		};
	},
	mounted() {
		this.$validator.localize("en", this.dictionary);
		this.defaultValues = this.formFields;
		if (this.$props.model) {
			this.formFields = {
				...this.$props.model
			};
		}
	},
	methods: {
		submit() {
			this.$validator.validateAll();

			let payload = {
				model: "categories",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.clear();
				this.$emit("submit", "categories");
			});
		},
		beforeDestroy() {
			this.$off("submit");
		},
		clear() {
			this.formFields = { ...this.defaultValues };
			this.$validator.reset();
		},
		edit() {
			this.getOne({
				model: "categories",
				data: {
					id: 2
				}
			}).then(category => {
				this.formFields = { ...category };
			});
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
