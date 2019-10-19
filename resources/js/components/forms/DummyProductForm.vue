<template>
	<v-form>
		<v-text-field v-model="dummyProduct.name" label="Name" required></v-text-field>
		<v-textarea rows="3" v-model="dummyProduct.notes" label="Notes" required></v-textarea>
		<v-text-field type="number" v-model="dummyProduct.price.amount" label="Price" required></v-text-field>
		<v-btn class="mr-4" @click="addProduct()">Add to cart</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object
	},
	data() {
		return {
			dummyProduct: {
				name: "",
				sku: 1,
				notes: "",
				price: {
					amount: null
				},
				final_price: null
			}
		};
	},

	methods: {
		addProduct() {
			this.dummyProduct.final_price = this.dummyProduct.price.amount;
			this.$store.commit("cart/addProduct", this.dummyProduct);
			this.$emit("addtocart", "cart");
		},
		beforeDestroy() {
			this.$off("addtocart");
		},
		beforeDestroy() {
			this.$off("addtocart");
		},
		clear() {
			this.dummyProduct = {
				name: "",
				price: {
					amount: null
				}
			};
		}
	}
};
</script>
