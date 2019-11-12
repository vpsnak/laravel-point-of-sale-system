<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form>
			<div class="text-center">
				<v-chip color="indigo darken-4" label>
					<v-icon left>fas fa-user-circle</v-icon>User Form
				</v-chip>
			</div>
			<ValidationProvider rules="required|min:3" v-slot="{ errors, valid }" name="Name">
				<v-text-field :error-messages="errors" :success="valid" v-model="formFields.name" label="Name"></v-text-field>
			</ValidationProvider>

			<ValidationProvider rules="required|email" v-slot="{ errors, valid }" name="Email">
				<v-text-field
					v-model="formFields.email"
					:error-messages="errors"
					:success="valid"
					label="E-mail"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|min:8" v-slot="{ errors, valid }" name="Password">
				<v-text-field
					v-model="formFields.password"
					:append-icon="showPassword ? 'visibility' : 'visibility_off'"
					:type="showPassword ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					name="input-10-1"
					label="Password"
					hint="At least 8 characters"
					counter
					@click:append="showPassword = !showPassword"
				></v-text-field>
			</ValidationProvider>
			<v-btn
				class="mr-4"
				type="submit"
				@click.prevent="submit"
				:disabled="invalid || disableSubmit"
			>submit</v-btn>
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
			showPassword: false,
			defaultValues: {},
			formFields: {
				id: null,
				name: null,
				email: null,
				password: null
			}
		};
	},
	computed: {
		disableSubmit() {
			return this.formFields.name ? false : true;
		}
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
			let payload = {
				model: "users",
				data: { ...this.formFields }
			};
			this.create(payload).then(() => {
				this.clear();
				this.$emit("submit", {
					getRows: true,
					model: "users",
					notification: {
						msg: "User added successfully",
						type: "success"
					}
				});
				this.clear();
			});
		},
		clear() {
			this.$refs.obs.reset();
			this.formFields = { ...this.defaultValues };
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