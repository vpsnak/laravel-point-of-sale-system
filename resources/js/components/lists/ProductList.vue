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
          <v-btn :disabled="disableFilters" @click="applyFilter('value')" text>
            <v-icon left small>fas fa-heart</v-icon>
            <span class="hidden-sm-and-down">Favourites</span>
          </v-btn>
          <v-btn :disabled="disableFilters" @click="applyFilter('value')" text>
            <v-icon left small>fas fa-sort-numeric-up</v-icon>
            <span class="hidden-sm-and-down">Best Sellers</span>
          </v-btn>
          <v-btn :disabled="disableFilters" text>
            <v-icon left small>fas fa-spa</v-icon>
            <span class="hidden-sm-and-down">Roses</span>
          </v-btn>
          <v-btn :disabled="disableFilters" @click="applyFilter('value')" text>
            <v-icon left small>fas fa-seedling</v-icon>
            <span class="hidden-sm-and-down">Succulents</span>
          </v-btn>
        </v-btn-toggle>
      </v-row>
      <v-row v-if="productList.length" align="center" style="height:58vh; overflow-y:auto;">
        <v-col v-for="product in productList" :key="product.id" cols="12" md="6" lg="4">
          <v-card height="300px" @click="addCartProduct(product)" :img="product.photo_url">
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
                  <v-list-item @click>
                    <v-icon class="pr-2">mdi-heart</v-icon>
                    <h5>Add to favorites</h5>
                  </v-list-item>
                  <v-list-item @click>
                    <v-icon class="pr-2">remove_red_eye</v-icon>
                    <h5>View</h5>
                  </v-list-item>
                  <v-list-item @click>
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