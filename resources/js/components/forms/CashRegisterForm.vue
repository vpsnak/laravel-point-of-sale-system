<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form @submit.prevent="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-cash-register</v-icon>Cash Register Form
				</v-chip>
			</div>
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Name">
				<v-text-field
					v-model="formFields.name"
					label="Name"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required" v-slot="{ errors, valid }" name="Stores">
				<v-select
					v-model="formFields.store_id"
					label="Stores"
					:items="stores"
					item-text="name"
					item-value="id"
					:disabled="loading"
					:error-messages="errors"
					:success="valid"
				></v-select>
			</ValidationProvider>

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
			})
				.then(response => {
					this.stores = response;
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
