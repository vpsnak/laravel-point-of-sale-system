<template>
	<v-row justify="center">
		<v-dialog v-model="dialog" persistent max-width="600px">
			<template v-slot:activator="{ on }">
				<v-btn color="primary" dark v-on="on">{{ this.props.title }}</v-btn>
			</template>
			<v-card>
				<v-card-title>
					<span class="headline">User Profile</span>
				</v-card-title>
				<v-card-text>
					<v-container>
						<customerForm></customerForm>
					</v-container>
					<small>*indicates required field</small>
				</v-card-text>
				<v-card-actions>
					<div class="flex-grow-1"></div>
					<v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
					<v-btn color="blue darken-1" text @click="dialog = false">Save</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-row>
</template>

<script>
export default {
	props: {
		title: String,
		model: String
	}
};
</script>