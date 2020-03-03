<template>
  <v-card class="pa-3 d-flex flex-column">
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
          @click:clear="(currentPage = 1), getAllProducts()"
        ></v-text-field>

        <v-btn
          class="mx-2"
          @click="btnactive = !btnactive"
          :color="btnactive ? 'primary' : 'secondary'"
        >
          <v-icon left>mdi-barcode-scan</v-icon>
          <v-icon right>
            {{ btnactive ? "mdi-cart-arrow-down" : "mdi-eye" }}
          </v-icon>
        </v-btn>

        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn class="mx-2 my-2" @click="addDummyProductDialog()" v-on="on">
              <v-icon left>mdi-flower</v-icon>Custom <br />Product
            </v-btn>
          </template>
          <span>Add custom product</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn class="mx-2 my-2" @click="giftcardDialog()" v-on="on">
              <v-icon left>mdi-credit-card-plus</v-icon>Gift <br />Card
            </v-btn>
          </template>
          <span>Add gift card</span>
        </v-tooltip>
      </v-row>

      <v-row align="center" justify="center">
        <v-col>
          <v-slide-group
            show-arrows
            v-model="selectedCategory"
            @change="keyword = ''"
          >
            <v-slide-item
              v-for="category in categories"
              :key="category.id"
              v-slot:default="{ active, toggle }"
              :value="category.id"
            >
              <v-btn
                :color="active ? 'secondary' : ''"
                class="mx-1"
                @click="toggle"
                depressed
                rounded
                >{{ category.name }}
              </v-btn>
            </v-slide-item>
          </v-slide-group>
        </v-col>
      </v-row>

      <v-row v-if="products.length">
        <v-container
          style="max-height:61vh;"
          class="overflow-y-auto fill-height"
        >
          <v-col
            v-for="product in products"
            :key="product.id"
            :cols="12"
            :md="6"
            :lg="4"
          >
            <v-card
              dark
              v-model="current_product"
              :img="product.photo_url"
              @click="addProduct(product)"
              height="170px"
            >
              <v-card-title class="secondary pa-0" @click.stop>
                <h6 class="px-2">{{ truncate(product.name) }}</h6>
                <v-spacer />
                <v-menu bottom left>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="viewProductDialog(product)">
                      <v-icon class="pr-2">mdi-eye</v-icon>
                      <h5>View product</h5>
                    </v-list-item>
                    <v-list-item
                      :href="product.plantcare_pdf"
                      target="_blank"
                      link
                      :disabled="!product.plantcare_pdf"
                    >
                      <v-icon class="pr-2">mdi-flower-outline</v-icon>
                      <h5>Plant care</h5>
                    </v-list-item>
                    <v-list-item
                      :href="product.url"
                      target="_blank"
                      link
                      :disabled="!product.url"
                    >
                      <v-icon class="pr-2">mdi-alpha-m-circle-outline</v-icon>
                      <h5>View on Magento</h5>
                    </v-list-item>
                    <v-divider />
                    <v-list-item @click="printProductBarcode(product)">
                      <v-icon class="pr-2">mdi-barcode</v-icon>
                      <h5>Print barcode</h5>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-card-title>
              <v-card-actions>
                <div class="d-flex flex-column">
                  <v-chip small class="grey darken-1 mt-2 ml-1 elevation-12">
                    <span>SKU: {{ product.sku }}</span>
                  </v-chip>
                  <v-chip small class="secondary mt-2 ml-1 elevation-12">
                    <span>
                      Price:
                      {{ displayPrice(product.price) }}
                    </span>
                  </v-chip>
                  <v-chip
                    v-if="stockColor(product)"
                    small
                    class="mt-2 ml-1 elevation-12"
                    :color="stockColor(product)"
                  >
                    <span>Stock: {{ product.stock }}</span>
                  </v-chip>
                </div>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-container>

        <v-col :cols="12">
          <v-pagination
            v-model="currentPage"
            @input="paginate"
            :length="lastPage"
            color="secondary"
            :disabled="loader"
            @previous="currentPage -= 1"
            @next="currentPage += 1"
          ></v-pagination>
        </v-col>
      </v-row>
      <v-row
        v-else
        align="center"
        justify="center"
        style="height: 58vh; overflow-y: auto;"
      >
        <v-progress-circular
          v-if="loader"
          indeterminate
          color="secondary"
        ></v-progress-circular>
        <h2 v-else-if="!productList.length">No products found</h2>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import Dinero from "dinero.js";
import { mapMutations, mapState, mapActions } from "vuex";

export default {
  data() {
    return {
      categories: [],
      current_product: null,
      current_page: 1,
      last_page: null,
      viewId: null,
      loader: false,
      search_keyword: "",
      selected_category: null,
      btnactive: true
    };
  },

  mounted() {
    this.getAllProducts();
    this.getAllCategories();
    this.$root.$on("barcodeScan", sku => {
      if (!this.interactive_dialog.show) {
        this.getSingleProduct(sku);
      }
    });
  },

  beforeDestroy() {
    this.$root.$off("barcodeScan");
  },

  computed: {
    ...mapState(["productList"]),
    ...mapState("dialog", ["interactive_dialog"]),

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
    products: {
      get() {
        return this.productList;
      },
      set(value) {
        this.setProductList(value);
      }
    }
  },
  methods: {
    ...mapActions("requests", ["request"]),
    ...mapMutations(["setProductList"]),
    ...mapMutations("cart", ["addProduct"]),
    ...mapMutations("dialog", ["viewItem", "setDialog"]),

    displayPrice(price) {
      return Dinero(price).toFormat("$0,0.00");
    },
    stockColor(product) {
      if (product.stock <= 10 && product.stock > 0) {
        return "orange";
      } else if (product.stock <= 0) {
        return "red";
      }
    },
    truncate(string) {
      return _.truncate(string);
    },
    printProductBarcode(product) {
      window.open(`/product_barcode/${product.id}`, "_blank");
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
        this.products = [];
      } else {
        this.loader = false;
      }
    },
    getAllProducts() {
      this.initiateLoadingSearchResults(true);
      this.selected_category = null;

      let payload = {
        method: "get",
        url: "products" + "?page=" + this.current_page
      };
      this.request(payload)
        .then(response => {
          this.setProductList(response.data);
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
        })
        .finally(() => {
          this.initiateLoadingSearchResults();
        });
    },
    getAllCategories() {
      this.initiateLoadingSearchResults(true);

      let payload = {
        method: "get",
        url: "product-listing/categories"
      };
      this.request(payload)
        .then(response => {
          this.categories = response.data;
        })
        .finally(() => {
          this.initiateLoadingSearchResults(false);
        });
    },
    getProductsFromCategoryID() {
      this.initiateLoadingSearchResults(true);
      const page = this.currentPage ? "?page=" + this.currentPage : "";
      const payload = {
        method: "get",
        url: `categories/${this.selectedCategory}/products${page}`
      };

      this.request(payload)
        .then(response => {
          this.setProductList(response.data);
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
        })
        .finally(() => {
          this.initiateLoadingSearchResults();
        });
    },
    getSingleProduct(sku) {
      this.initiateLoadingSearchResults(true);

      let payload = {
        method: "post",
        url: `products/search?page=${this.currentPage}`,
        data: { keyword: sku }
      };

      this.request(payload)
        .then(response => {
          this.setProductList(response.data);
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
          if (_.isObjectLike(response[0])) {
            if (this.btnactive) {
              this.addProduct(response[0]);
            } else {
              response[0].form = "product";
              this.viewProductDialog(response[0]);
            }
          }
        })
        .finally(() => {
          this.initiateLoadingSearchResults();
        });
    },
    searchProduct() {
      this.selected_category = null;

      if (this.keyword.length > 0) {
        this.initiateLoadingSearchResults(true);
        let payload = {
          method: "post",
          url: `products/search?page=${this.currentPage}`,
          data: { keyword: this.keyword }
        };

        this.request(payload)
          .then(response => {
            this.setProductList(response.data);
            this.currentPage = response.current_page;
            this.lastPage = response.last_page;
          })
          .finally(() => {
            this.initiateLoadingSearchResults();
          });
      } else {
        this.getAllProducts();
      }
    },
    viewProductDialog(product) {
      product.form = "product";
      this.viewItem(product);
    },
    giftcardDialog() {
      this.setDialog({
        show: true,
        component: "giftCardToCartForm",
        title: "Add a gift card",
        cancelBtnTxt: "Close"
      });
    },
    addDummyProductDialog() {
      this.setDialog({
        show: true,
        component: "dummyProductForm",
        title: "Add a custom product",
        cancelBtnTxt: "Close"
      });
    }
  }
};
</script>
