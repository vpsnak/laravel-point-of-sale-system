<template>
	<v-app-bar app>
		<v-app-bar-nav-icon @click.stop="mini = !mini"></v-app-bar-nav-icon>

		<v-toolbar-title>Plantshed</v-toolbar-title>

		<div class="flex-grow-1"></div>

		<v-btn
			icon
			@click="invertTheme"
		>
			<v-icon>person</v-icon>
		</v-btn>
	</v-app-bar>
</template>

<script>
	export default {
		data() {
			return {
				mini: false
			};
		},
		methods: {
			invertTheme() {
				this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
			}
		}
	};
</script>