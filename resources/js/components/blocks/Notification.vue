<template>
	<v-snackbar v-model="show" :color="type" :type="type" top absolute>
		<v-icon v-if="show" large>{{ icon() }}</v-icon>

		<span class="pl-3">{{ message }}</span>

		<div class="flex-grow-1"></div>

		<v-btn icon @click="show = null">
			<v-icon>mdi-close</v-icon>
		</v-btn>
	</v-snackbar>
</template>

<script>
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
		}
	}
};
</script>