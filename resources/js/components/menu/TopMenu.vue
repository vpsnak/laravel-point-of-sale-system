<template>
	<v-app-bar app clipped-left>
		<v-app-bar-nav-icon @click.stop="toggle = !toggle"></v-app-bar-nav-icon>

		<v-toolbar-title>Plantshed</v-toolbar-title>

		<div class="flex-grow-1"></div>

		<interactiveDialog
			v-if="CloseCashRegisterDialog"
			:show="CloseCashRegisterDialog"
			title="Close cash register"
			:fullscreen="false"
			:width="600"
			component="closeCashRegisterForm"
			cancelBtnTxt="Close"
			@action="result"
			:titleCloseBtn="true"
		></interactiveDialog>
		<interactiveDialog
			v-if="CheckCashRegisterDialog"
			:show="CheckCashRegisterDialog"
			title="Check cash register ( X )"
			:fullscreen="false"
			:width="1000"
			component="cashRegisterReports"
			:model="cashRegister"
			cancelBtnTxt="Close"
			@action="result"
			:titleCloseBtn="true"
		></interactiveDialog>
		<interactiveDialog
			v-if="changePasswordDialog"
			:show="changePasswordDialog"
			title="Change Password"
			:fullscreen="false"
			:width="600"
			:model="{ action: 'change_self' }"
			component="PasswordForm"
			cancelBtnTxt="Close"
			@action="result"
			:titleCloseBtn="true"
		></interactiveDialog>
		<v-chip
			v-if="!openedRegister"
			text
			@click="$router.push({ name: 'openCashRegister' })"
		>Open cash register</v-chip>
		<v-menu v-if="openedRegister" left bottom offset-x transition="scale-transition">
			<template v-slot:activator="{ on }">
				<v-btn v-on="on" icon>
					<v-avatar color="green" size="36">
						<v-icon dark>mdi-cash-register</v-icon>
					</v-avatar>
				</v-btn>
			</template>
			<v-list dense>
				<v-list-item-group>
					<v-list-item inactive two-line @click.stop :ripple="false">
						<v-list-item-avatar color="green">
							<v-icon>mdi-cash-register</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>{{ store.name }}</v-list-item-title>
							<v-list-item-subtitle>{{ cashRegister.name }}</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item @click="CheckCashRegisterDialog = true">
						<v-list-item-avatar>
							<v-icon color="secondary">mdi-check-circle</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>Check {{ cashRegister.name }} ( X )</v-list-item-title>
						</v-list-item-content>
					</v-list-item>
					<v-list-item @click="CloseCashRegisterDialog = true">
						<v-list-item-avatar>
							<v-icon color="red">mdi-close-circle</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>Close {{ cashRegister.name }} ( Z )</v-list-item-title>
						</v-list-item-content>
					</v-list-item>
				</v-list-item-group>
			</v-list>
		</v-menu>
		<v-menu left bottom offset-x transition="scale-transition">
			<template v-slot:activator="{ on }">
				<v-btn v-on="on" icon>
					<v-icon>mdi-account-circle</v-icon>
				</v-btn>
			</template>
			<v-list dense>
				<v-list-item-group>
					<v-list-item inactive two-line @click.stop :ripple="false">
						<v-list-item-avatar color="orange">
							{{
							user.name.charAt(0)
							}}
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>
								{{
								user.name
								}}
							</v-list-item-title>
							<v-list-item-subtitle>
								{{
								user.email
								}}
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item to="/logout">
						<v-list-item-avatar>
							<v-icon>mdi-logout-variant</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>Sign out</v-list-item-title>
						</v-list-item-content>
					</v-list-item>
					<v-list-item @click="changePasswordDialog = true">
						<v-list-item-avatar>
							<v-icon>mdi-key</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>Change password</v-list-item-title>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item @click.stop="darkMode = !darkMode">
						<v-list-item-avatar>
							<v-icon>mdi-brightness-4</v-icon>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>Dark mode</v-list-item-title>
						</v-list-item-content>
						<v-list-item-action>
							<v-switch v-model="darkMode" @click.stop="darkMode = !darkMode"></v-switch>
						</v-list-item-action>
					</v-list-item>
				</v-list-item-group>
			</v-list>
		</v-menu>
	</v-app-bar>
</template>

<script>
import { mapState } from "vuex";

export default {
	data() {
		return {
			whatever: "",
			OpenCashRegisterDialog: false,
			CloseCashRegisterDialog: false,
			CheckCashRegisterDialog: false,
			changePasswordDialog: false
		};
	},
	methods: {
		invertTheme() {
			this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
		},
		result(event) {
			this.OpenCashRegisterDialog = false;
			this.CloseCashRegisterDialog = false;
			this.CheckCashRegisterDialog = false;
			this.changePasswordDialog = false;
		}
	},
	computed: {
		openedRegister() {
			return this.$store.getters.openedRegister;
		},
		user() {
			return this.$store.state.user;
		},
		cashRegister() {
			return this.$store.state.cashRegister;
		},
		store() {
			return this.$store.state.store;
		},
		darkMode: {
			get() {
				return this.$vuetify.theme.dark;
			},
			set(value) {
				this.$vuetify.theme.dark = value;
			}
		},
		toggle: {
			get() {
				return this.$store.state.menu.toggle;
			},
			set(value) {
				this.$store.commit("menu/toggleMenu", value);
			}
		}
	}
};
</script>
