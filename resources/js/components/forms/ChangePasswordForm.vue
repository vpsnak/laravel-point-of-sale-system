<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form>
			<div
				class="text-center"
			>Use the form below to change your current_password. Your current_password cannot be the same as your username.</div>
			<ValidationProvider rules="required|min:8" v-slot="{ errors, valid }" name="Password">
				<v-text-field
					v-model="formFields.current_password"
					:append-icon="showCurrentPassword ? 'visibility' : 'visibility_off'"
					:type="showCurrentPassword ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					label="Password"
					hint="At least 8 characters"
					counter
					@click:append="showCurrentPassword = !showCurrentPassword"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="required|min:8" v-slot="{ errors, valid }" name="New Password">
				<v-text-field
					v-model="formFields.password"
					:append-icon="showPassword ? 'visibility' : 'visibility_off'"
					:type="showPassword ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					name="input-10-1"
					label="New Password"
					hint="At least 8 characters"
					counter
					@click:append="showPassword = !showPassword"
				></v-text-field>
			</ValidationProvider>

			<ValidationProvider
				rules="required|min:8"
				v-slot="{ errors, valid }"
				name="Password Confirmation"
			>
				<v-text-field
					v-model="formFields.password_confirmation"
					:append-icon="showPasswordConfirmation ? 'visibility' : 'visibility_off'"
					:type="showPasswordConfirmation ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					name="input-10-1"
					label="Retype the new password"
					counter
					@click:append="showPasswordConfirmation = !showPasswordConfirmation"
				></v-text-field>
			</ValidationProvider>
			<v-btn class="mr-4" @click.prevent="submit" :disabled="invalid || disableSubmit">submit</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
	</ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			showCurrentPassword: false,
			showPassword: false,
			showPasswordConfirmation: false,
			formFields: {
				current_password: null,
				password: null,
				password_confirmation: null
			}
		};
	},
	computed: {
		disableSubmit() {
			return this.formFields.current_password ? false : true;
		}
	},
	methods: {
		submit() {
			this.changePassword(this.formFields).then(() => {
				this.$emit("submit", "users");
			}).finally(() => {
				this.clear();
			});
		},
		clear() {
			this.$refs.obs.reset();

			this.formFields.current_password = null;
			this.formFields.password = null;
			this.formFields.password_confirmation = null;
		},
		...mapActions(['changePassword'])
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>