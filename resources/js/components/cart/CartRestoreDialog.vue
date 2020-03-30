<template>
  <v-container fluid>
    <v-data-iterator
      v-if="!cartsLoading"
      :items="carts"
      :items-per-page="4"
      hide-default-footer
      no-data-text="No carts found"
    >
      <template v-slot:default="{ items }">
        <v-row>
          <v-col
            :cols="12"
            :sm="6"
            :md="4"
            v-for="(item, index) in items"
            :key="item.id"
          >
            <v-card :disabled="isLoading">
              <v-card-title>
                <h6 v-text="`${1 + index}. ${item.created_at}`" />
              </v-card-title>
              <v-divider />
              <v-list>
                <v-list-item>
                  <v-list-item-content v-text="'Items:'" />
                  <v-list-item-content
                    class="align-end"
                    v-text="item.item_count"
                  />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Method:'" />
                  <v-list-item-content class="align-end" v-text="item.method" />
                </v-list-item>
                <v-list-item>
                  <v-list-item-content v-text="'Customer:'" />
                  <v-list-item-content
                    class="align-end"
                    v-text="item.customer"
                  />
                </v-list-item>
              </v-list>
              <v-card-actions class="justify-space-between">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      :loading="deleteLoading[index]"
                      icon
                      color="red"
                      @click.stop="
                        (deleteLoading[index] = true),
                          deleteCart(item.id, index)
                      "
                    >
                      <v-icon v-text="'mdi-delete-outline'" v-on="on" />
                    </v-btn>
                  </template>
                  <span v-text="'Delete'" />
                </v-tooltip>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      text
                      icon
                      color="primary"
                      :loading="restoreLoading[index]"
                      @click.stop="
                        (restoreLoading[index] = true), loadCart(item.id, index)
                      "
                    >
                      <v-icon v-on="on" v-text="'mdi-cart-arrow-down'" />
                    </v-btn>
                  </template>
                  <span v-text="'Restore'" />
                </v-tooltip>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </template>
      <template v-slot:footer>
        <v-row class="mt-2" align="center" justify="end">
          <v-pagination
            v-model="currentPage"
            :length="lastPage"
            @input="getCarts()"
            :total-visible="5"
          />
        </v-row>
      </template>
    </v-data-iterator>

    <v-row v-else>
      <v-col v-for="i in 3" :key="i" :cols="12" :sm="6" :md="4">
        <v-skeleton-loader type="card-heading" tile class="mx-auto" />
        <v-divider />
        <v-skeleton-loader type="image" tile class="mx-auto" />
        <div class="d-flex justify-space-between align-center mt-2">
          <v-skeleton-loader type="button" tile class="" width="40px" />
          <v-skeleton-loader type="button" tile class="" width="40px" />
        </div>
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
  </v-container>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  data() {
    return {
      deleteLoading: [],
      restoreLoading: [],
      cartsLoading: false,
      carts: [],
      currentPage: 1,
      lastPage: null,
      cartReplacePrompt: false
    };
  },

  mounted() {
    this.getCarts();
  },

  computed: {
    ...mapState("cart", ["cart_products"]),

    isLoading() {
      const deleteLoading = this.deleteLoading.forEach(item => {
        if (item) {
          return true;
        }
      });

      const restoreLoading = this.restoreLoading.forEach(item => {
        if (item) {
          return true;
        }
      });

      console.log(deleteLoading);

      if (this.cartLoading || deleteLoading || restoreLoading) {
        return true;
      } else {
        return false;
      }
    }
  },

  methods: {
    ...mapActions("requests", ["request"]),

    confirmation(event) {
      if (event) {
        this.loadCart();
      }

      this.cartReplacePrompt = false;
    },
    getCarts() {
      this.cartsLoading = true;
      const payload = {
        method: "get",
        url: `carts?page=${this.currentPage}`
      };
      this.request(payload)
        .then(response => {
          this.currentPage = response.current_page;
          this.lastPage = response.last_page;
          this.carts = response.data;
        })
        .catch(error => {
          console.error(error);
        })
        .finally(() => {
          this.cartsLoading = false;
        });
    },
    restoreCart(cart, index) {
      this.restoreLoading[index] = true;
      if (_.size(this.cart_products)) {
        this.cartReplacePrompt = true;
      } else {
        this.loadCart();
      }
    },
    loadCart() {
      this.deleteCart();
      this.close();
    },
    deleteCart(id, index) {
      this.deleteLoading[index] = true;

      const payload = {
        method: "delete",
        url: `carts/${id}`
      };

      this.request(payload)
        .then(() => {
          this.getCarts();
        })
        .catch(error => {
          this.cartsLoading = false;
        })
        .finally(() => {
          this.deleteLoading[index] = false;
        });
    }
  }
};
</script>
