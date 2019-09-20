<template>
	<v-card>
		<v-card-title>
			Products
			<v-spacer></v-spacer>
			<v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
		</v-card-title>
		<v-data-table :headers="headers" :items="productList" :search="search" :loading="loader">
			<v-alert
				slot="no-results"
				:value="true"
				color="error"
				icon="warning"
			>Your search for "{{ search }}" found no results.</v-alert>
		</v-data-table>
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
				search: "",
				headers: [
					{
						text: "Products",
						align: "left",
						sortable: false,
						value: "name"
					},
					{ text: "Id", value: "id" },
					{ text: "Sku", value: "sku" },
					{ text: "Photo url", value: "photo_url" },
					{ text: "Price_id", value: "price_id" }
				]
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
			}
		}
	};
</script>