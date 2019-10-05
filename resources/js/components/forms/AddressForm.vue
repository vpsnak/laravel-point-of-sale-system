<template>
	<v-form>
		<div class="text-center">
			<v-chip color="primary" label>
				<v-icon left>fas fa-address-card</v-icon>Address Form
			</v-chip>
		</div>
		<v-text-field v-model="address.area_code_id" counter label="Area Code id" required></v-text-field>
		<v-text-field v-model="address.first_name" counter label="First name" required></v-text-field>
		<v-text-field v-model="address.last_name" counter label="Last name" required></v-text-field>
		<v-text-field v-model="address.street" counter label="Street" required></v-text-field>
		<v-text-field v-model="address.city" counter label="City" required></v-text-field>
		<v-text-field v-model="address.country_id" counter label="Country id" required></v-text-field>
		<v-text-field v-model="address.region" counter label="Region" required></v-text-field>
		<v-text-field v-model="address.postcode" counter label="Postcode" required></v-text-field>
		<v-text-field v-model="address.phone" counter label="Phone" required></v-text-field>
		<!-- <v-btn class="mr-4" @click="submit">submit</v-btn>
		<v-btn @click="clear">clear</v-btn>-->
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
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
				savingSuccessful: false,
				defaultValues: {},
				address: {
					area_code_id: "",
					last_name: "",
					first_name: "",
					street: "",
					city: "",
					country_id: "",
					region: "",
					postcode: "",
					phone: ""
				}
			};
		},
		mounted() {
			this.defaultValues = this.address;
			if (this.$props.model) {
				this.address = {
					...this.$props.model
				};
			}
		},
		watch: {
			address: {
				handler() {
					this.$emit("address", this.address);
				},
				deep: true
			}
		},
		beforeDestroy() {
			this.$off("address", this.address);
		},
		methods: {
			submit() {
				let payload = {
					model: "address",
					data: { ...this.address }
				};
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
					window.location.reload();
				});
			},
			clear() {
				this.address = { ...this.defaultValues };
			},

			...mapActions({
				create: "create"
			})
		}
	};
</script>