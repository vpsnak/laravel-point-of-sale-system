<template>
	<ValidationObserver v-slot="{ invalid }" ref="obs">
		<v-form @submit.prevent="submit">
			<ValidationProvider
				v-if="action === 'change_self' || action === 'verify'"
				rules="required|min:8|max:191"
				v-slot="{ errors, valid }"
				name="Password"
			>
				<v-text-field
					v-model="formFields.current_password"
					:append-icon="
                        showCurrentPassword ? 'visibility' : 'visibility_off'
                    "
					:type="showCurrentPassword ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					label="Password"
					hint="At least 8 characters"
					counter
					@click:append="showCurrentPassword = !showCurrentPassword"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider
				v-if="action === 'change' || action === 'change_self'"
				v-slot="{ errors, valid }"
				rules="required|min:8|max:191"
				name="New Password"
				vid="confirmation"
			>
				<v-text-field
					v-model="formFields.password"
					:append-icon="
                        showPassword ? 'visibility' : 'visibility_off'
                    "
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
				v-if="action === 'change' || action === 'change_self'"
				rules="required|min:8|max:191|confirmed:confirmation"
				v-slot="{ errors, valid }"
				name="Password Confirmation"
			>
				<v-text-field
					v-model="formFields.password_confirmation"
					:append-icon="
                        showPasswordConfirmation
                            ? 'visibility'
                            : 'visibility_off'
                    "
					:type="showPasswordConfirmation ? 'text' : 'password'"
					:error-messages="errors"
					:success="valid"
					name="input-10-1"
					label="Retype the new password"
					counter
					@click:append="
                        showPasswordConfirmation = !showPasswordConfirmation
                    "
				></v-text-field>
			</ValidationProvider>
			<v-btn
				class="mr-4 mt-2"
				type="submit"
				:loading="loading"
				:disabled="invalid || disableSubmit"
			>submit</v-btn>
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
			showCurrentPassword: false,
			showPassword: false,
			showPasswordConfirmation: false,
			loading: false,
			formFields: {
				current_password: null,
				password: null,
				password_confirmation: null
			}
		};
	},
	computed: {
		action() {
			return this.$props.model.action;
		},
		disableSubmit() {
			switch (this.action) {
				case "change_self":
					return this.formFields.current_password ? false : true;
				case "change":
					return this.formFields.password ? false : true;
				case "verify":
					return this.formFields.current_password ? false : true;
				default:
					return true;
			}
		}
	},
	methods: {
		submit() {
			switch (this.action) {
				case "change_self":
					this.loading = true;
					this.changeSelfPwd(this.formFields)
						.then(() => {
							this.$emit("submit", true);
						})
						.finally(() => {
							this.loading = false;
						});
					break;
				case "change":
					this.loading = true;
					this.formFields.user_id = this.$props.model.id;
					this.changeUserPwd(this.formFields)
						.then(() => {
							this.$emit("submit", true);
						})
						.finally(() => {
							this.loading = false;
						});
					break;
				case "verify":
					this.loading = true;
					this.verifySelf(this.formFields)
						.then(() => {
							this.$emit("submit", true);
						})
						.finally(() => {
							this.loading = false;
						});
					break;
			}
		},
		...mapActions(["changeSelfPwd", "changeUserPwd", "verifySelf"])
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
