<template>
	<v-snackbar v-model="show" :color="type" absolute top>
		{{ message }}
		<v-btn></v-btn>
	</v-snackbar>
</template>

<script>
export default {
	computed: {
		show() {
			return this.$store.state.notification.msg ? true : false;
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