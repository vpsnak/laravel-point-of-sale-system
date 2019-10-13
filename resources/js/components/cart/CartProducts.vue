<template>
	<div class="d-flex flex-grow-1" style="height:38vh; overflow-y:auto">
		<v-expansion-panels class="d-block" accordion>
			<v-expansion-panel v-for="(product, index) in products" :key="index">
				<v-expansion-panel-header class="pa-3" ripple @click.stop>
					<div class="d-flex align-center justify-space-between">
						<div class="d-flex flex-column">
							<span class="subtitle-2">{{ product.name }}</span>
							<span class="body-2">$ {{ price(product) }}</span>
						</div>
						<v-spacer />
						<div class="d-flex justify-content-center align-center">
							<v-btn v-if="editable" small icon @click.stop="decreaseQty(product)">
								<v-icon small class="px-1">remove</v-icon>
							</v-btn>

							<v-text-field
								class="px-1"
								style="max-width:45px;"
								:disabled="!editable"
								type="number"
								label="Qty"
								v-model="product.qty"
								min="1"
								@click.stop
								@blur="limits(product)"
								@keyup="limits(product)"
							></v-text-field>

							<v-btn icon small v-if="editable" @click.stop="increaseQty(product)">
								<v-icon small class="px-1">add</v-icon>
							</v-btn>

							<v-btn v-if="editable" small icon @click.stop="removeItem(product)">
								<v-icon small class="px-1">delete</v-icon>
							</v-btn>
						</div>
					</div>
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
		price(product) {
			if (product.qty) {
				return product.qty * product.final_price || product.qty * product.price;
			} else {
				return product.final_price || product.price;
			}
		},
		limits(product) {
			if (product.qty < 1) {
				product.qty = 1;
			}
		},
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