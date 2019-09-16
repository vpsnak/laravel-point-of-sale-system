<template>
	<v-app id="app">
		<v-navigation-drawer app overflow :mini-variant="mini">
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
			<v-app-bar-nav-icon @click.stop="mini = !mini"></v-app-bar-nav-icon>
			<v-toolbar-title>Plantshed</v-toolbar-title>

			<div class="flex-grow-1"></div>

			<v-btn icon>
				<v-icon>person</v-icon>
			</v-btn>

		</v-app-bar>

		<v-content>
			<v-container fluid>
				<v-row justify="center">
					<v-col cols="8">
						<v-card>
							<v-card-text>
								<v-row>
									<v-col>
										<v-toolbar flat>
											<v-text-field placeholder="Search product" class="grey--text">
												<v-icon slot="prepend">mdi-magnify</v-icon>
											</v-text-field>
											<v-tooltip bottom>
												<template v-slot:activator="{ on }">
													<v-btn icon v-on="on" class="ml-1">
														<v-icon>
															add
														</v-icon>
													</v-btn>
												</template>
												<span>Add product</span>
											</v-tooltip>
										</v-toolbar>
									</v-col>
								</v-row>
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
												<v-tooltip bottom>
													<template v-slot:activator="{ on }">
														<v-btn icon v-on="on" class="ml-1">
															<v-icon>add_shopping_cart</v-icon>
														</v-btn>
													</template>
													<span>
														Add to shopping cart
													</span>
												</v-tooltip>
												<v-tooltip bottom>
													<template v-slot:activator="{ on }">
														<v-btn icon v-on="on" class="ml-1">
															<v-icon>mdi-heart</v-icon>
														</v-btn>
													</template>
													<span>
														Add to favorites
													</span>
												</v-tooltip>
												<div class="flex-grow-1"></div>
												<v-tooltip bottom>
													<template v-slot:activator="{ on }">
														<v-btn icon v-on="on" class="ml-1">
															<v-icon>remove_red_eye</v-icon>
														</v-btn>
													</template>
													<span>
														View product
													</span>
												</v-tooltip>
												<v-tooltip bottom>
													<template v-slot:activator="{ on }">
														<v-btn icon v-on="on" class="ml-1">
															<v-icon>edit</v-icon>
														</v-btn>
													</template>
													<span>
														Edit product
													</span>
												</v-tooltip>
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
									<v-col>
										<v-toolbar flat>
											<v-text-field placeholder="Search customer" class="grey--text">
												<v-icon slot="prepend">mdi-magnify</v-icon>
											</v-text-field>
											<v-tooltip bottom>
												<template v-slot:activator="{ on }">
													<v-btn icon v-on="on" class="ml-1">
														<v-icon>
															person_add
														</v-icon>
													</v-btn>
												</template>
												<span>Add customer</span>
											</v-tooltip>
										</v-toolbar>
										<v-divider />
										    <v-list three-line subheader dense>
												<v-subheader>Shopping cart</v-subheader>

												<v-list-item
													v-for="item in items"
													:key="item.name"
													@click=""
													dense
												>
												
													<v-list-item-content>
														<v-list-item-title v-text="item.name"></v-list-item-title>
														<v-list-item-subtitle>$ {{ item.price }}</v-list-item-subtitle>
													</v-list-item-content>

													<v-list-item-action>
														<v-btn icon>
															<v-icon color="grey lighten-1">remove</v-icon>
														</v-btn>
													</v-list-item-action>

													<v-list-item-action>
														<v-chip filter>
															<span>42</span>
														</v-chip>
													</v-list-item-action>

													<v-list-item-action>
														<v-btn icon>
															<v-icon color="grey lighten-1">add</v-icon>
														</v-btn>
													</v-list-item-action>

													<v-list-item-action>
														<v-btn icon>
															<v-icon color="grey lighten-1">delete</v-icon>
														</v-btn>
													</v-list-item-action>
												</v-list-item>
										    </v-list>
									</v-col>
								</v-row>

								<v-row>
									<v-col>
										<v-divider />

										<div class="d-flex justify-space-between">
											<span class="pa-2">Sub total</span>
											<span class="pa-2">12 {{ }}</span>
										</div>

										<v-divider />

										<div class="d-flex justify-space-between">
											<span class="pa-2">Tax</span>
											<span class="pa-2">12 {{ }}</span>
										</div>

										<v-divider />

										<div class="d-flex justify-space-between">
											<span class="pa-2">Total discount</span>
											<span class="pa-2">12 {{ }}</span>
										</div>

										<v-divider />

										<div class="d-flex justify-space-between">
											<span class="pa-2">Total</span>
											<span class="pa-2">12 {{ }}</span>
										</div>

										<v-divider />
										
										<v-btn block class="my-2">
											Checkout
										</v-btn>
										
										<v-divider />
									</v-col>
								</v-row>
								<v-row>
									<v-col cols="4" class="text-center">
										<v-btn icon>
											<v-icon>
												fa-recycle
											</v-icon>
										</v-btn>
									</v-col>

									<v-col cols="4" class="text-center">
										<v-btn icon>
											<v-icon>
												pause
											</v-icon>
										</v-btn>
									</v-col>

									<v-col cols="4" class="text-center">
										<v-btn icon>
											<v-icon>
												delete
											</v-icon>
										</v-btn>
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
			items: [
				{ name: 'Cotton Long Sleeve T-shirt', price: '44' },
				{ name: 'Recipes', price: '4' },
				{ name: 'Work', price: '7' },
			],
			mini: false,
			showDialog: false,
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
					this.$vuetify.theme.dark = !this.$vuetify.theme.dark 
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