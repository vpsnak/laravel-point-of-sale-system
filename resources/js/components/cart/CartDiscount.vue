<template>
	<div class="d-flex">
		<v-col cols="6" class="px-2 py-0">
			<v-select
				:key="model.id"
				v-model="discountType"
				:items="discountTypes"
				label="Discount"
				item-text="label"
				item-value="value"
			></v-select>
		</v-col>
		<v-col cols="6" class="px-2 py-0">
			<v-text-field
				:key="model.id"
				v-if="discountType && discountType !== 'none'"
				v-model="discountAmount"
				type="number"
				label="Amount"
				min="1"
			></v-text-field>
		</v-col>
	</div>
</template>

<script>
import { mapState } from "vuex";
import { mapGetters } from "vuex";

export default {
	props: {
		model: Array | Object
	},
	computed: {
		...mapState("cart", ["discountTypes"]),
		discountType: {
			get() {
				return this.$props.model.discount_type;
			},
			set(value) {
				this.$set(this.$props.model, "discount_type", value);
			}
		},
		discountAmount: {
			get() {
				return this.$props.model.discount_amount;
			},
			set(value) {
				this.$set(this.$props.model, "discount_amount", value);
				this.$store.commit("cart/setDiscount", this.$props.model);
			}
		}
	},
	methods: {
		setDiscount() {}
	}
};
</script>