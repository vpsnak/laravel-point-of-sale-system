<template>
	<data-table
		icon="mdi-package-variant"
		title="Products"
		:headers="headers"
		data-url="products"
		btnTxt="New Product"
		:newForm="form"
		:disableNewBtn="false"
	>
		<template v-slot:item.final_price="{ item }">${{ parseFloat(item.final_price).toFixed(2) }}</template>

		<template v-slot:item.photo_url="{ item }">
			<v-img :src="item.photo_url" :lazy-src="item.photo_url" :width="40" :height="40"></v-img>
		</template>

		<template v-slot:item.stock="{ item }">
			<h4 :class="getStockColor(item.stock)">{{ item.stock }}</h4>
		</template>

		<template v-slot:item.actions="{ item }">
			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						@click.stop="printProductBarcode(item) "
						:disabled="disableActions"
						class="my-2"
						icon
						v-on="on"
					>
						<v-icon>mdi-barcode</v-icon>
					</v-btn>
				</template>
				<span>Print Barcode</span>
			</v-tooltip>

			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="disableActions"
						@click.stop="item.form=form,editItem(item)"
						class="my-2"
						v-on="on"
						icon
					>
						<v-icon small>edit</v-icon>
					</v-btn>
				</template>
				<span>Edit</span>
			</v-tooltip>

			<v-tooltip bottom>
				<template v-slot:activator="{ on }">
					<v-btn
						small
						:disabled="disableActions"
						@click.stop="item.form=form,viewItem(item)"
						class="my-1"
						v-on="on"
						icon
					>
						<v-icon small>mdi-eye</v-icon>
					</v-btn>
				</template>
				<span>View</span>
			</v-tooltip>
		</template>
	</data-table>
</template>
<script>
import { mapState, mapMutations } from "vuex";

export default {
	data() {
		return {
			form: "productForm",
			headers: [
				{ text: "#", value: "id" },
				{ text: "Image", value: "photo_url", shortable: false },
				{ text: "Name", value: "name" },
				{ text: "Sku", value: "sku" },
				{ text: "Stock", value: "stock" },
				{ text: "Price", value: "final_price" },
				{ text: "Actions", value: "actions", shortable: false }
			]
		};
	},
	computed: {
		...mapState("dialog", ["interactive_dialog"]),
		...mapState("datatable", ["loading"]),

		dialog: {
			get() {
				return this.interactive_dialog;
			},
			set(value) {
				this.setDialog(value);
			}
		},

		disableActions: {
			get() {
				return this.loading;
			},
			set(value) {
				this.setLoading(value);
			}
		},
		role() {
			return this.$store.getters.role;
		}
	},
	methods: {
		...mapMutations("dialog", ["viewItem", "editItem"]),
		...mapMutations("datatable", ["setLoading"]),

		getStockColor(stock) {
			if (stock <= 0) {
				return "red--text";
			} else if (stock <= 10) {
				return "orange--text";
			} else {
				return "";
			}
		},
		printProductBarcode(item) {
			window.open(`/product_barcode/${item.id}`, "_blank");
		}
	}
};
</script>
