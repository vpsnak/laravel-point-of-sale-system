<template>
	<div class="d-flex flex-grow-1" style="max-height: 44vh; overflow-y: auto">
		<v-expansion-panels class="d-block" accordion>
			<v-expansion-panel v-for="cartProduct in cartProducts" :key="cartProduct.id">
				<v-expansion-panel-header class="pa-3" ripple>
					<v-row no-gutters>
						<v-col cols="8" class="d-flex flex-column">
							<span class="subtitle-2">{{ cartProduct.name }}</span>
							<span class="body-2">$ {{ cartProduct.qty * cartProduct.price.amount }}</span>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn v-if="editable" icon @click.stop="decreaseQty(cartProduct)">
								<v-icon color="grey lighten-1">remove</v-icon>
							</v-btn>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-text-field
								:disabled="!editable"
								type="number"
								label="Qty"
								v-model="cartProduct.qty"
								min="1"
								@click.stop
							></v-text-field>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn icon v-if="editable" @click.stop="increaseQty(cartProduct)">
								<v-icon color="grey lighten-1">add</v-icon>
							</v-btn>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn v-if="editable" icon @click.stop="removeItem(cartProduct)">
								<v-icon color="grey lighten-1">delete</v-icon>
							</v-btn>
						</v-col>
					</v-row>
				</v-expansion-panel-header>
				<v-expansion-panel-content class="pa-3">
					<v-row no-gutters>
						<v-col cols="12">
							<cartDiscount :model="cartProduct" :editable="editable"></cartDiscount>
						</v-col>
					</v-row>
					<v-row no-gutters>
						<v-col cols="12">
							<v-textarea
								v-model="cartProduct.notes"
								rows="3"
								label="Notes"
								:hint="'For product: ' + cartProduct.name"
								counter
								no-resize
								:disabled="!editable"
							></v-textarea>
						</v-col>
					</v-row>
				</v-expansion-panel-content>
			</v-expansion-panel>
		</v-expansion-panels>
	</div>
</template>

<script>
import { mapState } from "vuex";
export default {
	props: {
		cartProducts: Array,
		editable: Boolean
	},
	computed: {
		...mapState("cart", ["discountTypes"])
	},
	methods: {
		decreaseQty(cartProduct) {
			this.$store.commit("cart/decreaseCartProductQty", cartProduct);
		},
		increaseQty(cartProduct) {
			this.$store.commit("cart/increaseCartProductQty", cartProduct);
		},
		removeItem(cartProduct) {
			this.cartProducts.splice(cartProduct, 1);
		}
	}
};
</script>