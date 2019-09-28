<template>
	<v-snackbar v-model="show" :color="type" absolute top>
		{{ message }}
		<v-btn></v-btn>
	</v-snackbar>
</template>

<script>
export default {
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
	}
};
</script>