<template>
	<v-content>
		<v-container
			class="fill-height"
			fluid
		>
			<v-row
				align="center"
				justify="center"
			>
				<v-col
					cols="12"
					sm="8"
					md="4"
				>
					<v-card class="elevation-12">
						<v-toolbar flat>
							<v-toolbar-title>Login form</v-toolbar-title>
							<div class="flex-grow-1"></div>
						</v-toolbar>
						<v-card-text>
							<v-form>
								<v-text-field
									v-model='username'
									label="Login"
									name="login"
									prepend-icon="person"
									type="text"
								></v-text-field>
								<v-text-field
									v-model='password'
									label="Password"
									prepend-icon="lock"
									type="password"
								></v-text-field>
							</v-form>
						</v-card-text>
						<v-card-actions>
							<div class="flex-grow-1"></div>
							<v-btn @click="login">Login</v-btn>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</v-content>
</template>

<script>
	export default {
		data() {
			return {
				username: "",
				password: ""
			};
		},
		methods: {
			login() {
				let payload = {
					username: this.username,
					password: this.password
				};
			}
		}
	};
</script>