<template>
	<v-app id="app">
		<sideMenu v-if="auth" />

		<topMenu v-if="auth" />

		<notification />

		<interactiveDialog
			v-if="auth && dialog.show"
			:show="dialog.show"
			:title="dialog.title"
			:titleCloseBtn="dialog.titleCloseBtn"
			:icon="dialog.icon"
			:fullscreen="dialog.fullscreen"
			:width="dialog.width"
			:component="dialog.component"
			:content="dialog.content"
			:model="dialog.model"
			@action="dialogEvent"
			:persistent="dialog.persistent"
		></interactiveDialog>

		<v-content>
			<router-view></router-view>
		</v-content>
	</v-app>
</template>

<script>
import { mapGetters, mapMutations, mapState } from "vuex";

export default {
	mounted() {
		this.$store.dispatch("getAppConfig");
	},
	computed: {
		...mapState("dialog", ["interactive_dialog"]),

		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		},
		auth() {
			if (this.authorized && this.role) {
				window.axios.defaults.headers.common[
					"Authorization"
				] = this.$store.state.token;
				return true;
			} else {
				this.logout();
				return false;
			}
		},
		...mapGetters(["authorized", "role"])
	},
	methods: {
		...mapMutations(["logout"]),
		...mapMutations("dialog", ["setDialog", "resetDialog"]),

		dialogEvent(event) {
			this.resetDialog();
		}
	}
};
</script>

<style>
.fas,
.fab,
.fa {
	font-size: 1.2em !important;
}

a {
	text-decoration: none;
}

input[type="number"] {
	-moz-appearance: textfield;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
	-webkit-appearance: none;
}
</style>
