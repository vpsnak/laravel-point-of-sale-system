<template>
	<v-container class="fill-height" fluid>
		<v-row align="center" justify="center">
			<v-col cols="12" sm="8" md="4">
				<v-card class="elevation-12 mx-auto pa-2">
					<v-card-title>Select / Open cash register</v-card-title>
					<v-card-text>
						<openCashRegisterForm @submit="submit"></openCashRegisterForm>
					</v-card-text>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	methods: {
		submit(event) {
			if (event) {
				this.$router.push(this.$route.query.redirect);
			}
		}
	}
};
</script>