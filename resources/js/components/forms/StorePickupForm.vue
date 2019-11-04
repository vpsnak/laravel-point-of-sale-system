<template>
	<v-form @submit="submit">
		<v-text-field v-model="formFields.name" label="Name" :disabled="loading" required></v-text-field>
		<v-select
			v-model="formFields.region_id"
			:items="regions"
			label="Regions"
			required
			item-text="default_name"
			item-value="region_id"
		></v-select>
		<v-select
			v-model="formFields.country_id"
			:items="countries"
			label="Countries"
			required
			item-text="iso2_code"
			item-value="iso2_code"
		></v-select>
		<v-text-field v-model="formFields.street" label="Street" :disabled="loading" required></v-text-field>
		<v-text-field v-model="formFields.street1" label="Second Street" :disabled="loading" required></v-text-field>
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
			defaultValues: {},
			countries: [],
			regions: [],
			formFields: {
				name: null,
				street: null,
				street1: null,
				region_id: null,
				country_id: null
			}
		};
	},
	mounted() {
		this.getAll({
			model: "regions"
		}).then(regions => {
			this.regions = regions;
		});
		this.getAll({
			model: "countries"
		}).then(countries => {
			this.countries = countries;
		});
		this.defaultValues = { ...this.formFields };
		if (this.$props.model) {
			this.formFields = {
				...this.$props.model
			};
		}
	},
	beforeDestroy() {
		this.$off("submit");
	},
	methods: {
		submit() {
			this.loading = true;
			let payload = {
				model: "store-pickups",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "store-pickups",
						notification: {
							msg: "Store pickup added successfully!",
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

		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	}
};
</script>