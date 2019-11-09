<template>
	<div class="d-flex">
		<v-col cols="6" class="pa-0 pr-2">
			<v-select
				v-if="editable"
				:key="model.id"
				@change="amount = amount"
				v-model="discountType"
				:items="discountTypes"
				label="Discount"
				item-text="label"
				item-value="value"
			></v-select>
			<v-text-field v-else-if="!editable" :value="discountType" label="Discount" disabled></v-text-field>
		</v-col>
		<v-col cols="6" class="pa-0 pl-2">
			<v-text-field
				:key="model.id"
				v-if="discountType && discountType !== 'None'"
				v-model="amount"
				type="number"
				label="Amount"
				:min="0"
				:disabled="!editable"
			></v-text-field>
		</v-col>
	</div>
</template>

<script>
import { mapState } from "vuex";
import { mapGetters } from "vuex";

export default {
	props: {
		model: Array | Object,
		editable: Boolean
	},
	computed: {
		...mapState("cart", ["discountTypes"]),
		discountType: {
			get() {
				return this.$props.model.discount_type
					? this.$props.model.discount_type
					: "None";
			},
			set(value) {
				this.$set(this.$props.model, "discount_type", value);

				if (value === "None") {
					this.amount = 0;
				}
			}
		},
		amount: {
			get() {
				return this.$props.model.discount_amount;
			},
			set(value) {
				if (value || value === 0) {
					this.$set(this.$props.model, "discount_amount", parseFloat(value));
					this.$store.commit("cart/setDiscount", this.$props.model);
				}
			}
		}
	}
};
</script>