<template>
	<div>
		<v-form v-model="valid">
			<div class="text-center">
				<v-chip color="blue-grey" label>
					<v-icon left>fas fa-warehouse</v-icon>Store Form
				</v-chip>
			</div>
			<v-text-field v-model="name" :rules="nameRules" :counter="10" label="Name" required></v-text-field>
			<v-row justify="space-around">
				<v-switch v-model="taxable" :label="`Taxable : ${taxable.toString()}`"></v-switch>
				<v-switch v-model="is_default" :label="`Default : ${is_default.toString()}`"></v-switch>
			</v-row>
			<v-select
				v-model="tax_id"
				:items="taxes"
				:rules="[v => !!v || 'Tax is required']"
				label="Taxes"
				required
				item-text="name"
				item-value="id"
			></v-select>

			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
	</div>
</template>

<script>
	import { mapActions } from "vuex";

	export default {
		data() {
			return {
				savingSuccessful: false,
				tax_id: null,
				valid: true,
				name: "",
				taxable: false,
				is_default: false,
				created_by: null,
				taxes: [],
				nameRules: [
					// v => !!v || "Name is required",
					v => v.length <= 10 || "Name must be less than 10 characters"
				]
			};
		},
		mounted() {
			this.getAll({
				model: "taxes"
			}).then(taxes => {
				this.taxes = taxes;
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
						tax_id: this.tax_id,
						created_by: this.user_id
					}
				};
				console.log(this.tax);
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
					window.location.reload();
				});
			},
			clear() {
				this.name = "";
				this.taxable = false;
				this.isdefault = false;
				this.tax_id = null;
				this.valid = false;
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
		},
		computed: {
			user_id: {
				get() {
					return this.$store.state.user.id;
				}
			}
		}
	};
</script>
