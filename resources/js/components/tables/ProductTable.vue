<template>
	<div>
		<prop-data-table
			icon="mdi-package-variant"
			:tableHeaders="headers"
			data-url="products"
			tableTitle="Products"
			tableBtnTitle="New Product"
			tableForm="productForm"
			:tableBtnDisable="false"
			tableViewComponent="product"
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
							@click.stop="editItemDialog(item)"
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
							@click.stop="viewItemDialog(item)"
							class="my-1"
							v-on="on"
							icon
						>
							<v-icon small>watch</v-icon>
						</v-btn>
					</template>
					<span>View</span>
				</v-tooltip>
			</template>
		</prop-data-table>
	</div>
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
		...mapMutations("dialog", ["setDialog", "resetDialog"]),
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
		},

		dialogEvent(event) {
			this.resetDialog();
		},
		editItemDialog(item) {
			this.dialog = {
				show: true,
				fullscreen: false,
				width: 600,
				title: `Edit item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-pencil",
				component: this.form,
				model: _.cloneDeep(item),
				persistent: true
			};
		},
		viewItemDialog(item) {
			this.dialog = {
				show: true,
				fullscreen: false,
				width: 600,
				title: `View item #${item.id}`,
				titleCloseBtn: true,
				icon: "mdi-watch",
				component: this.form,
				model: item,
				persistent: false
			};
		}
	}
};
</script>
