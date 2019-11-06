<template>
	<v-container fluid>
		<v-row>
			<v-col>
				<v-slide-group show-arrows v-model="selectedTable">
					<v-slide-item v-slot:default="{ active, toggle }">
						<v-btn
							v-model="cashRegister"
							:color="active ? 'blue-grey' : ''"
							class="mx-1"
							@click="toggle, cashRegister = true, user = false;"
							depressed
							rounded
						>Cash Register</v-btn>
					</v-slide-item>
					<v-slide-item v-slot:default="{ active, toggle }">
						<v-btn
							v-model="user"
							:color="active ? 'blue-grey' : ''"
							class="mx-1"
							@click="toggle, cashRegister = false, user = true;"
							depressed
							rounded
						>User</v-btn>
					</v-slide-item>
				</v-slide-group>
				<div class="mt-2" v-if="cashRegister">
					<cashRegisterReportsTable></cashRegisterReportsTable>
				</div>
				<div class="mt-2" v-if="user">
					<userTable></userTable>
				</div>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			selectedTable: null,
			cashRegister: false,
			user: false
		};
	}
};
</script>
  