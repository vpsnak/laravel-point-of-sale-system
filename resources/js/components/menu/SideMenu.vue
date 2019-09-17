<template>
	<v-navigation-drawer app overflow :mini-variant="mini">
		<v-list dense nav>
			<v-list-item
				v-for="menuItem in menuItems"
				:key="menuItem.id"
				@click="menuAction(menuItem.action)"
			>
				<v-list-item-icon>
					<v-icon>{{ menuItem.icon }}</v-icon>
				</v-list-item-icon>

				<v-list-item-content>
					<v-list-item-title>{{ menuItem.title }}</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-list>
	</v-navigation-drawer>
</template>

<script>
export default {
	data() {
		return {
			mini: false,
			menuItems: [
				{
					id: 1,
					title: "asd",
					icon: "arrow_downward",
					action: "popup"
				},
				{
					id: 2,
					title: "asd",
					icon: "fa-user",
					action: "alert"
				}
			]
		};
	},
	methods: {
		menuAction(action) {
			switch (action) {
				case "alert":
					this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
					break;
				case "popup":
					this.showDialog = true;
					break;
				default:
					console.log(action);
					break;
			}
		}
	}
};
</script>