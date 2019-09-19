<template>
	<v-form v-model="valid">
		<div class="text-center">
			<v-chip color="blue-grey darken-4" label>
				<v-icon left>fas fa-warehouse</v-icon>Store Form
			</v-chip>
		</div>
		<v-text-field v-model="name" :rules="nameRules" :counter="10" label="Name" required></v-text-field>
	</v-form>
</template>

<script>
export default {
	data: () => ({
		valid: false,
		name: "",
		nameRules: [
			v => !!v || "Name is required",
			v => v.length <= 10 || "Name must be less than 10 characters"
		]
	})
};
</script>