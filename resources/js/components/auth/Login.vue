<template>
	<v-container class="fill-height" fluid>
		<v-row align="center" justify="center">
			<v-col cols="12" sm="8" md="4">
				<v-card class="elevation-12">
					<v-form @submit.prevent="submit">
						<v-toolbar flat class="d-flex justify-center">
							<v-toolbar-title>Plantshed Sales Management</v-toolbar-title>
						</v-toolbar>
						<v-card-text>
							<v-text-field
								v-model="username"
								label="Login"
								name="login"
								prepend-icon="person"
								type="text"
							></v-text-field>
							<v-text-field v-model="password" label="Password" prepend-icon="lock" type="password"></v-text-field>
						</v-card-text>
						<v-card-actions>
							<v-spacer />
							<v-btn type="submit" :loading="loading" color="secondary">Login</v-btn>
							<v-spacer />
						</v-card-actions>
					</v-form>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {
		return {
			username: "",
			password: "",
			loading: false
		};
	},
	methods: {
		submit() {
			this.loading = true;

			let payload = {
				username: this.username,
				password: this.password
			};

			this.login(payload)
				.then(() => {
					if (this.$route.query.redirect) {
						this.$router.push(this.$route.query.redirect);
					} else {
						this.$router.push("/");
					}
				})
				.finally(() => {
					this.loading = false;
				});
		},
		...mapActions(["login"])
	}
};
</script>
