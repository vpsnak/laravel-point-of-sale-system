<template>
  <v-card class="fill-height pa-3" elevation="12">
    <v-container fluid>
      <v-row justify="space-between">
        <v-col cols="auto">
          <v-btn
            class="mx-1"
            @click="btnactive = !btnactive"
            :color="btnactive ? 'primary' : null"
          >
            <v-icon left v-text="'mdi-barcode-scan'" />
            <v-icon v-text="'mdi-chevron-right'" />
            <v-icon right v-text="btnactive ? 'mdi-cart' : 'mdi-eye'" />
          </v-btn>
        </v-col>

        <v-col :cols="8">
          <v-text-field
            v-model="keyword"
            :disabled="productLoading"
            dense
            class="mx-1"
            outlined
            solo
            placeholder="Search product"
            @keyup.enter="searchProduct()"
          >
            <template slot="append">
              <v-btn @click="searchProduct()" text>
                <v-icon v-text="'mdi-magnify'" />
              </v-btn>
            </template>
          </v-text-field>
        </v-col>

        <v-col cols="auto">
          <v-menu :nudge-width="200" offset-x>
            <template v-slot:activator="{ on }">
              <v-btn text v-on="on">
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
        </v-col>
      </v-row>

      <v-row align="center" justify="center">
        <v-col v-if="categories.length" :cols="12">
          <v-chip-group
            v-model="selectedCategory"
            @change="keyword = ''"
            active-class="primary--text"
          >
            <v-chip
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
              :disabled="productLoading"
              class="ma-1"
              v-text="category.name"
              label
            />
          </v-chip-group>
        </v-col>

        <v-col v-else-if="categoriesLoading" v-for="n in 6" :key="n" :cols="2">
          <v-skeleton-loader type="chip" tile />
        </v-col>

        <v-alert
          v-else
          type="info"
          border="left"
          colored-border
          :elevation="3"
          dense
          width="175px"
        >
          <span v-text="'No categories'" />
        </v-alert>
      </v-row>
    </v-container>

    <v-card-text v-if="products.length">
      <perfect-scrollbar tag="v-container" style="height: 550px;">
        <v-row>
          <v-col
            v-for="product in products"
            :key="product.id"
            :cols="12"
            :md="6"
            :lg="4"
          >
            <v-card
              @click="addProduct(product)"
              :elevation="12"
              :ripple="false"
            >
              <v-card-title class="pa-0 grey white--text darken-2" @click.stop>
                <h6 class="px-2" v-text="truncate(product.name)" />
                <v-spacer />
                <v-menu bottom left dark>
                  <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                      <v-icon color="white" v-text="'mdi-dots-vertical'" />
                    </v-btn>
                  </template>
                  <v-list dense>
                    <v-subheader v-text="'VIEW'" />
                    <v-list-item @click="viewProductDialog(product)">
                      <v-list-item-icon>
                        <v-icon v-text="'mdi-eye'" />
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>
                          <span v-text="'Product'" />
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>

                    <v-list-item
                      :href="product.url"
                      target="_blank"
                      link
                      :disabled="!product.url"
                    >
                      <v-list-item-icon>
                        <v-icon v-text="'mdi-alpha-m-circle-outline'" />
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>
                          <span v-text="'On Magento'" />
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>

                    <v-list-item
                      :href="product.plantcare_pdf"
                      target="_blank"
                      link
                      :disabled="!product.plantcare_pdf"
                    >
                      <v-list-item-icon>
                        <v-icon v-text="'mdi-flower-outline'" />
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>
                          <span v-text="'Plant care'" />
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>

                    <v-subheader v-text="'ACTIONS'" />
                    <v-list-item
                      :disabled="!product.plantcare_pdf"
                      @click="mailPlantCareDialog(product)"
                    >
                      <v-list-item-icon>
                        <v-icon v-text="'mdi-email-send'" />
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>
                          <span v-text="'Send plant care pdf'" />
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>

                    <v-list-item @click="printProductBarcode(product)">
                      <v-list-item-icon>
                        <v-icon v-text="'mdi-barcode'" />
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>
                          <span v-text="'Print barcode'" />
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-card-title>
              <v-card-text
                style="height: 100px; background: #f2f2f2;"
                class="d-flex pa-1"
              >
                <v-col :cols="5" class="pa-0 d-flex justify-center flex-column">
                  <v-chip label dark small class="elevation-6 secondary mb-1">
                    <span
                      v-text="`Price: ${parsePrice(product.price).toFormat()}`"
                    />
                  </v-chip>
                  <v-chip label small dark class="elevation-6 grey darken-1">
                    <span v-text="`SKU: ${product.sku}`" />
                  </v-chip>

                  <v-chip
                    v-if="stockColor(product)"
                    label
                    small
                    dark
                    class="elevation-6 my-1"
                    :color="stockColor(product)"
                  >
                    <span v-text="`Stock: ${product.stock}`" />
                  </v-chip>
                </v-col>
                <v-col
                  :cols="7"
                  class="pa-0 d-flex justify-center align-center mt-1"
                >
                  <v-img
                    v-if="product.photo_url"
                    :src="product.photo_url"
                    width="100%"
                    height="100%"
                    aspect-ratio="1"
                    contain
                    @error="product.photo_url = null"
                  />

                  <v-icon
                    v-else
                    color="black"
                    v-text="'mdi-image-off-outline'"
                    size="50"
                  />
                </v-col>
              </v-card-text>
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
            />
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-text v-else>
      <v-container style="height: 550px;">
        <v-row v-if="productLoading" align="center" justify="center">
          <v-col v-for="n in 9" :key="n" :cols="12" :md="6" :lg="4">
            <v-card :elevation="12" class="pa-2">
              <v-card-title
                class="pa-0 d-flex align-center"
                style="height: 36px;"
                @click.stop
              >
                <v-col :cols="11" class="pa-0">
                  <v-skeleton-loader
                    type="heading"
                    tile
                    height="24px"
                    width="100%"
                  />
                </v-col>
                <v-col :cols="1" class="pa-0">
                  <v-skeleton-loader
                    class="pl-1"
                    type="button"
                    tile
                    width="10px"
                    height="24px"
                  />
                </v-col>
              </v-card-title>
              <v-card-text style="height: 100px;" class="d-flex pa-1">
                <v-col :cols="5" class="pa-0 d-flex justify-center flex-column">
                  <v-skeleton-loader
                    v-for="n in 3"
                    :key="n"
                    type="chip"
                    tile
                    height="25px"
                    class="py-1"
                  />
                </v-col>
                <v-col :cols="7" class="pa-0 mt-1">
                  <v-skeleton-loader
                    type="card-avatar"
                    width="100%"
                    height="100%"
                  />
                </v-col>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
        <v-row v-else justify="center" align="center" class="fill-height">
          <v-alert
            type="info"
            border="left"
            colored-border
            :elevation="3"
            dense
            width="175px"
          >
            <span v-text="'No items'" />
          </v-alert>
        </v-row>
      </v-container>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
  data() {
    return {
      categories: [],
      currentPage: 1,
      lastPage: null,
      viewId: null,
      productLoading: false,
      categoriesLoading: false,
      search_keyword: "",
      selectedCategory: null,
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
    products: {
      get() {
        return this.productList;
      },
      set(value) {
        this.setProductList(value);
      }
    }
  },

  watch: {
    selectedCategory(value) {
      if (value) {
        this.getProductsFromCategoryID();
      } else {
        this.currentPage = 1;
        this.getAllProducts();
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
        url: "categories/product-listing"
      };

      this.request(payload)
        .then(response => {
          this.categories = response;
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
        persistent: true,
        width: 700
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
