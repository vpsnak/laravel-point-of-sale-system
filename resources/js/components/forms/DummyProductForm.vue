<template>
	<ValidationObserver v-slot="{ invalid }" ref="dummyProductObs">
		<v-form @submit.prevent="submit">
			<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Name">
				<v-text-field
					v-model="dummyProduct.name"
					label="Name"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<ValidationProvider rules="max:191" v-slot="{ errors, valid }" name="Notes">
				<v-textarea
					rows="3"
					v-model="dummyProduct.notes"
					label="Notes"
					:error-messages="errors"
					:success="valid"
				></v-textarea>
			</ValidationProvider>
			<ValidationProvider
				:rules="{
                    required : true,
					regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
					}"
				v-slot="{ errors, valid }"
				name="Price Amount"
			>
				<v-text-field
					type="number"
					v-model="dummyProduct.price.amount"
					label="Price"
					:error-messages="errors"
					:success="valid"
				></v-text-field>
			</ValidationProvider>
			<v-btn class="mr-4" type="submit" :disabled="invalid" @click="addProduct()">Add to cart</v-btn>
			<v-btn @click="clear">clear</v-btn>
		</v-form>
	</ValidationObserver>
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
			this.$off("submit");
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
			this.dummyProduct.sku = "dummy" + "-" + random();
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
