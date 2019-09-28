<template>
	<div>
		<v-form>
			<div class="text-center">
				<v-chip color="primary" label>
					<v-icon left>fas fa-user</v-icon>Customer Form
				</v-chip>
			</div>
			<v-text-field v-model="first_name" label="First name" required></v-text-field>
			<v-text-field v-model="last_name" label="Last name" required></v-text-field>
			<v-text-field v-model="email" label="Email" required></v-text-field>
			<v-text-field v-model="phone" label="Phone" required></v-text-field>
			<v-text-field v-model="company_name" label="Company name" required></v-text-field>
			<addressForm @address="watchAddress($event)"></addressForm>
			<v-btn class="mr-4" @click="submit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
		<v-alert v-if="savingSuccessful === true" class="mt-4" type="success">Form submitted successfully!</v-alert>
	</div>
</template>
<script>
	import { mapActions, mapState, mapGetters } from "vuex";

	export default {
		data() {
			return {
				savingSuccessful: false,
				first_name: null,
				last_name: null,
				company_name: null,
				email: null,
				phone: null,
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

		computed: {},
		methods: {
			watchAddress(current_address) {
				console.log(current_address);
				this.address = current_address;
			},
			submit() {
				console.log(this.address);
				let payload = {
					model: "customers",
					data: {
						first_name: this.first_name,
						last_name: this.last_name,
						company_name: this.company_name,
						email: this.email,
						phone: this.phone,
						addresses: this.address
					}
				};
				this.create(payload).then(() => {
					this.clear();
					this.savingSuccessful = true;
				});
			},
			clear() {
				this.firstname = "";
				this.lastname = "";
				this.company_name = "";
				this.email = "";
				this.phone = "";
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