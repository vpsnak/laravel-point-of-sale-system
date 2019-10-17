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
                    @click:clear="getAllProducts"
                ></v-text-field>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn @click="showCreateDialog = true" class="my-1" v-on="on" icon>
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <span>Add dummy product</span>
                </v-tooltip>
            </v-row>

            <v-row align="center" justify="center">
                <v-col>
                    <v-combobox
                        v-model="selectedCategories"
                        :items="categoryList"
                        item-text="name"
                        chips
                        clearable
                        label="Filter by category"
                        multiple
                        prepend-icon="filter_list"
                        solo
                    ></v-combobox>
                </v-col>
            </v-row>
            <v-row v-if="productList.length" style="height:61vh; overflow-y:auto;">
                <v-col v-for="product in productList" :key="product.id" cols="12" md="6" lg="4">
                    <v-card :img="product.photo_url" @click="addProduct(product)" height="170px">
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
                                    <v-list-item @click="addToFavorites(product)">
                                        <v-icon class="pr-2">mdi-heart</v-icon>
                                        <h5>Add to favorites</h5>
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
        <interactiveDialog
            v-if="showCreateDialog"
            :show="showCreateDialog"
            :model="{}"
            component="dummyProductForm"
            title="Add a dummy product"
            @action="result"
            cancelBtnTxt="Close"
            persistent
            titleCloseBtn
        ></interactiveDialog>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            showCreateDialog: false,
            loader: false,
            model: "products",
            keyword: "",
            selectedCategories: []
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

                if (this.selectedCategories.length) {
                    let filteredProducts = [];

                    this.$store.state.productList.forEach(product => {
                        product.categories.forEach(productCategory => {
                            this.selectedCategories.forEach(category => {
                                if (
                                    category.id === productCategory.id &&
                                    !_.includes(filteredProducts, product)
                                ) {
                                    filteredProducts.push(product);
                                }
                            });
                        });
                    });

                    return filteredProducts;
                } else {
                    return this.$store.state.productList;
                }
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

            let payload = {
                model: "products",
                mutation: "setProductList"
            };
            this.$store.dispatch("getAll", payload).finally(() => {
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
        searchProduct() {
            if (this.keyword.length > 0) {
                this.selectedCategories = [];

                this.initiateLoadingSearchResults(true);

                let payload = {
                    model: "products",
                    mutation: "setProductList",
                    keyword: this.keyword
                };

                this.$store.dispatch("search", payload).finally(() => {
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
            this.showCreateDialog = false;
        }
    }
};
</script>