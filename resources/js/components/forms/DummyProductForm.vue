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
	data() {
		return {
			dummyProduct: {
				id: null,
				name: "",
				sku: null,
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
			this.skuGenerator();
			this.dummyProduct.final_price = this.dummyProduct.price.amount;
			this.$store.commit("cart/addProduct", this.dummyProduct);
			this.$emit("submit", {
				notification: {
					msg: this.dummyProduct.name + " added to cart!",
					type: "success"
				}
			});
		},
		beforeDestroy() {
			this.$off("addtocart");
		},
		beforeDestroy() {
			this.$off("addtocart");
		},
		skuGenerator() {
			let random = function() {
				return (((1 + Math.random()) * 0x10000) | 0)
					.toString(16)
					.substring(1);
			};
			this.dummyProduct.id = "dummy" + "-" + random();
			this.dummyProduct.sku = "dummy" + "-" + random() + random();
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
