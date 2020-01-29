<template>
	<ValidationObserver
		v-slot="{ invalid }"
		ref="giftCardToCartObs"
		tag="form"
		@submit.prevent="submit"
	>
		<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Name">
			<v-text-field v-model="giftCard.name" label="Name" :error-messages="errors" :success="valid"></v-text-field>
		</ValidationProvider>
		<ValidationProvider rules="max:191" v-slot="{ errors }" name="Notes">
			<v-textarea
				:rows="3"
				v-model="giftCard.notes"
				label="Notes"
				:error-messages="errors"
				count
				no-resize
			></v-textarea>
		</ValidationProvider>
		<ValidationProvider
			:rules="{
                    required : true,
					max_value:99999,
					regex: /^[\d]{1,8}(\.[\d]{1,2})?$/g
					}"
			v-slot="{ errors, valid }"
			name="Price Amount"
		>
			<v-text-field
				type="number"
				v-model="giftCard.price.amount"
				label="Price"
				:error-messages="errors"
				:success="valid"
			></v-text-field>
		</ValidationProvider>

		<ValidationProvider rules="required|max:191" v-slot="{ errors, valid }" name="Code">
			<v-text-field v-model="giftCard.code" label="Code" :error-messages="errors" :success="valid"></v-text-field>
		</ValidationProvider>

		<v-btn class="mr-4" type="submit" :disabled="invalid">Add to cart</v-btn>
	</ValidationObserver>
</template>

<script>
export default {
	data() {
		return {
			giftCard: {
				id: null,
				name: "",
				code: "",
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
		submit() {
			this.IdAndSkuGenerator();
			this.giftCard.final_price = this.giftCard.price.amount;
			this.giftCard.name = "Gift card - " + this.giftCard.name;
			this.$store.commit("cart/addProduct", this.giftCard);
			this.$emit("submit", {
				notification: {
					msg: this.giftCard.name + " added to cart!",
					type: "success"
				}
			});
		},
		IdAndSkuGenerator() {
			let random = function() {
				return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
			};
			this.giftCard.id = "giftCard" + "-" + random();
			this.giftCard.sku = "giftCard" + "-" + this.giftCard.code;
		}
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
