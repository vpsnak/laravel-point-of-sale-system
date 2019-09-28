<template>
	<v-form v-model="valid">
		<div class="text-center">
			<v-chip color="blue-grey darken-4" label>
				<v-icon left>fas fa-warehouse</v-icon>Store Form
			</v-chip>
		</div>
		<v-text-field v-model="name" :rules="nameRules" :counter="10" label="Name" required></v-text-field>
		<v-row justify="space-around">
			<v-switch v-model="taxable" :label="`Taxable : ${taxable.toString()}`"></v-switch>
			<v-switch v-model="is_default" :label="`Default : ${is_default.toString()}`"></v-switch>
		</v-row>
		<v-select
			v-model="tax"
			:items="taxes"
			:rules="[v => !!v || 'Tax is required']"
			label="Taxes"
			required
		></v-select>

		<v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
	import { mapActions, mapState, mapGetters } from "vuex";
	export default {
		data: () => ({
			tax: null,
			valid: false,
			name: "",
			taxable: false,
			is_default: false,
			taxes: [],
			nameRules: [
				v => !!v || "Name is required",
				v => v.length <= 10 || "Name must be less than 10 characters"
			]
		}),
		mounted() {
			this.getAll({
				model: "taxes"
			}).then(taxes => {
				for (let tax in taxes) {
					this.taxes.push(tax);
				}
			});
		},
		methods: {
			submit() {
				let payload = {
					model: "stores",
					data: {
						name: this.name,
						taxable: this.taxable,
						is_default: this.is_default,
						tax_id: this.tax
					}
				};
				this.create(payload).then(() => {
					console.log("To phre");
				});
			},
			clear() {
				this.name = "";
				this.taxable = false;
				this.isdefault = false;
				this.taxt = null;
			},
			getAllTaxes() {
				this.getAll({
					model: "taxes"
				}).then(taxes => {
					this.taxes = taxes;
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