<template>
	<v-card class="pa-3 d-flex flex-column" style="max-height: 90vh;">
		<div class="d-flex justify-center align-center pt-5">
			<v-icon class="pr-2">mdi-package-variant</v-icon>
			<h4 class="title-2">Products</h4>
		</div>

		<v-card-text>
			<v-row align="center" justify="center">
				<v-btn icon @click="searchProduct" class="mx-2">
					<v-icon>mdi-magnify</v-icon>
				</v-btn>
				<v-text-field
					v-model="keyword"
					placeholder="Search product"
					class="mx-2"
					@keyup.enter="searchProduct"
					clearable
					@click:clear="currentPage = 1, getAllProducts()"
				></v-text-field>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn outlined icon @click="enableScan" :color=" btnactive ? 'red' : ''" v-on="on">
							<v-icon>mdi-barcode-scan</v-icon>
						</v-btn>
					</template>
					<span v-if="btnactive">Disable scan mode</span>
					<span v-else>Enable scan mode</span>
				</v-tooltip>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn @click="showDummyDialog = true" class="my-1" v-on="on" icon>
							<v-icon>mdi-plus</v-icon>
						</v-btn>
					</template>
					<span>Add dummy product</span>
				</v-tooltip>
				<v-tooltip bottom>
					<template v-slot:activator="{ on }">
						<v-btn @click="showGiftCardDialog = true" class="my-1" v-on="on" icon>
							<v-icon>mdi-credit-card-plus</v-icon>
						</v-btn>
					</template>
					<span>Add gift card</span>
				</v-tooltip>
			</v-row>

			<v-row align="center" justify="center">
				<v-col>
					<v-slide-group show-arrows v-model="selectedCategory" @change="keyword = ''">
						<v-slide-item
							v-for="category in categoryList"
							:key="category.id"
							v-slot:default="{ active, toggle }"
							:value="category.id"
						>
							<v-btn
								:color="active ? 'blue-grey' : ''"
								class="mx-1"
								@click="toggle"
								depressed
								rounded
							>{{ category.name }}</v-btn>
						</v-slide-item>
					</v-slide-group>
				</v-col>
			</v-row>
			<v-row v-if="productList.length" style="height:61vh; overflow-y:auto;">
				<v-col v-for="product in productList" :key="product.id" cols="12" md="6" lg="4">
					<v-card
						v-model="current_product"
						:img="product.photo_url"
						@click="addProduct(product)"
						height="170px"
					>
						<v-card-title class="blue-grey pa-0" @click.stop>
							<h6 class="px-2">{{product.name}}</h6>
							<div class="flex-grow-1"></div>
							<v-menu bottom left>
								<template v-slot:activator="{ on }">
									<v-btn dark icon v-on="on">
										<v-icon>mdi-dots-vertical</v-icon>
									</v-btn>
								</template>
								<v-list>
									<v-list-item @click="viewItem(product)">
										<v-icon class="pr-2">mdi-eye</v-icon>
										<h5>View product</h5>
									</v-list-item>
									<v-list-item :href="product.plantcare_pdf" link :disabled="!product.plantcare_pdf">
										<v-icon class="pr-2">mdi-flower-outline</v-icon>
										<h5>Plant care</h5>
									</v-list-item>
									<v-list-item :href="product.url" link :disabled="!product.url">
										<v-icon class="pr-2">fab fa-magento</v-icon>
										<h5>View on Magento</h5>
									</v-list-item>
								</v-list>
							</v-menu>
						</v-card-title>
						<v-card-actions>
							<v-chip class="mt-2 ml-1">
								<span>Price: {{ parseFloat(product.final_price).toFixed(2) }} $</span>
							</v-chip>
							<v-chip v-if="product.final_price != product.price.amount" class="mt-2 ml-1">
								<span>Net Price: {{ parseFloat(product.final_price).toFixed(2) }} $</span>
							</v-chip>
						</v-card-actions>
					</v-card>
				</v-col>
			</v-row>
			<v-row v-else align="center" justify="center" style="height: 58vh; overflow-y: auto;">
				<h2 v-if="!loader">No products found</h2>
				<v-progress-circular v-else dark indeterminate color="white"></v-progress-circular>
			</v-row>
		</v-card-text>
		<v-pagination
			v-model="currentPage"
			@input="paginate"
			:length="lastPage"
			color="blue-grey"
			:disabled="loader"
			@previous="currentPage -= 1"
			@next="currentPage += 1"
		></v-pagination>

		<interactiveDialog
			v-if="showDummyDialog"
			:show="showDummyDialog"
			component="dummyProductForm"
			title="Add a dummy product"
			@action="result"
			cancelBtnTxt="Close"
			titleCloseBtn
		></interactiveDialog>

		<interactiveDialog
			v-if="showGiftCardDialog"
			:show="showGiftCardDialog"
			component="giftCardToCartForm"
			title="Add a gift card"
			@action="result"
			cancelBtnTxt="Close"
			titleCloseBtn
		></interactiveDialog>

		<interactiveDialog
			v-if="showViewDialog"
			:show="showViewDialog"
			title="View item"
			:fullscreen="false"
			:width="1000"
			component="product"
			:model="viewProduct"
			@action="result"
			action="newItem"
			cancelBtnTxt="Close"
		></interactiveDialog>
	</v-card>
</template>

<script>
import { log } from "util";
export default {
	data() {
		return {
			current_product: null,
			current_page: 1,
			last_page: null,
			showViewDialog: false,
			showDummyDialog: false,
			showGiftCardDialog: false,
			viewId: null,
			loader: false,
			model: "products",
			search_keyword: "",
			selected_category: null,
			btnactive: true
		};
	},
	mounted() {
		this.getAllProducts();
		this.getAllCategories();
		this.$root.$on("barcodeScan", sku => {
			if (this.btnactive) {
				this.getSingleProduct(sku, true);
			} else {
				this.getSingleProduct(sku, false);
			}
		});
	},
	beforeDestroy() {
		this.$root.$off("barcodeScan");
	},
	computed: {
		keyword: {
			get() {
				return this.search_keyword;
			},
			set(value) {
				if (!this.keyword) {
					this.currentPage = 1;
				}
				this.search_keyword = value;
			}
		},
		currentPage: {
			get() {
				return this.current_page;
			},
			set(value) {
				this.current_page = value;
			}
		},
		lastPage: {
			get() {
				return this.last_page;
			},
			set(value) {
				this.last_page = value;
			}
		},
		selectedCategory: {
			get() {
				return this.selected_category;
			},
			set(value) {
				if (!this.selected_category) {
					this.currentPage = 1;
				}

				this.selected_category = value;

				if (value) {
					this.getProductsFromCategoryID();
				} else {
					this.getAllProducts();
				}
			}
		},
		productList: {
			get() {
				return this.$store.state.productList;
			},
			set(value) {
				this.$store.state.productList = value;
			}
		},
		categoryList: {
			get() {
				return this.$store.state.categoryList;
			},
			set(value) {
				this.$store.state.categoryList = value;
			}
		}
	},
	methods: {
		enableScan() {
			if (this.btnactive == false) {
				this.btnactive = true;
			} else {
				this.btnactive = false;
			}
		},
		paginate() {
			if (this.selectedCategory) {
				this.getProductsFromCategoryID();
			} else if (this.keyword) {
				this.searchProduct();
			} else {
				this.getAllProducts();
			}
		},

		initiateLoadingSearchResults(loading) {
			if (loading) {
				this.loader = true;
				this.productList = [];
			} else {
				this.loader = false;
			}
		},
		getAllProducts() {
			this.initiateLoadingSearchResults(true);
			this.selected_category = null;

			let payload = {
				model: "products",
				mutation: "setProductList",
				page: this.current_page
			};
			this.$store
				.dispatch("getAll", payload)
				.then(response => {
					this.currentPage = response.current_page;
					this.lastPage = response.last_page;
				})
				.finally(() => {
					this.initiateLoadingSearchResults(false);
				});
		},
		getAllCategories() {
			this.initiateLoadingSearchResults(true);

			let payload = {
				url: "product-listing/categories"
			};
			this.$store
				.dispatch("getRequest", payload, { root: true })
				.then(result => {
					this.$store.commit("setCategoryList", result.data);
				})
				.finally(() => {
					this.initiateLoadingSearchResults(false);
				});
		},
		getProductsFromCategoryID() {
			this.initiateLoadingSearchResults(true);
			let payload = {
				model: "categories",
				mutation: "setProductList",
				page: this.currentPage,
				data: {
					id: this.selectedCategory,
					model: "products"
				}
			};
			this.$store
				.dispatch("getManyByOne", payload)
				.then(response => {
					this.currentPage = response.current_page;
					this.lastPage = response.last_page;
				})
				.finally(() => {
					this.initiateLoadingSearchResults(false);
				});
		},
		getSingleProduct(sku, addToCart) {
			this.showViewDialog = false;
			this.initiateLoadingSearchResults(true);
			let payload = {
				model: "products",
				mutation: "setProductList",
				page: this.currentPage,
				keyword: sku
			};
			this.$store
				.dispatch("search", payload)
				.then(response => {
					this.currentPage = response.current_page;
					this.lastPage = response.last_page;
					if (addToCart) {
						this.$store.commit("cart/addProduct", response.data[0]);
					} else if (response.data[0]) {
						this.viewItem(response.data[0]);
					}
				})
				.finally(() => {
					this.initiateLoadingSearchResults(false);
				});
		},
		searchProduct() {
			this.selected_category = null;

			if (this.keyword.length > 0) {
				this.initiateLoadingSearchResults(true);

				let payload = {
					model: "products",
					mutation: "setProductList",
					page: this.currentPage,
					keyword: this.keyword
				};

				this.$store
					.dispatch("search", payload)
					.then(response => {
						this.currentPage = response.current_page;
						this.lastPage = response.last_page;
					})
					.finally(() => {
						this.initiateLoadingSearchResults(false);
					});
			} else {
				this.getAllProducts();
			}
		},
		addProduct(product) {
			this.$store.commit("cart/addProduct", product);
		},
		result(event) {
			this.showDummyDialog = false;
			this.showGiftCardDialog = false;
			this.showViewDialog = false;
		},

		viewItem(product) {
			this.viewProduct = product;
			this.showViewDialog = true;
		}
	}
};
</script>