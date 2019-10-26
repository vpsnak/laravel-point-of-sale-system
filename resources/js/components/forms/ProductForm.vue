<template>
	<v-form>
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-box-open</v-icon>Product Form
			</v-chip>
		</div>
		<v-text-field v-model="formFields.name" counter label="Name" required></v-text-field>
		<v-text-field v-model="formFields.sku" counter label="Sku" required></v-text-field>
		<v-text-field v-model="formFields.photo_url" counter label="Photo url" required></v-text-field>
		<v-text-field type="number" v-model="formFields.final_price" counter label="Final price" required></v-text-field>
		<v-text-field type="number" v-model="formFields.stock" counter label="Stock" required></v-text-field>
		<!-- <v-combobox
			v-model="selectedCategories"
			:items="categories"
			item-text="name"
			hide-selected
			clearable
			chips
			label="Categories"
			multiple
		></v-combobox>-->
		<v-select
			v-model="selectedCategories"
			:items="categories"
			chips
			label="Categories"
			multiple
			item-text="name"
			item-value="id"
			clearable
		></v-select>
		<v-select
			v-model="formFields.store_id"
			:items="stores"
			label="Stores"
			required
			clearable
			item-text="name"
			item-value="id"
		></v-select>
		<v-btn class="mr-4" @click="submit">submit</v-btn>
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
			categories: [],
			selectedCategories: [],
			taxes: [],
			stores: [],
			defaultValues: {},
			formFields: {
				name: "",
				sku: null,
				photo_url: null,
				final_price: null,
				stock: null,
				tax_id: null,
				store_id: null
			}
		};
	},
	mounted() {
		this.getAllCategories();
		this.getAllStores();

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
				model: "products",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.clear();
				this.$emit("submit", "products");
			});
		},
		beforeDestroy() {
			this.$off("submit");
		},
		clear() {
			this.formFields = { ...this.defaultValues };
		},
		getAllCategories() {
			this.getAll({
				model: "categories"
			}).then(categories => {
				this.categories = categories;
			});
		},
		getAllStores() {
			this.getAll({
				model: "stores"
			}).then(stores => {
				this.stores = stores;
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
