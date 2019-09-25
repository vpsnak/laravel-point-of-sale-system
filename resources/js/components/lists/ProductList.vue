<template>
	<v-card>
		<v-card-title>
			<v-row align="center" justify="center">
				<v-col align="center" justify="center">
					<v-icon>fa-dice-d6</v-icon>
					<h5 class="text-center">Products</h5>
				</v-col>
			</v-row>
		</v-card-title>
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
				></v-text-field>
			</v-row>
			<v-row justify="center">
				<v-btn-toggle v-model="toggle_one" v-for="category in categoryList" :key="category.id">
					<v-btn :disabled="disableFilters" @click="setProductListByCategoryProducts(category)" text>
						<span class="hidden-sm-and-down">{{category.name}}</span>
					</v-btn>
				</v-btn-toggle>
			</v-row>
			<v-row v-if="productList.length" style="height:58vh; overflow-y:auto;">
				<v-col v-for="product in productList" :key="product.id" cols="12" md="6" lg="4">
					<v-card :img="product.photo_url" @click="addCartProduct(product)" height="170px">
						<v-card-title class="indigo white--text pa-0" @click.stop>
							<h6 class="px-2">{{product.name}}</h6>
							<div class="flex-grow-1"></div>

							<v-menu bottom left>
								<template v-slot:activator="{ on }">
									<v-btn dark icon v-on="on">
										<v-icon>mdi-dots-vertical</v-icon>
									</v-btn>
								</template>

								<v-list>
									<v-list-item @click="addToFavorites(product)">
										<v-icon class="pr-2">mdi-heart</v-icon>
										<h5>Add to favorites</h5>
									</v-list-item>
									<v-list-item @click="viewProduct(product)">
										<v-icon class="pr-2">remove_red_eye</v-icon>
										<h5>View</h5>
									</v-list-item>
									<v-list-item @click="editProduct(product)">
										<v-icon class="pr-2">edit</v-icon>
										<h5>Edit</h5>
									</v-list-item>
								</v-list>
							</v-menu>
						</v-card-title>
					</v-card>
				</v-col>
			</v-row>
			<v-row v-else align="center" justify="center" style="height: 58vh; overflow-y: auto;">
				<h2 v-if="!loader">No products found</h2>
				<v-progress-circular v-else dark indeterminate color="white"></v-progress-circular>
			</v-row>
		</v-card-text>
		<v-card-actions></v-card-actions>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			loader: false,
			disableFilters: false,
			model: "products",
			keyword: "",
			toggle_one: undefined
		};
	},
	mounted() {
		this.getAllProducts();
		this.getAllCategories();
	},
	computed: {
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
		addToFavorites(product) {},
		viewProduct(product) {},
		editProduct(product) {},
		applyFilter(filter) {},
		initiateLoadingSearchResults(loading) {
			if (loading) {
				this.loader = true;
				this.disableFilters = true;
				this.productList = [];
			} else {
				this.loader = false;
				this.disableFilters = false;
			}
		},

		setProductListByCategoryProducts(category) {
			this.productList = category.products;
		},
		getAllProducts() {
			this.initiateLoadingSearchResults(true);

			let payload = {
				model: "products",
				mutation: "setProductList"
			};
			this.$store
				.dispatch("getAll", payload)
				.then(result => {
					this.initiateLoadingSearchResults(false);
				})
				.catch(error => {
					this.initiateLoadingSearchResults(false);
					console.log(error);
				});
		},
		getAllCategories() {
			this.initiateLoadingSearchResults(true);

			let payload = {
				model: "categories",
				mutation: "setCategoryList"
			};
			this.$store
				.dispatch("getAll", payload)
				.then(result => {
					this.initiateLoadingSearchResults(false);
				})
				.catch(error => {
					this.initiateLoadingSearchResults(false);
					console.log(error);
				});
		},
		searchProduct() {
			if (this.keyword.length > 0) {
				this.initiateLoadingSearchResults(true);

				let payload = {
					model: "products",
					mutation: "setProductList",
					keyword: this.keyword
				};

				this.$store
					.dispatch("search", payload)
					.then(result => {
						this.initiateLoadingSearchResults(false);
					})
					.catch(error => {
						this.initiateLoadingSearchResults(false);
					});
			}
		},
		addCartProduct(product) {
			this.$store.commit("cart/addCartProduct", product);
		}
	}
};
</script>