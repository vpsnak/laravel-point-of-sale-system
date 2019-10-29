<template>
	<div>
		<v-form @submit="submit">
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-ticket-alt</v-icon>Coupon Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.name" label="Name" :disabled="loading" required></v-text-field>
			<v-text-field v-model="formFields.code" label="Code" :disabled="loading" required></v-text-field>
			<v-text-field v-model="formFields.uses" type="number" label="Uses" :disabled="loading" required></v-text-field>
			<v-select
				v-model="formFields.discount.type"
				label="Discount type"
				:items="discount_types"
				:disabled="loading"
				item-value="id"
				required
			></v-select>
			<v-text-field
				v-model="formFields.discount.amount"
				type="number"
				label="Discount amount"
				:disabled="loading"
				required
			></v-text-field>
			<v-menu
				v-model="menu1"
				:close-on-content-click="false"
				:nudge-right="40"
				transition="scale-transition"
				offset-y
				min-width="290px"
			>
				<template v-slot:activator="{ on }">
					<v-text-field v-model="formFields.from" label="From:" prepend-icon="event" readonly v-on="on"></v-text-field>
				</template>
				<v-date-picker v-model="formFields.from" @input="menu1 = false"></v-date-picker>
			</v-menu>
			<v-menu
				v-model="menu2"
				:close-on-content-click="false"
				:nudge-right="40"
				transition="scale-transition"
				offset-y
				min-width="290px"
			>
				<template v-slot:activator="{ on }">
					<v-text-field v-model="formFields.to" label="To:" prepend-icon="event" readonly v-on="on"></v-text-field>
				</template>
				<v-date-picker v-model="formFields.to" @input="menu2 = false"></v-date-picker>
			</v-menu>

			<v-btn class="mr-4" :loading="loading" :disabled="loading" @click="submit">submit</v-btn>
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
			discount_types: ["flat", "percentage"],
			formFields: {
				name: null,
				code: null,
				uses: null,
				discount: {
					type: null,
					amount: null
				},
				from: null,
				to: null
			},
			menu1: false,
			menu2: false
		};
	},
	mounted() {
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
				model: "coupons",
				data: { ...this.formFields }
			};
			this.create(payload)
				.then(() => {
					this.$emit("submit", {
						getRows: true,
						model: "coupons",
						notification: {
							msg: "Coupon added successfully",
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
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	}
};
</script>
