<template>
	<div class="d-flex flex-grow-1" style="height: 38vh; overflow-y: auto">
		<v-expansion-panels class="d-block" accordion>
			<v-expansion-panel v-for="(product, index) in products" :key="index">
				<v-expansion-panel-header class="pa-3" ripple>
					<v-row no-gutters>
						<v-col cols="8" class="d-flex flex-column">
							<span class="subtitle-2">{{ product.name }}</span>
							<span
								class="body-2"
							>$ {{ product.qty * product.final_price ? product.final_price : product.price }}</span>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn v-if="editable" icon @click.stop="decreaseQty(product)">
								<v-icon color="grey lighten-1">remove</v-icon>
							</v-btn>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-text-field
								:disabled="!editable"
								type="number"
								label="Qty"
								v-model="product.qty"
								min="1"
								@click.stop
							></v-text-field>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn icon v-if="editable" @click.stop="increaseQty(product)">
								<v-icon color="grey lighten-1">add</v-icon>
							</v-btn>
						</v-col>
						<v-col cols="1" class="d-flex align-center justify-center pa-1">
							<v-btn v-if="editable" icon @click.stop="removeItem(product)">
								<v-icon color="grey lighten-1">delete</v-icon>
							</v-btn>
						</v-col>
					</v-row>
				</v-expansion-panel-header>
				<v-expansion-panel-content class="pa-3">
					<v-row no-gutters>
						<v-col cols="12">
							<cartDiscount :model="product" :editable="editable"></cartDiscount>
						</v-col>
					</v-row>
					<v-row no-gutters>
						<v-col cols="12">
							<v-textarea
								v-model="product.notes"
								rows="3"
								label="Notes"
								:hint="'For product: ' + product.name"
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
		products: Array,
		editable: Boolean
	},
	computed: {
		...mapState("cart", ["discountTypes"])
	},
	methods: {
		decreaseQty(product) {
			this.$store.commit("cart/decreaseProductQty", product);
		},
		increaseQty(product) {
			this.$store.commit("cart/increaseProductQty", product);
		},
		removeItem(product) {
			const index = this.products.indexOf(product);
			this.products.splice(index, 1);
		}
	}
};
</script>