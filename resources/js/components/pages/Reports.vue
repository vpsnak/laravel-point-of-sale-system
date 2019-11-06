<template>
	<v-container fluid>
		<v-row>
			<v-col>
				<v-slide-group show-arrows>
					<v-slide-item>
						<v-btn
							v-model="cashRegister"
							color="blue-grey"
							class="mx-1"
							@click="cashRegister = true, user = false;"
							depressed
							rounded
						>Cash Register</v-btn>
					</v-slide-item>
					<v-slide-item>
						<v-btn
							v-model="user"
							color="blue-grey"
							class="mx-1"
							@click="cashRegister = false, user = true;"
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
			cashRegister: true,
			user: false
		};
	}
};
</script>
  