<template>
	<v-form>
		<v-text-field v-model="formFields.name" counter label="Name" required></v-text-field>
		<v-text-field v-model="formFields.sku" counter label="Sku" required></v-text-field>
		<v-text-field type="number" v-model="formFields.final_price" counter label="Final price" required></v-text-field>
		<v-btn class="mr-4" @click="addProduct(formFields)">Add to cart</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object || undefined
	},
	data() {
		return {
			defaultValues: {},
			formFields: {
				name: "",
				sku: null,
				final_price: null
			}
		};
	},
	mounted() {
		this.defaultValues = this.formFields;
		if (this.$props.model) {
			this.formFields = {
				...this.$props.model
			};
		}
	},
	methods: {
		addProduct(product) {
			this.$store.commit("cart/addProduct", product);
			this.$emit("addtocart", "cart");
		},
		beforeDestroy() {
			this.$off("addtocart");
		},
		beforeDestroy() {
			this.$off("submit");
		},
		clear() {
			this.formFields = { ...this.defaultValues };
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	}
};
</script>
