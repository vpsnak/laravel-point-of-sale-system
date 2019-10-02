<template>
	<v-dialog persistent max-width="290">
		<span class="pl-3">{{ message }}</span>

		<div class="flex-grow-1"></div>

		<v-btn icon @click="show = null">
			<v-icon>mdi-close</v-icon>
		</v-btn>
	</v-dialog>
</template>

<script>
export default {
	data() {
		return {};
	},
	computed: {
		message() {
			return this.$store.state.notification.msg;
		},
		type() {
			return this.$store.state.notification.type;
		}
	},
	methods: {}
};
</script>