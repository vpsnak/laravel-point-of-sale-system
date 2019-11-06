<template>
	<v-container fluid>
		<v-row>
			<v-col>
				<v-card>
					<v-card-title class="text-center justify-center py-6">
						<h1 class="font-weight-bold display-3 primary--text">Reports</h1>
					</v-card-title>

					<v-tabs v-model="tabs" background-color="transparent" grow>
						<v-tab>Cash Register</v-tab>
						<v-tab>User</v-tab>
					</v-tabs>
					<v-tabs-items v-model="tabs">
						<v-tab-item>
							<v-card flat>
								<v-card-text>
									lalalalla
									<cashRegisterReportsTable></cashRegisterReportsTable>
								</v-card-text>
							</v-card>
						</v-tab-item>
						<v-tab-item>
							<v-card flat>
								<v-card-text>
									<userTable></userTable>
								</v-card-text>
							</v-card>
						</v-tab-item>
					</v-tabs-items>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			tabs: null
		};
	}
};
</script>
  