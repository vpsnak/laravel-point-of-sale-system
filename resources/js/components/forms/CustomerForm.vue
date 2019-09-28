<template>
	<v-card ref="form">
		<div class="text-center">
			<v-chip class="mb-n8 mx-auto" elevation="5" color="primary" label>
				<v-icon left>fas fa-user-astronaut</v-icon>Customer Form
			</v-chip>
		</div>
		<v-card-text>
			<v-text-field
				ref="firstname"
				v-model="firstname"
				:rules="[() => !!firstname || 'This field is required']"
				:error-messages="errorMessages"
				label="First name"
				placeholder="Panos"
				required
			></v-text-field>
			<v-text-field
				ref="lastname"
				v-model="lastname"
				:rules="[() => !!lastname || 'This field is required']"
				:error-messages="errorMessages"
				label="Last name"
				placeholder="Meletis"
				required
			></v-text-field>
			<v-text-field
				ref="email"
				v-model="email"
				:rules="[ v => !!v || 'E-mail is required',
        v => /.+@.+/.test(v) || 'E-mail must be valid']"
				:error-messages="errorMessages"
				label="Email"
				placeholder="Email"
				required
			></v-text-field>
			<v-text-field
				ref="company_name"
				v-model="company_name"
				:rules="[
              () => !!company_name || 'This field is required',
              () => !!company_name && company_name.length <= 25 || 'Company name must be less than 25 characters',
               lengthCheck
            ]"
				label="Company name"
				placeholder="Web O2"
				counter="25"
				required
			></v-text-field>
			<v-autocomplete
				ref="country"
				v-model="country"
				:rules="[() => !!country || 'This field is required']"
				:items="countries"
				label="Country"
				placeholder="Select..."
				required
			></v-autocomplete>
			<addressForm @address="watchAddress($event)"></addressForm>
		</v-card-text>
		<v-divider class="mt-12"></v-divider>
		<v-card-actions>
			<v-btn text>Cancel</v-btn>
			<div class="flex-grow-1"></div>
			<v-slide-x-reverse-transition>
				<v-tooltip v-if="formHasErrors" left>
					<template v-slot:activator="{ on }">
						<v-btn icon class="my-0" @click="resetForm" v-on="on">
							<v-icon>mdi-refresh</v-icon>
						</v-btn>
					</template>
					<span>Refresh form</span>
				</v-tooltip>
			</v-slide-x-reverse-transition>
			<v-btn color="primary" text @click="submit">Submit</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
	export default {
		data() {
			return {
				firstname: null,
				lastname: null,
				company_name: null,
				email: null,
				company_name: null,
				country: null,
				countries: [
					"Afghanistan",
					"Albania",
					"Australia",
					"Venezuela",
					"Vietnam",
					"Virgin Islands (US)",
					"Yemen",
					"Zambia",
					"Zimbabwe"
				],
				formHasErrors: false,
				errorMessages: ""
			};
		},

		computed: {
			form() {
				return {
					firstname: this.firstname,
					lastname: this.lastname,
					company_name: this.company_name,
					country: this.country,
					email: this.email
				};
			}
		},

		watch: {
			name() {
				this.errorMessages = "";
			}
		},

		methods: {
			watchAddress(message) {
				console.log(message);
			},
			lengthCheck() {
				this.errorMessages =
					this.company_name && !this.firstname
						? "Hey! I'm required (den to phre)"
						: "";

				return true;
			},
			resetForm() {
				this.errorMessages = [];
				this.formHasErrors = false;

				Object.keys(this.form).forEach(f => {
					this.$refs[f].reset();
				});
			},
			submit() {
				this.formHasErrors = false;

				Object.keys(this.form).forEach(f => {
					if (!this.form[f]) this.formHasErrors = true;

					this.$refs[f].validate(true);
				});
			}  
		}
	};
</script>