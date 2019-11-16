<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form @submit.prevent="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-box-open</v-icon>Product Form
				</v-chip>
			</div>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Name">
				<v-text-field v-model="formFields.name" label="Name" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Sku">
				<v-text-field v-model="formFields.sku" label="Sku" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="max:191" v-slot="{ errors, valid }" name="Url">
				<v-text-field v-model="formFields.url" label="Url" :error-messages="errors" :success="valid"></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="max:191" v-slot="{ errors, valid }" name="Photo url">
				<v-text-field
					v-model="formFields.photo_url"
					label="Photo url"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>

			<ValidationProvider rules="max:65535" v-slot="{ errors, valid }" name="Description">
				<v-text-field
					v-model="formFields.description"
					label="Description"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>

			<ValidationProvider
				rules="max:8|decimal|max_value:10"
				v-slot="{ errors, valid }"
				name="Final price"
			>
				<v-text-field
					type="number"
					v-model="formFields.final_price"
					label="Final price"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
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

			<v-row v-if="formFields.stores">
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
			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="invalid || loading">submit</v-btn>
			<v-btn v-if="!model" @click="clear">clear</v-btn>
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

			for (let index in this.formFields.stores) {
				if (this.formFields.stores.hasOwnProperty(index)) {
					this.formFields.stores[index].pivot.qty = null;
				}
			}
		},
		getAllCategories() {
			this.loading = true;
			this.getAll({
				model: "categories"
			})
				.then(categories => {
					this.categories = categories;
				})
				.finally(() => {
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