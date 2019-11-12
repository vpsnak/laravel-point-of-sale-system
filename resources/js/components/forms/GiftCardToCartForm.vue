<template>
	<v-form>
		<v-text-field v-model="giftCard.name" label="Name" required></v-text-field>
		<v-textarea rows="3" v-model="giftCard.notes" label="Notes" required></v-textarea>
		<v-text-field type="number" v-model="giftCard.price.amount" label="Price" required></v-text-field>
		<v-text-field v-model="giftCard.code" label="Code" required></v-text-field>
		<v-btn class="mr-4" @click="addProduct()">Add to cart</v-btn>
		<v-btn @click="clear">clear</v-btn>
	</v-form>
</template>

<script>
import { mapActions } from "vuex";

export default {
	data() {``
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
		addProduct() {
			let payload = {
				model: "gift-cards",
				data: {
					name: this.giftCard.name,
					code: this.giftCard.code,
					enabled: true,
					amount: this.giftCard.price.amount
				}
			};
			this.create(payload).then(() => {
				this.$emit("submit", {
					model: "gift-cards"
				});
				this.clear();
			});
			this.IdAndSkuGenerator();
			this.giftCard.final_price = this.giftCard.price.amount;
			this.giftCard.name = "Gift cart - " + this.giftCard.name;
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
				return (((1 + Math.random()) * 0x10000) | 0)
					.toString(16)
					.substring(1);
			};
			this.giftCard.id = "giftCard" + "-" + random();
			this.giftCard.sku = "giftCard" + "-" + this.giftCard.code;
		},
		clear() {
			this.giftCard = {
				name: "",
				code: "",
				price: {
					amount: null
				}
			};
		},
		...mapActions({
			getAll: "getAll",
			getOne: "getOne",
			create: "create",
			delete: "delete"
		})
	},
	beforeDestroy() {
		this.$off("submit");
	}
};
</script>
