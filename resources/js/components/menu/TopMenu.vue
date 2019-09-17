<template>
    <v-app-bar app>
		<v-app-bar-nav-icon @click.stop="mini = !mini"></v-app-bar-nav-icon>
		
        <v-toolbar-title>Plantshed</v-toolbar-title>

		<div class="flex-grow-1"></div>

		<v-btn icon>
			<v-icon>person</v-icon>
		</v-btn>
	</v-app-bar>
</template>

<script>
export default {
    data() {
        return {
            mini: false,
        }
    }
}
</script>