<template>
	<v-form v-model="valid">
		<div class="text-center">
			<v-chip color="deep-purple" label>
				<v-icon left>fab fa-product-hunt</v-icon>Product Form
			</v-chip>
		</div>
		<v-text-field v-model="sku" :rules="nameRules" :counter="10" label="Sku" required></v-text-field>

		<v-text-field v-model="name" :rules="nameRules" :counter="10" label="Name" required></v-text-field>

		<v-text-field v-model="photo_url" label="Photo Url" required></v-text-field>

		<v-select
			v-model="select"
			:items="items"
			:rules="[v => !!v || 'Item is required']"
			label="Store"
			required
		></v-select>
	</v-form>
</template>

<script>
	export default {
		data: () => ({
			valid: false,
			sku: "",
			name: "",
			photo_url: "",
			nameRules: [
				v => !!v || "Name is required",
				v => v.length <= 10 || "Name must be less than 10 characters"
			],
			email: "",
			emailRules: [
				v => !!v || "E-mail is required",
				v => /.+@.+/.test(v) || "E-mail must be valid"
			],
			select: null,
			items: ["Store 1", "Store 2", "Store 3", "Store 4"]
		})
	};
</script>