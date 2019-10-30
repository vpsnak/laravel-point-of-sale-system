<template>
	<v-form @submit="submit">
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-box-open</v-icon>Product Form
			</v-chip>
		</div>
		<v-text-field v-model="formFields.name" label="Name" required></v-text-field>
		<v-text-field v-model="formFields.sku" label="Sku" required></v-text-field>
		<v-text-field v-model="formFields.url" label="Url" required></v-text-field>
		<v-text-field v-model="formFields.photo_url" label="Phot url" required></v-text-field>
		<v-text-field v-model="formFields.description" label="Description" required></v-text-field>
		<v-text-field type="number" v-model="formFields.stock" label="Stock" required></v-text-field>
		<v-text-field type="number" v-model="formFields.final_price" label="Final price" required></v-text-field>
		<v-select
			v-model="formFields.category_ids"
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
		<v-row v-if="stores.length">
			<v-col v-for="store in stores" :key="store.id">
				<v-card>
					<v-card-title class="blue-grey pa-0" @click.stop>
						<h6 class="px-2">{{store.name}}</h6>
					</v-card-title>
					<v-text-field type="number" v-model="formFields.store.pivot.qty" label="Qty" required></v-text-field>
				</v-card>
			</v-col>
		</v-row>
		<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
		<v-btn v-if="this.model === undefined" @click="clear">clear</v-btn>
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
			categories: [],
			selectedCategories: [],
			stores: [],
			defaultValues: {},
			formFields: {
				name: "",
				sku: null,
				photo_url: null,
				final_price: null,
				stock: null,
				url: null,
				description: null,
				category_ids: [],
				store_id: null,
				store: {
					pivot: {
						product_id: null,
						store_id: null,
						qty: null
					}
				}
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
			this.loading = true;
			let payload = {
				model: "products",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "products",
						notification: {
							msg: "Product added successfully",
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
