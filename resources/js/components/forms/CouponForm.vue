<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-ticket-alt</v-icon>Coupon Form
				</v-chip>
			</div>
			<v-text-field v-model="formFields.name" label="Name" required></v-text-field>
			<v-text-field v-model="formFields.code" label="Code" required></v-text-field>
			<v-text-field v-model="formFields.uses" label="Uses" required></v-text-field>
			<v-text-field v-model="formFields.discount_id" type="number" label="Discount ID" required></v-text-field>
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

			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
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
				defaultValues: {},
				savingSuccessful: false,
				formFields: {
					name: null,
					code: null,
					uses: null,
					discount_id: null,
					from: null,
					to: null
				},
				menu1: false,
				menu2: false
			};
		},
		mounted() {
			this.defaultValues = this.formFields;
			if (this.$props.model) {
				this.formFields = {
					...this.$props.model
				};
			}
		},
		methods: {
			submit() {
				let payload = {
					model: "coupons",
					data: { ...this.formFields }
				};
				this.create(payload).then(() => {
					this.clear();
					window.location.reload();
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
