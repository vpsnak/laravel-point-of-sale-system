<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-cash-register</v-icon>Cash Register Form
				</v-chip>
			</div>
			<v-text-field v-model="name" label="Name" required></v-text-field>
			<v-select
				v-model="store_id"
				:items="stores"
				:rules="[v => !!v || 'Store is required']"
				label="Stores"
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
		data: () => ({
			savingSuccessful: false,
			name: null,
			store_id: null,
			stores: []
		}),
		mounted() {
			this.getAll({
				model: "stores"
			}).then(stores => {
				this.stores = stores;
			});
		},
		methods: {
			submit() {
				let payload = {
					model: "stores",
					data: {
						name: this.name,
						store_id: this.tax_id
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
				this.store_id = null;
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
