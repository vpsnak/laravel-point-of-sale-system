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
		<v-text-field v-model="formFields.photo_url" label="Photo url" required></v-text-field>
		<v-text-field v-model="formFields.description" label="Description" required></v-text-field>
		<v-text-field type="number" v-model="formFields.final_price" label="Final price" required></v-text-field>
		<v-select
			:loading="loading"
			v-model="formFields.categories"
			:items="categories"
			chips
			label="Categories"
			multiple
			item-text="name"
			item-value="id"
			clearable
		></v-select>
		<v-row v-if="this.formFields.stores">
			<!--			mporeis na valeis item, index gia na exeis access se poia epanalipsi eisai => na ksereis pio entry na peirakseis-->
			<v-col :key="store.id" v-for="(store, index) in formFields.stores">
				<v-card>
					<v-card-title class="blue-grey pa-0" @click.stop>
						<h6 class="px-2">{{store.name}}</h6>
					</v-card-title>
					<v-text-field
						type="number"
						label="Qty"
						:value="formFields.stores[index].pivot.qty"
						v-model="formFields.stores[index].pivot.qty"
						required
					></v-text-field>
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
				url: null,
				description: null,
				categories: [],
				stores: []
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
			console.log(this.formFields.categories);
			if (
				this.formFields.categories != null &&
				typeof this.formFields.categories[0] === "object"
			) {
				let category_ids = [];
				for (const category of this.formFields.categories) {
					category_ids.push(category.id);
				}
				this.formFields.categories = category_ids;
			}
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
		clear() {
			this.formFields = { ...this.defaultValues };
		},
		getAllCategories() {
			this.loading = true;
			this.getAll({
				model: "categories"
			}).then(categories => {
				this.categories = categories;
				this.loading = false;
			});
		},
		getAllStores() {
			this.getAll({
				model: "stores"
			}).then(stores => {
				// india gia na exoume default value sto qty twn stores
				// @TODO fix object assign on product edit
				stores = stores.map(item => {
					return (item = { ...item, ...{ pivot: { qty: 0 } } });
				});
				if (
					this.formFields.stores === undefined ||
					this.formFields.stores.length == 0
				) {
					this.formFields.stores = stores;
				}
				// reset default values after getting and setting the stores
				this.defaultValues = { ...this.formFields };
			});
		},

		// getFromProductsStoreQty() {
		// 	for (const stores of this.products) {
		// 		for (const store_qty of stores) {
		// 			console.log(this.store_qty.pivot.store_id);
		// 		}
		// 	}
		// },
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
