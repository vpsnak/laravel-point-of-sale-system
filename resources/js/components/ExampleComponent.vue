<template>
	<v-app id="app">
		<v-navigation-drawer app overflow :mini-variant="primaryDrawer.model">
			<v-list dense nav>
				<v-list-item v-for="menuItem in menuItems" :key="menuItem.id" link @click="menuAction(menuItem.action)">
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
				<v-dialog
					v-model="showDialog"
					max-width="290"
					>
      <v-card>
        <v-card-title class="headline">Use Google's location service?</v-card-title>

        <v-card-text>
          Let Google help apps determine location. This means sending anonymous location data to Google, even when no apps are running.
        </v-card-text>

        <v-card-actions>
          <div class="flex-grow-1"></div>

          <v-btn
            color="green darken-1"
            text
            @click="dialog = false"
          >
            Disagree
          </v-btn>

          <v-btn
            color="green darken-1"
            text
            @click="dialog = false"
          >
            Agree
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
	data() {
		return {
			showDialog: false,
			primaryDrawer: {
				model: true
			},
			menuItems: [
				{
					id: 1,
					title: 'asd',
					icon: 'arrow_downward',
					action: 'popup'
				},
				{
					id: 2,
					title: 'asd',
					icon: 'fa-user',
					action: 'alert'
				}
			]
		} 
	},
	mounted() {

	},
	methods: {
		menuAction(action) {
			switch(action) {
				case 'alert':
					alert('psssst')
					break
				case 'popup':
					this.showDialog = true
					break
				default:
					console.log(action);
					break;
			}
		}
	}
};
</script>