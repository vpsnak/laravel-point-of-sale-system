<template>
	<ValidationObserver
		v-slot="{ invalid }"
		ref="rechargeGiftCardToCartObs"
		tag="form"
		@submit.prevent="submit"
	>
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
				v-model="giftCard.price.amount"
				label="Price"
				:error-messages="errors"
				:success="valid"
			></v-text-field>
		</ValidationProvider>

		<v-btn class="mr-4" type="submit" :disabled="invalid">Add to cart</v-btn>
	</ValidationObserver>
</template>

<script>
import { mapActions } from "vuex";

export default {
	props: {
		model: Object || undefined
	},
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
			this.giftCard.name = "Gift cart - " + this.$props.model.name;
			this.$store.commit("cart/addProduct", this.giftCard);

			this.$emit("submit", true);
		},
		IdAndSkuGenerator() {
			let random = function() {
				return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
			};
			this.giftCard.id = "giftCard" + "-" + random();
			this.giftCard.sku = "giftCard" + "-" + this.$props.model.code;
		}
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
