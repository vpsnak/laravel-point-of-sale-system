<template>
	<div class="d-flex">
		<v-col cols="6" class="px-2 py-0">
			<v-select
				:key="model.id"
				v-model="model.discount_type"
				:items="discountTypes"
				label="Discount"
				item-text="label"
				item-value="value"
			></v-select>
		</v-col>
		<v-col cols="6" class="px-2 py-0">
			<v-text-field
				:key="model.id"
				v-if="model.discount_type && model.discount_type !== 'none'"
				v-model="model.discount_amount"
				type="number"
				label="Amount"
				min="1"
				@input="setDiscount"
			></v-text-field>
		</v-col>
	</div>
</template>

<script>
import { mapState } from "vuex";

export default {
	props: {
		model: Array | Object
	},
	computed: {
		...mapState("cart", ["discountTypes"])
	},
	methods: {
		setDiscount() {
			this.$store.commit("cart/setDiscount", this.$props.model);
		}
	}
};
</script>