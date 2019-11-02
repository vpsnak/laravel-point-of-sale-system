<template>
	<v-app-bar app clipped-left>
		<v-app-bar-nav-icon @click.stop="mini = !mini"></v-app-bar-nav-icon>

		<v-toolbar-title>Plantshed</v-toolbar-title>
		<div class="flex-grow-1"></div>
		<interactiveDialog
			v-if="showViewDialog"
			:show="showViewDialog"
			title="Select store and cash register"
			:fullscreen="false"
			:width="600"
			component="storeRegForm"
			cancelBtnTxt="Close"
			@action="result"
			:titleCloseBtn="true"
		></interactiveDialog>
		<v-menu left bottom offset-x transition="scale-transition">
			<template v-slot:activator="{ on }">
				<v-btn v-on="on" icon>
					<v-icon>mdi-account-circle</v-icon>
				</v-btn>
				<v-chip @click="showViewDialog = true" class="ma-2" color="blue-grey" text-color="white">
					<v-avatar left>
						<v-icon>fas fa-store-alt</v-icon>
					</v-avatar>
					{{store.name}}
					<v-icon class="px-2">fas fa-cash-register</v-icon>
					{{cashRegister.name}}
				</v-chip>
			</template>
			<v-list dense>
				<v-list-item-group>
					<v-list-item inactive two-line @click.stop :ripple="false">
						<v-list-item-avatar color="orange">{{ user.name.charAt(0) }}</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>{{ user.name }}</v-list-item-title>
							<v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
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
					<v-list-item>
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
			showViewDialog: false
		};
	},
	methods: {
		invertTheme() {
			this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
		},
		result(event) {
			this.showViewDialog = false;
		}
	},
	computed: {
		user: {
			get() {
				return this.$store.state.user;
			}
		},
		cashRegister: {
			get() {
				return this.$store.state.cashRegister;
			}
		},
		store: {
			get() {
				return this.$store.state.store;
			}
		},
		darkMode: {
			get() {
				return this.$vuetify.theme.dark;
			},
			set(value) {
				this.$vuetify.theme.dark = value;
			}
		},
		mini: {
			get() {
				return this.$store.state.topMenu.mini;
			},
			set() {
				this.$store.commit("topMenu/toggleMini");
			}
		}
	}
};
</script>