<template>
	<ValidationObserver v-slot="{ invalid }" ref="userRoleObs">
		<v-form @submit.prevent="submit">
			<ValidationProvider v-slot="{ errors, valid }" name="Role">
				<v-select
					hide-selected
					v-model="role_id"
					:items="roles"
					label="Role"
					required
					:loading="loading"
					:disabled="loading"
					item-text="name"
					item-value="id"
				></v-select>
			</ValidationProvider>

			<v-btn class="mr-4" type="submit" :loading="loading" :disabled="loading">submit</v-btn>
		</v-form>
	</ValidationObserver>
</template>
<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object
	},
	data() {
		return {
			loading: false,
			user_name: null,
			loading: false,
			roles: [],
			id: null,
			role_id: null
		};
	},
	mounted() {
		this.getAllUserRoles();
		this.user_name = this.$props.model.name;
		this.user_id = this.$props.model.id;
		this.role_id = this.$props.model.roles[0].id;
	},
	methods: {
		submit() {
			this.loading = true;
			let payload = {
				model: "roles/set",
				data: {
					user_id: this.user_id,
					role_id: this.role_id
				}
			};
			this.setRole(payload)
				.then(() => {
					this.$emit("submit", true);
				})
				.finally(() => {
					this.loading = false;
				});
		},
		getAllUserRoles() {
			this.loading = true;
			this.getAll({
				model: "roles"
			})
				.then(response => {
					this.roles = response;
				})
				.finally(() => {
					this.loading = false;
				});
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			setRole: "setRole"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>