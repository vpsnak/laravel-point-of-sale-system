<template>
	<v-snackbar v-model="show" :color="type" :type="type" top absolute class="d-flex align-center">
		<v-icon v-if="show" large>{{ icon() }}</v-icon>
		<span class="pl-3" v-html="displayErrors()"></span>

		<div class="flex-grow-1"></div>
	</v-snackbar>
</template>

<script>
import { isArray } from "util";
export default {
	data() {
		return {
			icons: [
				{
					name: "success",
					icon: "check_circle"
				},
				{
					name: "info",
					icon: "info"
				},
				{
					name: "warning",
					icon: "warning"
				},
				{
					name: "error",
					icon: "error"
				}
			]
		};
	},
	computed: {
		show: {
			get() {
				return this.$store.state.notification.msg ? true : false;
			},
			set() {
				this.$store.state.notification.msg = "";
				this.$store.state.notification.type = undefined;
			}
		},
		message() {
			return this.$store.state.notification.msg;
		},
		type() {
			return this.$store.state.notification.type;
		}
	},
	methods: {
		icon() {
			return _.find(this.icons, { name: this.type }).icon;
		},
		displayErrors() {
			if (typeof this.message === "object") {
				let errors = "";

				for (const [key, value] of Object.entries(this.message)) {
					errors +=
						"<strong>" +
						_.capitalize(key) +
						"</strong>" +
						": " +
						value +
						"<br>";
				}

				return errors;
			} else {
				return _.capitalize(this.message);
			}
		}
	}
};
</script>