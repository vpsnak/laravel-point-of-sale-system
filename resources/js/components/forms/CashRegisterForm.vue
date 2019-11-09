<template>
	<div>
		<v-form @submit="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-cash-register</v-icon>Cash Register Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.name" label="Name" :disabled="loading" required></v-text-field>
			<v-select
				v-model="formFields.store_id"
				label="Stores"
				:items="stores"
				item-text="name"
				item-value="id"
				:disabled="loading"
				required
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
			defaultValues: {},
			formFields: {
				name: null,
				store_id: null
			},
			stores: []
		};
	},
	mounted() {
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
				model: "cash-registers",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					if (
						this.formFields.id == this.$store.state.cashRegister.id
					) {
						this.$store.state.cashRegister = this.formFields;
					}
					this.$emit("submit", {
						getRows: true,
						model: "cash-registers",
						notification: {
							msg: "Cash Register added successfully",
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
		getAllStores() {
			this.loading = true;
			this.getAll({
				model: "stores"
			}).then(stores => {
				this.stores = stores;
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
