<template>
	<v-form>
		<v-text-field v-model="formFields.name" label="Name" required></v-text-field>
		<v-text-field v-model="formFields.sku" label="Sku" required></v-text-field>
		<v-text-field type="number" v-model="formFields.price.amount" label="Price" required></v-text-field>
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
			formFields: {
				name: "",
				sku: null,
				price: {
					amount: null
				}
			}
		};
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
			this.$off("addtocart");
		},
		clear() {
			this.formFields = {
				name: "",
				sku: null,
				price: {
					amount: 0
				}
			};
		}
	}
};
</script>
