<template>
	<v-app id="app">
		<v-navigation-drawer app overflow :mini-variant="primaryDrawer.model">
			<v-list dense nav>
				<v-list-item
					v-for="menuItem in menuItems"
					:key="menuItem.id"
					link
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

		<v-app-bar app>
			<v-app-bar-nav-icon @click.stop="primaryDrawer.model = !primaryDrawer.model"></v-app-bar-nav-icon>
			<v-toolbar-title>Plantshed</v-toolbar-title>
		</v-app-bar>

		<v-content>
			<v-container fluid>
				<v-row justify="center">
					<v-col cols="8">
						<v-card>
							<v-card-text>
								<v-row>
									<v-col v-for="card in cards" :key="card.title" :cols="card.flex">
										<v-card>
											<v-img
												:src="card.src"
												class="white--text"
												height="200px"
												gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
											>
												<v-card-title class="fill-height align-end" v-text="card.title"></v-card-title>
											</v-img>

											<v-card-actions>
												<div class="flex-grow-1"></div>

												<v-btn icon>
													<v-icon>mdi-heart</v-icon>
												</v-btn>

												<v-btn icon>
													<v-icon>mdi-bookmark</v-icon>
												</v-btn>

												<v-btn icon>
													<v-icon>mdi-share-variant</v-icon>
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-col>
								</v-row>
							</v-card-text>
							<v-card-actions></v-card-actions>
						</v-card>
					</v-col>
					<v-col cols="4">
						<v-card>
							<v-card-text>
								<v-row>
									<v-col cols="12">
										<v-toolbar flat>
											<v-text-field placeholder="Search product" class="grey--text">
												<v-icon slot="prepend" color="green">mdi-magnify</v-icon>
											</v-text-field>
										</v-toolbar>
									</v-col>
								</v-row>
							</v-card-text>
							<v-card-actions></v-card-actions>
						</v-card>
					</v-col>
				</v-row>
			</v-container>
		</v-content>

		<v-footer inset app>
			<span class="px-4">&copy; {{ new Date().getFullYear() }}</span>
		</v-footer>
	</v-app>
</template>

<script>
export default {
	data() {
		return {
			showDialog: false,
			primaryDrawer: {
				model: true
			},
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
			],
			cards: [
				{
					title: "Pre-fab homes",
					src: "https://cdn.vuetifyjs.com/images/cards/house.jpg",
					flex: 3
				},
				{
					title: "Favorite road trips",
					src: "https://cdn.vuetifyjs.com/images/cards/road.jpg",
					flex: 3
				},
				{
					title: "Best airlines",
					src: "https://cdn.vuetifyjs.com/images/cards/plane.jpg",
					flex: 3
				},
				{
					title: "Favorite road trips",
					src: "https://cdn.vuetifyjs.com/images/cards/road.jpg",
					flex: 3
				}
			]
		};
	},
	mounted() {},
	methods: {
		menuAction(action) {
			switch (action) {
				case "alert":
					alert("psssst");
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