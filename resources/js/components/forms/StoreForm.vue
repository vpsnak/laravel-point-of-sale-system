<template>
	<div>
		<v-form @submit="submit">
			<div class="text-center">
				<v-chip color="blue-grey" label>
					<v-icon left>fas fa-warehouse</v-icon>Store Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.name" :counter="30" label="Name" :disabled="loading" required></v-text-field>
			<v-row justify="space-around">
				<v-switch :disabled="loading" v-model="formFields.taxable" label="Taxable"></v-switch>
				<v-switch :disabled="loading" v-model="formFields.is_default" label="Default"></v-switch>
			</v-row>
			<v-select
				v-model="formFields.tax_id"
				:items="taxes"
				label="Taxes"
				required
				:disabled="loading"
				item-text="name"
				item-value="id"
			></v-select>
			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
			<v-btn v-if="this.model === undefined" @click="clear">clear</v-btn>
		</v-form>
	</div>
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
			valid: true,
			taxes: [],
			defaultValues: {},
			formFields: {
				name: null,
				tax_id: null,
				taxable: false,
				is_default: false,
				created_by: null
			}
		};
	},
	mounted() {
		this.getAllTaxes();
		this.defaultValues = { ...this.formFields };
		if (this.$props.model) {
			this.formFields = {
				...this.$props.model
			};
		}
	},
	computed: {
		user_id: {
			get() {
				return this.$store.state.user.id;
			}
		}
	},
	methods: {
		submit() {
			let payload = {
				model: "stores",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					if (this.formFields.id == this.$store.state.store.id) {
						this.$store.state.store = this.formFields;
					}
					this.clear();
					this.$emit("submit", {
						getRows: true,
						model: "stores",
						notification: {
							msg: "Store added successfully",
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
			this.formFields = this.defaultValues;
		},
		getAllTaxes() {
			this.loading = true;
			this.getAll({
				model: "taxes"
			})
				.then(response => {
					this.taxes = response;
				})
				.finally(() => {
					this.loading = false;
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
