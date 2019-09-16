<template>
	<v-app id="app">
		<v-navigation-drawer app overflow></v-navigation-drawer>

		<v-app-bar app>
			<v-app-bar-nav-icon @click.stop="primaryDrawer.model = !primaryDrawer.model"></v-app-bar-nav-icon>
			<v-toolbar-title>Plantshed</v-toolbar-title>
		</v-app-bar>

		<v-content>
			<v-container fluid>
				<v-row align="center" justify="center">
					<v-col cols="8">
						<v-card>
							<v-card-text>
								<v-row>
									<v-col cols="12" md="6">
										<router-view></router-view>
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
									<v-col cols="12" md="6">
										<router-link to="/foo">Go to Foo</router-link>
										<router-link to="/bar">Go to Bar</router-link>
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
	data: () => ({
		primaryDrawer: {
			model: true
		}
	}),

	mounted() {}
};
</script>