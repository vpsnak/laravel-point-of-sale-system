<template>
	<v-card>
		<v-card-text>
			<v-row align="center" justify="center">
				<v-col align="center" justify="center">
					<v-icon>fa-dice-d6</v-icon>
					<h3 class="text-center">Products</h3>
				</v-col>
			</v-row>
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

				<v-btn class="mx-2">
					<v-icon>add</v-icon>
					<span>Add product</span>
				</v-btn>
			</v-row>
			<v-row align="center" justify="center">
				<v-btn-toggle>
					<v-btn :disabled="disableFilters" @click="applyFilter()" text>
						<v-icon left small>fas fa-heart</v-icon>
						<span class="hidden-sm-and-down">Favourites</span>
					</v-btn>
					<v-btn :disabled="disableFilters" @click="applyFilter()" text>
						<v-icon left small>fas fa-sort-numeric-up</v-icon>
						<span class="hidden-sm-and-down">Best Sellers</span>
					</v-btn>
					<v-btn :disabled="disableFilters" text>
						<v-icon left small>fas fa-spa</v-icon>
						<span class="hidden-sm-and-down">Roses</span>
					</v-btn>
					<v-btn :disabled="disableFilters" @click="applyFilter()" text>
						<v-icon left small>fas fa-seedling</v-icon>
						<span class="hidden-sm-and-down">Succulents</span>
					</v-btn>
				</v-btn-toggle>
			</v-row>
			<v-row v-if="productList.length" align="center" style="height:58vh; overflow-y:auto;">
				<v-col v-for="product in productList" :key="product.id" cols="12" md="6" lg="4" xl="3">
					<v-card>
						<v-img
							:src="product.photo_url"
							class="white--text"
							height="125px"
							gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
						>
							<v-card-title class="fill-height align-end" v-text="product.name"></v-card-title>
						</v-img>

						<v-card-actions>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon class="ml-1" @click="addCartProduct(product)">
										<v-icon>add_shopping_cart</v-icon>
									</v-btn>
								</template>
								<span>Add to shopping cart</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>mdi-heart</v-icon>
									</v-btn>
								</template>
								<span>Add to favorites</span>
							</v-tooltip>
							<div class="flex-grow-1"></div>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>remove_red_eye</v-icon>
									</v-btn>
								</template>
								<span>View product</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on }">
									<v-btn icon v-on="on" class="ml-1">
										<v-icon>edit</v-icon>
									</v-btn>
								</template>
								<span>Edit product</span>
							</v-tooltip>
						</v-card-actions>
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
			keyword: ""
		};
	},
	mounted() {
		this.getAllProducts();
	},
	computed: {
		productList: {
			get() {
				return this.$store.state.productList;
			},
			set(value) {
				this.$store.state.productList = value;
			}
		}
	},
	methods: {
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
			this.$store.commit("addCartProduct", product);
		}
	}
};
</script>