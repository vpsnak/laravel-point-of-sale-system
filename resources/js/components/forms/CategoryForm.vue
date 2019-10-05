<template>
	<v-form v-model="valid">
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-bars</v-icon>Category Form
			</v-chip>
		</div>
		<v-text-field v-model="formFields.name" :rules="nameRules" :counter="13" label="Name" required></v-text-field>
		<v-switch
			v-model="formFields.in_product_listing"
			:label="`In Product listing : ${in_product_listing.toString()}`"
		></v-switch>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn class="mr-4" @click="clear">clear</v-btn>
		<v-btn @click="edit">edit</v-btn>
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
				in_product_listing: false,
				valid: false,
				name: "",
				nameRules: [
					// v => !!v || "Name is required",
					v => v.length <= 15 || "Name must be less than 10 characters"
				],
				defaultValues: {},
				formFields: {
					name: "",
					in_product_listing: false
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

		computed: {
			// edit() {
			// 	this.getOne({
			// 		model: "categories",
			// 		data: {
			// 			id: 2
			// 		}
			// 	}).then(category => {
			// 		this.name = this.category.name;
			// 		this.in_product_listing = this.category.in_product_listing;
			// 	});
			// }
		},
		methods: {
			submit() {
				let payload = {
					model: "categories",
					data: { ...this.formFields }
				};
				this.create(payload).then(() => {
					this.clear();
					window.location.reload();
				});
			},
			clear() {
				this.formFields = { ...this.defaultValues };
			},
			// edit() {
			// 	this.name = this.testObject.name;
			// 	this.in_product_listing = this.testObject.in_product_listing;
			// },

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
