<template>
  <v-card class="fill-height">
    <v-card-text>
      <v-container fluid>
        <v-row>
          <v-btn
            class="mx-1"
            @click="btnactive = !btnactive"
            :color="btnactive ? 'primary' : null"
          >
            <v-icon left v-text="'mdi-barcode-scan'" />
            <v-icon v-text="'mdi-chevron-right'" />
            <v-icon right v-text="btnactive ? 'mdi-cart' : 'mdi-eye'" />
          </v-btn>

          <v-text-field
            :disabled="productLoading"
            dense
            class="mx-1"
            outlined
            solo
            v-model="keyword"
            prepend-inner-icon="mdi-magnify"
            @click:prepend-inner="searchProduct()"
            placeholder="Search product"
            @keyup.enter="searchProduct"
            clearable
            @click:clear="(currentPage = 1), getAllProducts()"
          />

          <v-menu :nudge-width="200" offset-x>
            <template v-slot:activator="{ on }">
              <v-btn icon v-on="on">
                <v-icon v-text="'mdi-plus'" />
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="addCustomProductDialog()">
                <v-list-item-icon>
                  <v-icon v-text="'mdi-flower'" />
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="'Custom item'" />
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="giftcardDialog()">
                <v-list-item-icon>
                  <v-icon v-text="'mdi-wallet-giftcard'" />
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-text="'Giftcard'" />
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-row>

        <v-row align="center" justify="center">
          <v-col :cols="12" v-if="!categoriesLoading">
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
                  v-text="category.name"
                  tile
                />
              </v-slide-item>
            </v-slide-group>
          </v-col>
          <v-col v-else v-for="n in 4" :key="n" :cols="3">
            <v-skeleton-loader type="chip" tile />
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <div v-if="products.length">
      <perfect-scrollbar tag="v-container" style="height:600px;">
        <v-row>
          <v-col
            v-for="product in products"
            :key="product.id"
            :cols="12"
            :md="6"
            :lg="4"
          >
            <v-card
              :img="product.photo_url"
              @click="addProduct(product)"
              height="170px"
              dark
              :elevation="12"
            >
              <v-card-title class="pa-0 grey darken-2" @click.stop>
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
                      :disabled="!product.plantcare_pdf"
                      @click="mailPlantCareDialog(product)"
                    >
                      <v-icon class="pr-2">mdi-email-send</v-icon>
                      <h5>Send plant care pdf</h5>
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
                      {{ $price(product.price).toFormat() }}
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
        </v-row>
      </perfect-scrollbar>

      <v-container>
        <v-row justify="center">
          <v-col :cols="10">
            <v-pagination
              v-model="currentPage"
              @input="paginate"
              :length="lastPage"
              color="primary"
              :disabled="productLoading"
            ></v-pagination>
          </v-col>
        </v-row>
      </v-container>
    </div>
    <v-container v-else>
      <v-row v-if="productLoading" align="center" justify="center">
        <v-col v-for="n in 9" :cols="12" :md="6" :lg="4" :key="n">
          <v-card :elevation="12">
            <v-skeleton-loader type="card-heading" tile class="mx-auto" />
            <v-skeleton-loader
              type="image"
              tile
              class="mx-auto"
              height="125px"
            />
          </v-card>
        </v-col>
        <v-col :cols="12">
          <v-skeleton-loader
            type="heading"
            tile
            class="mx-auto d-flex justify-center mt-2"
            max-width="600px"
            width="100%"
          />
        </v-col>
      </v-row>
      <v-row justify="center" v-else>
        <h2 v-text="'No products found'" />
      </v-row>
    </v-container>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  data() {
    return {
      categories: [],
      current_page: 1,
      last_page: null,
      viewId: null,
      productLoading: false,
      categoriesLoading: false,
      search_keyword: "",
      selected_category: null,
      btnactive: true
    };
  },

  created() {
    this.getAllProducts();
    this.getAllCategories();
  },

  mounted() {
    this.$root.$on("barcodeScan", sku => {
      if (!this.interactive_dialog.show) {
        this.getSingleProduct(sku);
      }
    });
  },

  computed: {
    ...mapState(["productList"]),
    ...mapState("dialog", ["interactive_dialog"]),

    keyword: {
      get() {
        return this.search_keyword;
      },
      set(value) {
        this.search_keyword = value;
        if (value) {
          this.currentPage = 1;
        }
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
    ...mapMutations(["setProductList"]),
    ...mapMutations("dialog", ["viewItem", "setDialog"]),
    ...mapActions("requests", ["request"]),
    ...mapActions("cart", ["addProduct"]),

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
    getAllCategories() {
      this.categoriesLoading = true;
      const payload = {
        method: "get",
        url: "product-listing/categories"
      };

      this.request(payload)
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
          console.log(error);
        })
        .finally(() => {
          this.categoriesLoading = false;
        });
    },

    getProductsFromCategoryID() {
      const payload = {
        method: "get",
        url: `categories/${this.selectedCategory}/products?page=${this.currentPage}`
      };

      this.getProductRequest(payload);
    },
    getSingleProduct(sku) {
      if (this.products.length === 1 && this.products[0].sku === sku) {
        if (this.btnactive) {
          this.addProduct(this.products[0]);
        } else {
          this.viewProductDialog(this.products[0]);
        }
      } else {
        this.keyword = sku;
        this.searchProduct().then(() => {
          if (_.isObjectLike(this.products[0])) {
            if (this.btnactive) {
              this.addProduct(this.products[0]);
            } else {
              this.viewProductDialog(this.products[0]);
            }
          }
        });
      }
    },
    searchProduct() {
      if (this.keyword.length > 0) {
        this.selected_category = null;
        const payload = {
          method: "post",
          url: `products/search?page=${this.currentPage}`,
          data: { keyword: this.keyword }
        };

        this.getProductRequest(payload);
      } else {
        this.getAllProducts();
      }
    },
    getAllProducts() {
      this.selected_category = null;
      const payload = {
        method: "get",
        url: `products?page=${this.current_page}`
      };
      this.getProductRequest(payload);
    },
    getProductRequest(payload) {
      return new Promise((resolve, reject) => {
        this.productLoading = true;
        this.products = [];

        this.request(payload)
          .then(response => {
            this.products = response.data;
            this.currentPage = response.current_page;
            this.lastPage = response.last_page;
            resolve(true);
          })
          .catch(error => {
            console.log(error);
            reject(error);
          })
          .finally(() => {
            this.productLoading = false;
          });
      });
    },
    viewProductDialog(product) {
      product.form = "product";
      this.viewItem(product);
    },
    mailPlantCareDialog(product) {
      const payload = {
        show: true,
        component: "MailPlantCareDialog",
        title: "Send plant care",
        cancelBtnTxt: "Close",
        model: product,
        titleCloseBtn: true
      };
      this.setDialog(payload);
    },
    giftcardDialog() {
      const payload = {
        show: true,
        component: "giftCardToCartForm",
        title: "Add a gift card to cart",
        icon: "mdi-wallet-giftcard",
        titleCloseBtn: true,
        persistent: true
      };
      this.setDialog(payload);
    },
    addCustomProductDialog() {
      const payload = {
        show: true,
        component: "customProductForm",
        title: "Add custom item to cart",
        icon: "mdi-flower",
        titleCloseBtn: true,
        persistent: true
      };
      this.setDialog(payload);
    }
  }
};
</script>
